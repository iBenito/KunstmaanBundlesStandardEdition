<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookingType extends AbstractType
{
    
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('credit_card', new CreditCardType(), array('label'    => 'Credit Card'));
        
        
        $builder->add('billing', new BillingAddressType($this->container->get('doctrine.orm.entity_manager')), array('label' => 'Billing Address'));
        
        //$builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted', 'label' => 'zizoo_user.label.terms'));
        $builder->add('custom_fields', new CustomFieldsType(), array('label' => ' '));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\BookingBundle\Form\Model\Booking',
                     'cascade_validation' => true);
    }
    
    public function getName()
    {
        return 'transaction';
    }
}

?>
