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
            ->add('name')
            ->add('boat_type', 'entity', array(
                'class' => 'ZizooBoatBundle:BoatType',
                'property' => 'name',))
            ->add('default_price', 'zizoo_number_nullable', array())
            ->add('minimum_days', 'zizoo_number_nullable', array())
            ->add('brand')
            ->add('model')
            ->add('length')
            ->add('cabins')
            ->add('berths')
            ->add('bathrooms')
            ->add('toilets')
            ->add('nr_guests')
            ->add('bathrooms')
            ->add('crew', new BoatCrewType(), array('label' => 'Crew'))
            ->add('address',new BoatAddressType())
            ->add('amenities', 'entity', array(
                'class'     => 'ZizooBoatBundle:Amenities',
                'multiple'  => true,
                'expanded'  => true,
                'property'  => 'name',
                'label'     => 'Amenities'))
            ->add('equipment', 'entity', array(
                'class'     => 'ZizooBoatBundle:Equipment',
                'multiple'  => true,
                'expanded'  => true,
                'property'  => 'name',
                'label'     => 'Equipment'))
            ->add('engine_type', 'entity', array(
                'class'     => 'ZizooBoatBundle:EngineType',
                'property'  => 'name',));
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BoatBundle\Entity\Boat',
            'cascasde_validation'   => true,
            'validations_groups'    => array('Default')
        ));
    }

    public function getName()
    {
        return 'zizoo_boat';
    }
}
