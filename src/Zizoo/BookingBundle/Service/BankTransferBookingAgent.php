<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Service\AbstractBookingAgent;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\PluginController\Result;

class BankTransferBookingAgent extends AbstractBookingAgent
{
    
    public function addPayment(Booking $booking, $amount, $extendedData)
    {
        $payment        = $this->initializePayment($booking, $amount, $extendedData);
        $instruction    = $payment->getPaymentInstruction();
        
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
        
        foreach ($jmsPayments as $jmsPayment){
            if ($jmsPayment->getState()==PaymentInterface::STATE_APPROVED){
                // Deposit payment
                $result = $this->ppc->deposit($jmsPayment->getId(), $jmsPayment->getTargetAmount());
                if ($result->getStatus()==Result::STATUS_PENDING){
                    $payment->setStatus(Payment::STATUS_SUCCESS);
                } else {
                    $payment->setStatus(Payment::STATUS_FAILED);
                }
                break;
            } else if ($jmsPayment->getState()==PaymentInterface::STATE_DEPOSITING){
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
        foreach ($jmsPayments as $jmsPayment){
            if ($jmsPayment->getState()==PaymentInterface::STATE_APPROVED){
                $result = $this->ppc->reverseDeposit($jmsPayment->getId(), $jmsPayment->getTargetAmount());
            } else if ($jmsPayment->getState()==PaymentInterface::STATE_DEPOSITED){
                $result = $this->ppc->reverseDeposit($jmsPayment->getId(), $jmsPayment->getTargetAmount());
                $result = $this->ppc->reverseApproval($jmsPayment->getId(), $jmsPayment->getTargetAmount());
            }
            
        }
        
        return $payment;
        
    }
    
    
    
}
?>
