<?php
namespace Zizoo\BillingBundle\Form\Model;

class BankAccount {
 
    protected $accountOwner;
    protected $bankName;
    protected $country;
    protected $iban;
    protected $bic;
    
    public function setAccountOwner($accountOwner)
    {
        $this->accountOwner = $accountOwner;
        return $this;
    }
    
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }
    
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
        return $this;
    }
    
    public function getBankName()
    {
        return $this->bankName;
    }
    
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
    
    public function getCountry()
    {
        return $this->country;
    }
    
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
