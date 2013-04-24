<?php

namespace Zizoo\BaseBundle\Form\Type;

use Zizoo\ReservationBundle\Form\EventListener\AddDenyReservationSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for confirming boat price. Optionally includes a "deny" message for any
 * other reservation requests that may be overlapping.
 *
 */
class ConfirmBoatPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddDenyReservationSubscriber());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BaseBundle\Form\Model\ConfirmBoatPrice',
            'cascade_validation'    => true,
        ));
    }

    public function getName()
    {
        return 'zizoo_boat_confirm_price';
    }
}
