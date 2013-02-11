<?php
// src/Zizoo/BookingBundle/Form/Model/CreditCard.php
namespace Zizoo\BookingBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class CreditCard
{
    
    protected $cardHolder;
    protected $creditCardNumber;
    protected $expiryMonth;
    protected $expiryYear;
    protected $cvv;


    public function getCardHolder()
    {
        return $this->cardHolder;
    }
    
    public function setCardHolder($cardHolder)
    {
        $this->cardHolder = $cardHolder;
        return $this;
    }
    
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
    }
    
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = $creditCardNumber;
        return $this;
    }
    
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }
    
    public function setExpiryMonth($expiryMonth)
    {
        $this->expiryMonth = $expiryMonth;
        return $this;
    }
    
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }
    
    public function setExpiryYear($expiryYear)
    {
        $this->expiryYear = $expiryYear;
        return $this;
    }
    
    public function getCVV()
    {
        return $this->cvv;
    }
    
    public function setCVV($cvv)
    {
        $this->cvv = $cvv;
        return $this;
    }
}
?>