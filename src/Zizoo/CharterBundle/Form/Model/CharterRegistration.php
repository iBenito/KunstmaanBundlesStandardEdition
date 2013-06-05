<?php
// src/Zizoo/CharterBundle/Form/Model/CharterRegistration.php
namespace Zizoo\CharterBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Zizoo\CharterBundle\Entity\Charter;

use Zizoo\UserBundle\Form\Model\Registration;

class CharterRegistration
{
    /**
     * @Assert\Type(type="Zizoo\CharterBundle\Entity\Charter")
     */
    protected $charter;
    
    /**
     * @Assert\Type(type="Zizoo\UserBundle\Form\Model\Registration")
     */
    protected $registration;
    
    public function setCharter(Charter $charter)
    {
        $this->charter = $charter;
    }
    
    public function getCharter()
    {
        return $this->charter;
    }
    
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function getRegistration()
    {
        return $this->registration;
    }
   
}
?>