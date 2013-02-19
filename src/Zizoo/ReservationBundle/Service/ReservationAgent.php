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
        
        $message = $composer->newThread()
                            ->setSender($registerUser)
                            ->addRecipient($user)
                            ->setSubject('Your booking')
                            ->setBody('This is the booking message')
                            ->setThreadType($threadTypeRepo->findOneByName('Booking'))
                            ->getMessage();
        
        $sender = $this->container->get('fos_message.sender');
        $sender->send($message);
        
        $this->messenger->sendNotificationBookingEmail($user, $reservation);
        
        return $message;
    }
    
    public function reservationExists($boat, $from, $to){
        if (!$from || !$to) return false;
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        $reservations = $boat->getReservation();
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            //(StartA <= EndB) and (EndA >= StartB)
            $inRange = ($from < $checkout) && ($to > $checkIn);
            //$inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
            if ($inRange) return true;
        }
        return false;
    }
    
    public function makeReservation(Boat $boat, BookBoat $bookBoat, $flush=false){
        $from = $bookBoat->getReservationFrom();
        $to   = $bookBoat->getReservationTo();
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        
        /**if ($bookBoat->getNumGuests() > $boat->getNrGuests()) throw new InvalidReservationException('Too many guests: '.$bookBoat->getNumGuests().'>'.$boat->getNrGuests());
        
        if ($this->reservationExists($boat, $from, $to)){
            throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'));
        }*/

        $reservation = new Reservation();
        $reservation->setCheckIn($from);
        $reservation->setCheckOut($to);
        $reservation->setNrGuests($bookBoat->getNumGuests());
        $reservation->setBoat($boat);
        $reservation->setStatus('4');

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
    
    public function getPrices($boat, $from, $to){
        if (!$from || !$to) return null;
        $prices = $this->em->getRepository('ZizooBoatBundle:Price')->getPrices($boat, $from, $to);
        return $prices;
    }   
    
    public function getTotalPrice(Boat $boat, $from, $to, $arrayFormat=false){
        if (!$from || !$to) return null;
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        
        $prices = $this->getPrices($boat, $from, $to);
        if (!$prices) return null;
        
        $arr = array();
        $totalPrice = 0;
        foreach ($prices as $price){
            if ($from >= $price->getAvailableFrom() && $to <= $price->getAvailableUntil()){
                $interval = $from->diff($to);
                $arr['price'][$price->getPrice()] = array('price' => $price->getPrice(), 'dates' => array($from, $to));
            } else if ($from >= $price->getAvailableFrom() && $from < $price->getAvailableUntil() && $to > $price->getAvailableUntil()){
                $interval = $from->diff($price->getAvailableUntil());
                $arr['price'][$price->getId()] = array('price' => $price->getPrice(), 'dates' => array($from, $price->getAvailableUntil()));
            } else if ($from < $price->getAvailableFrom() && $to >= $price->getAvailableFrom() && $to <= $price->getAvailableUntil()){
                $interval = $price->getAvailableFrom()->diff($to);
                $arr['price'][$price->getId()] = array('price' => $price->getPrice(), 'dates' => array($price->getAvailableFrom(), $to));
            } else {
                $interval = $price->getAvailableFrom()->diff($price->getAvailableUntil());
                $arr['price'][$price->getId()] = array('price' => $price->getPrice(), 'dates' => array($price->getAvailableFrom(), $price->getAvailableUntil()));
            }
            $totalPrice += $price->getPrice() * ($interval->days+1);
        }
        $arr['total_price'] = $totalPrice;
        if ($arrayFormat){
            return $arr;
        } else {
            return $totalPrice;
        }
    }
}
?>
