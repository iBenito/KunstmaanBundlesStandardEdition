<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomFieldsType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted', 'label' => 'zizoo_user.label.terms'));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\BookingBundle\Form\Model\CustomFields');
    }
    
    public function getName()
    {
        return 'custom_fields';
    }
}

?>
