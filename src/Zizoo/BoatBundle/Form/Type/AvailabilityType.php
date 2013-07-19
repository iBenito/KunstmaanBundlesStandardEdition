<?php

namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AvailabilityType extends AbstractType
{
    
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reservation_range', new ReservationRangeType(), array(   'required'          => true, 
                                                                                'label'             => false,
                                                                                'from_placeholder'  => 'From dd/mm/yyyy',
                                                                                'to_placeholder'    => 'Until dd/mm/yyyy',
                                                                                'attr'              => array('readonly' => 'readonly')));
        
        $boat = $options['boat'];
        $choices = array(
                'availability'      => 'Available',
                'default'           => $boat->getHasDefaultPrice() ? 'Default ('.$boat->getDefaultPrice().' €)' : 'Default (unavailable)',
                'unavailability'    => 'Unavailable'
            );
        $builder->add('type', 'choice', array(
                                                'choices'   => $choices,
                                                'required'  => true,
                                                'expanded'  => true,
                                                'multiple'  => false,
                                                'label'     => false
        ));
        
        $builder->add('confirmed', 'hidden');
        
        $builder->add('price', 'number', array('label'      => false,
                                                'required'  => true,
                                                'attr'      => array('placeholder' => 'Price per day')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BoatBundle\Form\Model\Availability',
            'cascade_validation'    => true,
            'boat'                  => null
        ));
    }

    public function getName()
    {
        return 'zizoo_boat_availability';
    }
}
