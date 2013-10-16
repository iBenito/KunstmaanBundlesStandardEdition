<?php
namespace Zizoo\PaymentBundle\Plugin;

use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Plugin\ErrorBuilder;
use JMS\Payment\CoreBundle\Plugin\PluginInterface;

class BraintreePlugin extends AbstractPlugin
{
    
    /**
     * This method executes an approve transaction.
     *
     * By an approval, funds are reserved but no actual money is transferred. A
     * subsequent deposit transaction must be performed to actually transfer the
     * money.
     *
     * A typical use case, would be Credit Card payments where funds are first
     * authorized.
     *
     * @param FinancialTransactionInterface $transaction
     * @param boolean $retry Whether this is a retry transaction
     * @return void
     */
    function approve(FinancialTransactionInterface $transaction, $retry)
    {
        $transaction->setProcessedAmount($transaction->getRequestedAmount());
        $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_PENDING);
        $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
    }
    
    
    /**
     * This method executes a deposit transaction (aka capture transaction).
     *
     * This method requires that the Payment has already been approved in
     * a prior transaction.
     *
     * A typical use case are Credit Card payments.
     *
     * @param FinancialTransactionInterface $transaction
     * @param boolean $retry
     * @return void
     */
    function deposit(FinancialTransactionInterface $transaction, $retry)
    {
        $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
        $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
    }
    
    
    public function checkPaymentInstruction(PaymentInstructionInterface $instruction)
    {
        $errorBuilder = new ErrorBuilder();
        $data = $instruction->getExtendedData();

//        if (!$data->get('holder')) {
//            $errorBuilder->addDataError('holder', 'form.error.required');
//        }
//        if (!$data->get('number')) {
//            $errorBuilder->addDataError('number', 'form.error.required');
//        }
//
//        if ($instruction->getAmount() > 10000) {
//            $errorBuilder->addGlobalError('form.error.credit_card_max_limit_exceeded');
//        }

        // more checks here ...

        if ($errorBuilder->hasErrors()) {
            throw $errorBuilder->getException();
        }
    }

    
    public function processes($method)
    {
        return 'credit_card' === $method;
    }
    
}
?>
