<?php
// src/Zizoo/BookingBundle/Form/Model/Booking.php
namespace Zizoo\BookingBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Booking
{
    
    protected $creditCard;
    protected $paymentMethod;
    protected $billingAddress;
    protected $customFields;
    protected $message;
    protected $instalmentOption;
    
    public function getCustomFields()
    {
        return $this->customFields;
    }
    
    public function setCustomFields($customFields)
    {
        $this->customFields = $customFields;
        return $this;
    }
    
    public function getCreditCard()
    {
        return $this->creditCard;
    }
    
    public function setCreditCard($creditCard)
    {
        $this->creditCard = $creditCard;
        return $this;
    }
    
    public function getBilling()
    {
        return $this->billingAddress;
    }
    
    public function setBilling($billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }
    
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
    
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    
    public function getMessageToOwner()
    {
        return $this->message;
    }
    
    public function setMessageToOwner($message)
    {
        $this->message = $message;
        return $this;
    }
    
    public function getInstalmentOption()
    {
        return $this->instalmentOption;
    }
    
    public function setInstalmentOption($instalmentOption)
    {
        $this->instalmentOption;
        return $this;
    }
}
?>