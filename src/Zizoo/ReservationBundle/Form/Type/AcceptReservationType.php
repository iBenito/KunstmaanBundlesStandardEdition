<?php

namespace Zizoo\ReservationBundle\Form\Type;

use Zizoo\ReservationBundle\Form\EventListener\AddDenyReservationSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for accepting a reservation request. Includes an "accept" message and optionally a "deny" message for any
 * other reservation requests that may be overlapping.
 *
 */
class AcceptReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddDenyReservationSubscriber())
            ->add('accept_message', 'textarea', array('property_path' => 'acceptMessage'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\ReservationBundle\Form\Model\AcceptReservation',
            'cascade_validation'    => true,
        ));
    }

    public function getName()
    {
        return 'zizoo_reservation_accept';
    }
}
