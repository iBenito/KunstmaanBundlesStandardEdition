<?php

namespace Zizoo\CharterBundle\Form\Type;

use Zizoo\CharterBundle\Form\EventListener\AddDenyBookingSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for accepting a reservation request. Includes an "accept" message and optionally a "deny" message for any
 * other reservation requests that may be overlapping.
 *
 */
class AcceptBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddDenyBookingSubscriber())
            ->add('accept_message', 'textarea', array('property_path' => 'acceptMessage', 'label' => false, 'attr' => array('placeholder' => 'Accept message')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\CharterBundle\Form\Model\AcceptBooking',
            'cascade_validation'    => true,
        ));
    }

    public function getName()
    {
        return 'zizoo_booking_accept';
    }
}
