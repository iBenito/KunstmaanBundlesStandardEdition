<?php

// src/Zizoo/UserBundle/Form/Type/RegistrationType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profile', new ProfileType(), array('label' => false));
        $builder->add('user', new UserType(), array('label' => false));
        $builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted', 'label' => 'zizoo_user.label.terms'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\UserBundle\Form\Model\Registration',
                                        'cascade_validation'    => true));
    }
    
    public function getName()
    {
        return 'registration';
    }
}

?>
