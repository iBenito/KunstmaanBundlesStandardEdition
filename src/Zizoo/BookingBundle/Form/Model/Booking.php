<?php
// src/Zizoo/BookingBundle/Form/Model/Booking.php
namespace Zizoo\BookingBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Booking
{
    
    protected $creditCard;
    protected $billingAddress;
    protected $customFields;
        
    public function getCustomFields()
    {
        return $this->customFields;
    }
    
    public function setCustomFields($customFields)
    {
        $this->customFields = $customFields;
    }
    
    public function getCreditCard()
    {
        return $this->creditCard;
    }
    
    public function setCreditCard($creditCard)
    {
        $this->creditCard = $creditCard;
    }
    
    public function getBilling()
    {
        return $this->billingAddress;
    }
    
    public function setBilling($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }
}
?>