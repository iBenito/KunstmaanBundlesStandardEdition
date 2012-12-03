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
        $builder->add('user', new UserType(), array('label' => ' '));
        $builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\UserBundle\Form\Model\Registration',
                        'cascade_validation' => true);
    }
    
    public function getName()
    {
        return 'registration';
    }
}

?>
