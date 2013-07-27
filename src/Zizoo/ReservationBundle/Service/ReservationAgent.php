<?php
namespace Zizoo\ReservationBundle\Service;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Zizoo\AddressBundle\Entity\ReservationAddress;

use Zizoo\UserBundle\Entity\User;

class ReservationAgent {
    
    private $em;
    private $messenger;
    private $container;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
    }
    
    public function sendReservationMessage(User $user, Reservation $reservation){
        $composer = $this->container->get('zizoo_message.composer');

        $registerUser = $this->em->getRepository('ZizooUserBundle:User')->findOneByEmail($this->container->getParameter('email_register'));
        $threadTypeRepo = $this->em->getRepository('ZizooMessageBundle:ThreadType');
        if (!$registerUser) return false;
        
        $thread = $composer->newThread()
                            ->setSender($registerUser)
                            ->addRecipient($user)
                            ->setSubject('Your booking')
                            ->setBody('This is the booking message')
                            ->setThreadType($threadTypeRepo->findOneByName('Booking'));
        $message = $thread->getMessage();
        
        $sender = $this->container->get('fos_message.sender');
        $sender->send($message);
        
        $this->messenger->sendNotificationBookingEmail($user, $reservation);
        
        return $thread;
    }
    
    public function getReservation($boat, $fromOrig, $toOrig)
    {
        if (!$fromOrig || !$toOrig) return null;
        $from   = clone $fromOrig;
        $to     = clone $toOrig;
        $from->setTime(12,0,0);
        $to->setTime(11,59,59);
        $reservations = $boat->getReservation();
        foreach ($reservations as $reservation){
            if ($reservation->getStatus()!=Reservation::STATUS_ACCEPTED && $reservation->getStatus()!=Reservation::STATUS_SELF && $reservation->getStatus()!=Reservation::STATUS_HOLD) continue;
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            //(StartA <= EndB) and (EndA >= StartB)
            $inRange = ($from < $checkout) && ($to > $checkIn);
            if ($inRange) return $reservation;
        }
        return null;
    }
    
    public function getOtherReservationRequests(Reservation $reservation)
    {
        $reservationRequests = array();
        $reservations = $reservation->getBoat()->getReservation();
        foreach ($reservations as $otherReservation){
            if ($reservation->getStatus()!=Reservation::STATUS_REQUESTED || $otherReservation == $reservation) continue;
            $checkIn = $otherReservation->getCheckIn();
            $checkout = $otherReservation->getCheckOut();
            //(StartA <= EndB) and (EndA >= StartB)
            $inRange = ($reservation->getCheckIn() < $checkout) && ($reservation->getCheckOut() > $checkIn);
            if ($inRange) {
                $reservationRequests[] = $reservation;
            }
                
        }
        return $reservationRequests;
    }
        
    public function reservationExists($boat, $from, $to){
        return $this->getReservation($boat, $from, $to)!=null;
    }
    
    private function makeReservationWithStatus($boat, $from, $to, $numGuests, $cost, User $guest, $status, $hoursToRespond=null, $flush=false)
    {
        $from->setTime(12,0,0);
        $to->setTime(11,59,59);
        $reservation = new Reservation();
        $reservation->setCheckIn($from);
        $reservation->setCheckOut($to);
        $reservation->setNrGuests($numGuests);
        $reservation->setBoat($boat);
        $reservation->setGuest($guest);
        $reservation->setStatus($status);
        $reservation->setCost($cost);
        $reservation->setHoursToRespond($hoursToRespond);

        $reservationAddress = new ReservationAddress($boat);
        $reservation->setAddress($reservationAddress);
        $reservationAddress->setReservation($reservation);
        
        $this->em->persist($reservationAddress);
        $this->em->persist($reservation);
        if ($flush) {
            $this->em->flush();
        }

        return $reservation;
    }
    
    public function makeReservationForSelf(Boat $boat, $from, $to, $flush=false)
    {
        return $this->makeReservationWithStatus($boat, $from, $to, 0, 0, $boat->getCharter()->getAdminUser(), Reservation::STATUS_SELF, null, $flush);
    }
    
    public function makeReservation(Boat $boat, BookBoat $bookBoat, $cost, User $guest, $flush=false)
    {
        $reservationRange = $bookBoat->getReservationRange();
        $from = $reservationRange->getReservationFrom();
        $to   = $reservationRange->getReservationTo();     
        $setHoursToRespond = $this->container->getParameter('zizoo_reservation.reservation_request_response_hours');
        return $this->makeReservationWithStatus($boat, $from, $to, $bookBoat->getNumGuests(), $cost, $guest, Reservation::STATUS_REQUESTED, $setHoursToRespond, $flush);
        
    }
    
    public function removeReservationForSelf(Boat $boat, Reservation $reservation)
    {
        $this->em->remove($reservation->getAddress());
        $this->em->remove($reservation);
    }
        
    public function acceptReservation(Reservation $reservation, $flush)
    {
        if ($reservation->getStatus()!=Reservation::STATUS_REQUESTED){
            throw new InvalidReservationException('Unable to accept reservation');
        }
        
        $bankTransferPaymentMethod = $this->em->getRepository('ZizooBookingBundle:PaymentMethod')->findOneById('bank_transfer');
        $booking = $reservation->getBooking();
        if ($booking->getInitialPaymentMethod()==$bankTransferPaymentMethod){
            $reservation->setStatus(Reservation::STATUS_HOLD);
        } else {
            $reservation->setStatus(Reservation::STATUS_ACCEPTED);
        }
        $this->em->persist($reservation);
        
        if ($flush) $this->em->flush();
        // TODO: handle notification, either here or in a listener
    }
    
    public function denyReservation(Reservation $reservation, $flush)
    {
        if ($reservation->getStatus()!=Reservation::STATUS_REQUESTED){
            throw new InvalidReservationException('Unable to accept reservation');
        }
        
        $reservation->setStatus(Reservation::STATUS_DENIED);
        $this->em->persist($reservation);
        
        if ($flush) $this->em->flush();
        
        // TODO: handle notification, either here or in a listener
    }
    
    public function expireReservation(Reservation $reservation, $flush)
    {
        if ($reservation->getStatus()!=Reservation::STATUS_REQUESTED){
            throw new InvalidReservationException('Unable to expire reservation');
        }
        
        $reservation->setStatus(Reservation::STATUS_EXPIRED);
        $this->em->persist($reservation);
        if ($flush) $this->em->flush();
        // TODO: handle notification, either here or in a listener
    }
    
    public function getPrices($boat, $from, $to){
        if (!$from || !$to) return null;
        $prices = $this->em->getRepository('ZizooBoatBundle:Price')->getPrices($boat, $from, $to);
        return $prices;
    }   
      
    // TODO: CHECK FUNCTION!
    public function available(Boat $boat, $fromOrig, $toOrig)
    {
        if (!$fromOrig || !$toOrig) return false;
        $from   = clone $fromOrig;
        $to     = clone $toOrig;
        $from->setTime(12,0,0);
        $to->setTime(11,59,59);
        
        $totalSetPrice  = $this->em->getRepository('ZizooBoatBundle:Price')->getTotalSetPrice($boat, $from, $to);
        
        $interval = $from->diff($to);
        $numDays = $interval->days;
        
        if ($numDays > $totalSetPrice['num_days']){
            return $boat->getDefaultPrice()>0;
        } else {
            return true;
        }
    }
    
    public function getTotalPrice(Boat $boat, $from, $to, $includeCrew, $arrayFormat=false){
        //if (!$boat->getCrewOptional() && !$includeCrew) throw new InvalidReservationException('Boat must be booked with crew');
        
        if (!$from || !$to) return null;
        $from = $from->setTime(0,0,0);
        $to = $to->setTime(0,0,0);
        
        $totalSetPrice  = $this->em->getRepository('ZizooBoatBundle:Price')->getTotalSetPrice($boat, $from, $to);
        
        $interval = $from->diff($to);
        $numDays = $interval->days;
        
        if ($numDays > $totalSetPrice['num_days']){
            if ($boat->getHasDefaultPrice()){
                $diffDays = $numDays-$totalSetPrice['num_days'];
                $subtotal = $totalSetPrice['set_price'] + ($diffDays*$boat->getDefaultPrice());
                $crewPrice = $diffDays*$boat->getCrewPrice();
                $total = $subtotal;
                if ($includeCrew){
                    $total = $subtotal + $crewPrice;
                }
                if ($arrayFormat){
                    return array('subtotal' => $subtotal, 'crew_price' => $crewPrice, 'total' => $total);
                } else {
                    return $subtotal + $crewPrice;
                }
            } else {
                throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'));
            }
        } else {
            $subtotal = $totalSetPrice['set_price'];
            $crewPrice = $numDays*$boat->getCrewPrice();
            $total = $subtotal;
            if ($includeCrew){
                $total = $subtotal + $crewPrice;
            }
            $total = $subtotal + $crewPrice;
            if ($arrayFormat){
                return array('subtotal' => $subtotal, 'crew_price' => $crewPrice, 'total' => $total);
            } else {
                return $total;
            }
        }
    }
    
    public function hoursToRespond(Reservation $reservation)
    {
        $hoursToRespond = $reservation->getHoursToRespond();
        if (!$hoursToRespond) return false;
        
        if ($reservation->getStatus()!=Reservation::STATUS_REQUESTED) return false;
        
        $setHoursToRespond = $this->container->getParameter('zizoo_reservation.reservation_request_response_hours');
        
        $now = new \DateTime();
        
        $interval = $now->diff($reservation->getCreated());
        
        $hours = $interval->h;
        $hours = $hours + ($interval->d*24);
        
        return $setHoursToRespond - $hours;
    }
    
    
    public function statusToString($status)
    {
        switch ($status){
            case Reservation::STATUS_REQUESTED:
                return 'Requested';
                break;
            case Reservation::STATUS_ACCEPTED:
                return 'Accepted';
                break;
            case Reservation::STATUS_EXPIRED:
                return 'Expired';
                break;
            case Reservation::STATUS_DENIED:
                return 'Denied';
                break;
            case Reservation::STATUS_SELF:
                return 'Reserved';
                break;
            case Reservation::STATUS_HOLD:
                return 'Hold';
                break;
            default:
                return 'Unkown status';
                break;
        }
    }
    
}
?>
