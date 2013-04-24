<?php

// src/Zizoo/CharterBundle/Form/Type/CharterRegistrationType.php
namespace Zizoo\CharterBundle\Form\Type;

use Zizoo\CharterBundle\Form\Type\CharterType;
use Zizoo\UserBundle\Form\Type\RegistrationType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharterRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('charter', new CharterType(), array('label' => ' '));
        $builder->add('registration', new RegistrationType(), array('label' => ' '));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\CharterBundle\Form\Model\CharterRegistration',
                     'cascade_validation' => true));
    }
    
    public function getName()
    {
        return 'charter_registration';
    }
}

?>
