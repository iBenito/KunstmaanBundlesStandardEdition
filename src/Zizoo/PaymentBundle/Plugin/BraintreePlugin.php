<?php
namespace Zizoo\PaymentBundle\Plugin;

use Zizoo\UserBundle\Service\UserService;
use Zizoo\UserBundle\Entity\User;

use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\Plugin\Exception\InvalidPaymentInstructionException;
use JMS\Payment\CoreBundle\Plugin\Exception\BlockedException;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Plugin\ErrorBuilder;
use JMS\Payment\CoreBundle\Plugin\PluginInterface;

class BraintreePlugin extends AbstractPlugin
{
    protected $userService;
    protected $braintreeCustomer;
    
    public function __construct(UserService $userService, $braintree, $isDebug = false) {
        parent::__construct($isDebug);
        $this->userService      = $userService;
        
        // Include Braintree API
        require_once $braintree['path'].'/lib/Braintree.php';
        \Braintree_Configuration::environment($braintree['environment']);
        \Braintree_Configuration::merchantId($braintree['merchant_id']);
        \Braintree_Configuration::publicKey($braintree['public_key']);
        \Braintree_Configuration::privateKey($braintree['private_key']);
    }
    
    public function setBraintreeCustomer(User $user)
    {
        $this->braintreeCustomer = $this->userService->getPaymentUser($user);
    }
    
    private function checkBraintreeCustomer()
    {
        if (!$this->braintreeCustomer){
            // TODO: handle?
            throw new BlockedException('There was an error with the payment provider when creating a customer');
        }
    }
    
    private function getExtraData(ExtendedData $extendedData)
    {
        $extraData = array();
        $extendedDataArr = $extendedData->all();
        foreach ($extendedDataArr as $k => $v){
            foreach ($v as $x => $y){
                if ($y instanceof ExtendedData){
                    $extraData[$k] = $this->getExtraData($y);
                } else {
                    $extraData[$k] = $y;
                }
                break;
            }
        }
        return $extraData;
    }
    
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
        $this->checkBraintreeCustomer();
        
        $data = array(
            'customerId'    => $this->braintreeCustomer->id,
            'amount'        => $transaction->getRequestedAmount(),
            'options'       => array(
                'storeInVaultOnSuccess'             => true,
                'addBillingAddressToPaymentMethod'  => true
            )
        );
        // Merge extended data
        $data = array_merge($data, $this->getExtraData($transaction->getExtendedData()));
        
        // Attempt to make Braintree transaction
        try {
            $result = \Braintree_Transaction::sale($data);
        } catch (\Exception $e){
            throw new BlockedException('There was an error with the payment provider');
        }
        if ($result->success){
            $transaction->setProcessedAmount($result->transaction->amount);
            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
            $transaction->setReferenceNumber($result->transaction->id);
        } else {
            $transaction->setReferenceNumber($result->transaction->id);
            $transaction->setReasonCode($result->message);
            throw new InvalidPaymentInstructionException();
        }
            
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
        $this->checkBraintreeCustomer();
        
        try {
            $result = \Braintree_Transaction::submitForSettlement($transaction->getReferenceNumber());
        } catch (\Exception $e){
            throw new BlockedException('There was an error with the payment provider');
        }
        
        if ($result->success) {
            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
        } else {
            $errors = array();
            foreach ($result->errors as $error){
                $errors[] = $error;
            }
            //$transaction->setReasonCode(implode(';', $errors));
            //throw new InvalidPaymentInstructionException(implode(';', $errors));
            $transaction->setReasonCode($result->message);
            throw new InvalidPaymentInstructionException($result->message);
        }
        
    }
    
    /**
     * This method cancels a previously approved payment.
     *
     * @throws InvalidDataException if a partial amount is passed, but this is
     *                              not supported by the payment backend system
     * @param FinancialTransactionInterface $transaction
     * @param boolean $retry
     * @return void
     */
    function reverseApproval(FinancialTransactionInterface $transaction, $retry)
    {
        $this->checkBraintreeCustomer();
        
        try {
            $result = \Braintree_Transaction::void($transaction->getReferenceNumber());
        } catch (\Exception $e){
            throw new BlockedException('There was an error with the payment provider');
        }
            
        if ($result->success) {
            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
        } else {
            $errors = array();
            foreach ($result->errors as $error){
                $errors[] = $error;
            }
            //$transaction->setReasonCode(implode(';', $errors));
            //throw new InvalidPaymentInstructionException(implode(';', $errors));
            $transaction->setReasonCode($result->message);
            throw new InvalidPaymentInstructionException($result->message);
        }
            
        
    }
    
    /**
     * This method cancels a previously deposited amount.
     *
     * @throws InvalidDataException if a partial amount is passed, but this is
     *                              not supported by the payment backend system
     * @param FinancialTransactionInterface $transaction
     * @param boolean $retry
     * @return void
     */
    function reverseDeposit(FinancialTransactionInterface $transaction, $retry)
    {
        $this->checkBraintreeCustomer();
        
        try {
            $result = \Braintree_Transaction::refund($transaction->getReferenceNumber());
        } catch (\Exception $e){
            throw new BlockedException('There was an error with the payment provider');
        }
            
        if ($result->success) {
            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
        } else {
            $errors = array();
            foreach ($result->errors as $error){
                $errors[] = $error;
            }
            //$transaction->setReasonCode(implode(';', $errors));
            //throw new InvalidPaymentInstructionException(implode(';', $errors));
            $transaction->setReasonCode($result->message);
            throw new InvalidPaymentInstructionException($result->message);
        }
            
    }
    
    public function checkPaymentInstruction(PaymentInstructionInterface $instruction)
    {
        $errorBuilder = new ErrorBuilder();
        $data = $instruction->getExtendedData();

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
