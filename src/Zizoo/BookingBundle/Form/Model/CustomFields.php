<?php
// src/Zizoo/BookingBundle/Form/Model/CustomFields.php
namespace Zizoo\BookingBundle\Form\Model;

class CustomFields
{

    protected $termsAccepted;


    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (boolean)$termsAccepted;
    }
}
?>
