<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageToOwnerType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text', array('property_path' => 'subject', 'data' => 'Reservation Request'));
        $builder->add('message', 'textarea', array('property_path' => 'body'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\MessageToOwner',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getParent()->getData();
                if ($data->getPaymentMethod()->getID()=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
        ));
    }
    
    public function getName()
    {
        return 'message_to_owner';
    }
}

?>
