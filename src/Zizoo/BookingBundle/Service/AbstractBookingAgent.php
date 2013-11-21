<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Service\BookingAgentInterface;
use Zizoo\BookingBundle\Service\Exception\FunctionNotSupportedException;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\UserBundle\Entity\User;

use JMS\Payment\CoreBundle\Entity\PaymentInstruction;
use JMS\Payment\CoreBundle\Entity\Payment as JMSPayment;
use JMS\Payment\CoreBundle\PluginController\EntityPluginController;
use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;

use Doctrine\ORM\EntityManager;

abstract class AbstractBookingAgent implements BookingAgentInterface
{
    protected $em;
    protected $ppc;
    protected $plugin;
    protected $id;
    
    public function __construct(EntityManager $em, EntityPluginController $ppc)
    {
        $this->em = $em;
        $this->ppc = $ppc;
    }
    
    public function setPlugin(AbstractPlugin $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function bookingPaidInFull()
    {
        return true;
    }
    
    
    // Overriding methods
    
    function makeBooking(Reservation $reservation, User $user, $cost, $crew)
    {
        $reference = date("Ymd").$reservation->getId().'-ZIZOO';

        $booking = new Booking();
        $booking->setReference($reference);
        $booking->setCost($cost);
        $booking->setPayoutAmount($cost);
        $booking->setRenter($user);
        $booking->setInitialPaymentMethod($this->id);
        $booking->setCrew($crew);

        $reservation->setBooking($booking);
        $booking->setReservation($reservation);
        $this->em->persist($booking);
        $this->em->persist($reservation);
        //$this->em->flush();
        
        return $booking;
    }
    
    protected function initializePaymentInstruction(Booking $booking, Payment $payment, $extendedData=null)
    {
        $payment->setBooking($booking);
        $booking->addPayment($payment);
        
        // Create JMS PaymentInstruction and connect to Zizoo payment
        $instruction = new PaymentInstruction($payment->getAmount(), 'EUR', $this->id, $extendedData);
        $payment->setPaymentInstruction($instruction);
        $instruction->setState(PaymentInstruction::STATE_VALID);
        $this->em->persist($instruction);
        $this->em->persist($payment);
        
        return $payment;
    }
    
    public function createPaymentInstruction(Booking $booking, Payment $payment, $extendedData)
    {
        throw new FunctionNotSupportedException('createPaymentInstruction() is not supported by this plugin.');
    }


    function processPayment(Payment $payment)
    {
        throw new FunctionNotSupportedException('processPayment() is not supported by this plugin.');
    }
    
    function reversePayment(Payment $payment)
    {
        throw new FunctionNotSupportedException('reversePayment() is not supported by this plugin.');
    }
    
    function addPayment(Booking $booking, $amount, $data)
    {
        throw new FunctionNotSupportedException('addPayment() is not supported by this plugin.');
    }
    
    function processes($paymentMethod)
    {
        return $paymentMethod === $this->id;
    }
    
}
?>
