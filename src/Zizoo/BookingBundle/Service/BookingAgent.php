<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Reservation;
use Zizoo\BookingBundle\Exception\InvalidReservationException;

use Zizoo\MessageBundle\Service\Messenger;
use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\UserBundle\Entity\User;
use Zizoo\ProfileBundle\Entity\Profile;

use Doctrine\Common\Collections\ArrayCollection;



class BookingAgent {
    
    private $em;
    private $messenger;
    private $container;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
    }
    
    private function sendReservationMessage(User $user, Reservation $reservation){
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
    
    private function reservationExists($boat, $from, $to){
        $reservations = $boat->getReservation();
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            $inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
            if ($inRange) return true;
        }
        return false;
    }
    
    public function makeReservation($boat, $user, $from, $to, $price){
        $availabilities = $boat->getAvailability();
        foreach ($availabilities as $availability){
            if ($from >= $availability->getAvailableFrom() 
                    && $to <= $availability->getAvailableUntil())
            {
                if ($this->reservationExists($boat, $from, $to)){
                    throw new InvalidReservationException('Already booked');
                }
                if ($availability->getPrice()!=$price){
                    throw new InvalidReservationException('Price mismatch: '.$price.'!='.$availability->getPrice());
                }
                $reservation = new Reservation();
                $reservation->setCheckIn($from);
                $reservation->setCheckOut($to);
                
                $reservation->setBoat($boat);
                $reservation->setStatus('4');
                
                $this->em->persist($reservation);
                $this->em->flush();
                
                
                $this->sendReservationMessage($user, $reservation);
                return $reservation;
                break;
            }
        }
        throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'));
    }
    
}
?>
