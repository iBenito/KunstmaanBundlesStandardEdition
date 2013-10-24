<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Service\AbstractBookingAgent;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\PluginController\Result;

class BraintreeBookingAgent extends AbstractBookingAgent
{
    
    public function createPaymentInstruction(Booking $booking, Payment $payment, $extendedData)
    {
        $payment        = $this->initializePaymentInstruction($booking, $payment, $extendedData);
        $instruction    = $payment->getPaymentInstruction();
        
        $this->plugin->setBraintreeCustomer($booking->getRenter());
        
        $this->em->flush();
        // Create JMS payment
        if (null === $instruction->getPendingTransaction()) {
            $jmsPayment = $this->ppc->createPayment($instruction->getId(), $instruction->getAmount() - $instruction->getDepositedAmount());
        } else {
            // Could this ever happen?
            $jmsPayment = $instruction->getPayment();
        }

        // Approve payment
        $result = $this->ppc->approve($jmsPayment->getId(), $jmsPayment->getTargetAmount());
        
        if (Result::STATUS_SUCCESS !== $result->getStatus()) {
            throw new \RuntimeException('Transaction was not successful: '.$result->getReasonCode());
        }

        return $payment;
    }
    
    public function processPayment(Payment $payment)
    {
        $instruction    = $payment->getPaymentInstruction();
        $jmsPayments = $instruction->getPayments();
        
        if ($jmsPayments->count()!=1) throw new \Exception('Too many or too few payments: one expected!');
        
        $this->plugin->setBraintreeCustomer($payment->getBooking()->getRenter());
        
        foreach ($jmsPayments as $jmsPayment){
            if ($jmsPayment->getState()==PaymentInterface::STATE_APPROVED){
                // Deposit payment
                $result = $this->ppc->deposit($jmsPayment->getId(), $jmsPayment->getTargetAmount());
                if ($result->getStatus()==Result::STATUS_SUCCESS){
                    $payment->setStatus(Payment::STATUS_SUCCESS);
                } else {
                    $payment->setStatus(Payment::STATUS_FAILED);
                }
                break;
            } 
        }
        
        return $payment;
    }
    
    public function reversePayment(Payment $payment)
    {
        $instruction    = $payment->getPaymentInstruction();
        $jmsPayments = $instruction->getPayments();
        
        if ($jmsPayments->count()!=1) throw new \Exception('Too many or too few payments: one expected!');
        
        $this->plugin->setBraintreeCustomer($payment->getBooking()->getRenter());
        
        foreach ($jmsPayments as $jmsPayment){
            if ($jmsPayment->getState()==PaymentInterface::STATE_APPROVED){
                $result = $this->ppc->reverseApproval($jmsPayment->getId(), $jmsPayment->getTargetAmount());
            } else if ($jmsPayment->getState()==PaymentInterface::STATE_DEPOSITED){
                $result = $this->ppc->reverseApproval($jmsPayment->getId(), $jmsPayment->getTargetAmount());
                $result = $this->ppc->reverseDeposit($jmsPayment->getId(), $jmsPayment->getTargetAmount());
            }
            if ($result->getStatus()==Result::STATUS_SUCCESS){
                $payment->setStatus(Payment::STATUS_SUCCESS);
            } else {
                $payment->setStatus(Payment::STATUS_FAILED);
            }
        }
        
        return $payment;
        
    }
    
}
?>
