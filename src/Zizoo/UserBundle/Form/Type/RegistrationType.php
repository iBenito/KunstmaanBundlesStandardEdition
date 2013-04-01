<?php

// src/Zizoo/UserBundle/Form/Type/RegistrationType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profile', new ProfileType(), array('label' => ' '));
        $builder->add('user', new UserType(), array('label' => ' '));
        $builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted', 'label' => 'zizoo_user.label.terms'));
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
