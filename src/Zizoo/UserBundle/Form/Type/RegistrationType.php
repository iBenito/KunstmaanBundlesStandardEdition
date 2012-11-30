<?php

// src/Zizoo/UserBundle/Form/Type/RegistrationType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType());
        $builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted'));
    }

    public function getName()
    {
        return 'zizoo_registration';
    }
}

?>
