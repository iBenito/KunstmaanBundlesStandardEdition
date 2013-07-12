<?php

// src/Zizoo/UserBundle/Form/Type/FacebookNewRegistrationType.php
namespace Zizoo\UserBundle\Form\Type\Facebook;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacebookLinkRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new FacebookLinkUserType(), array('label' => ' '));
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
