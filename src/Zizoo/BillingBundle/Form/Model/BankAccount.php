<?php
namespace Zizoo\BillingBundle\Form\Model;

class BankAccount {
 
    protected $iban;
    protected $bic;
    
    public function setIBAN($iban)
    {
        $this->iban = $iban;
        return $this;
    }
    
    public function getIBAN()
    {
        return $this->iban;
    }
    
    public function setBIC($bic)
    {
        $this->bic = $bic;
        return $this;
    }
    
    public function getBIC()
    {
        return $this->bic;
    }
    
}
?>
