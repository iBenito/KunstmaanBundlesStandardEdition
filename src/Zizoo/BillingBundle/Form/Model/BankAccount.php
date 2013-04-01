<?php
namespace Zizoo\BillingBundle\Form\Model;

class BankAccount {
 
    protected $accountOwner;
    protected $bankName;
    protected $bankCountry;
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
    
    public function setBankCountry($bankCountry)
    {
        $this->bankCountry = $bankCountry;
        return $this;
    }
    
    public function getBankCountry()
    {
        return $this->bankCountry;
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
