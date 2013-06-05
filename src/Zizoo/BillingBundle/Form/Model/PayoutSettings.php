<?php
namespace Zizoo\BillingBundle\Form\Model;

class PayoutSettings {
 
    protected $payoutMethod;
    protected $bankAccount;
    protected $payPal;
    
    public function setPayoutMethod($payoutMethod)
    {
        $this->payoutMethod = $payoutMethod;
        return $this;
    }
    
    public function getPayoutMethod()
    {
        return $this->payoutMethod;
    }
    
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }
    
    public function getBankAccount()
    {
        return $this->bankAccount;
    }
    
    public function setPayPal($payPal)
    {
        $this->payPal = $payPal;
        return $this;
    }
    
    public function getPayPal()
    {
        return $this->payPal;
    }
    
}
?>
