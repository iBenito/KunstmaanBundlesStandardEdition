<?php

namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BoatBundle\Form\Type\BoatTypeType;
use Zizoo\BoatBundle\Form\Type\Crew\BoatCrewType;
use Zizoo\BaseBundle\Form\Type\NumberNullableType;
use Zizoo\AddressBundle\Form\BoatAddressType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'  => $options['required']))
            ->add('boat_type', 'entity', array(
                                                'class'     => 'ZizooBoatBundle:BoatType',
                                                'property'  => 'name',
                                                'required'  => $options['required']))
            ->add('default_price', 'zizoo_number_nullable', array('required'  => $options['required']))
            ->add('minimum_days', 'zizoo_number_nullable', array('required'  => $options['required']))
            ->add('brand', 'text', array('required'  => $options['required']))
            ->add('model', 'text', array('required'  => $options['required']))
            ->add('length', 'number', array('required'  => $options['required']))
            ->add('cabins', 'number', array('required'  => $options['required']))
            ->add('berths', 'number', array('required'  => $options['required']))
            ->add('bathrooms', 'number', array('required'  => $options['required']))
            ->add('toilets', 'number', array('required'  => $options['required']))
            ->add('nr_guests', 'number', array('required'  => $options['required']))
            ->add('crew', new BoatCrewType(), array('label'             => 'Crew',
                                                    'required'          => $options['required']))
            ->add('address', 'zizoo_address', array('label'             => false,
                                                    'property_path'     => 'address',
                                                    'validation_groups' => $options['validation_groups'],
                                                    'data_class'        => 'Zizoo\AddressBundle\Entity\BoatAddress',
                                                    'map_show'          => $options['map_show'],
                                                    'map_update'        => $options['map_update'],
                                                    'map_drag'          => $options['map_drag'],
                                                    'required'          => $options['required']))
            ->add('engine_type', 'entity', array(
                'class'     => 'ZizooBoatBundle:EngineType',
                'property'  => 'name',
                'required'  => $options['required']));        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BoatBundle\Entity\Boat',
            'cascade_validation'    => true,
            'validation_groups'     => array('boat_edit', 'boat_create'),
            'map_show'              => true,
            'map_update'            => false,
            'map_drag'              => false,
            'required'              => true,
        ));
    }

    public function getName()
    {
        return 'zizoo_boat';
    }
}
