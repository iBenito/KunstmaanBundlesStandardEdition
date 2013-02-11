<?php
// src/Zizoo/BookingBundle/Form/Model/CustomFields.php
namespace Zizoo\BookingBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class CustomFields
{
    /**
     * @Assert\True(message="You must accept the Terms & Conditions.")
     */
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
