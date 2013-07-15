<?php

namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BaseBundle\Form\Type\NumberNullableType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatDetailsType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
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
                'label'     => 'Equipment'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Zizoo\BoatBundle\Entity\Boat',
            'validation_groups' => array('boat_details', 'boat_create')
        ));
    }

    public function getName()
    {
        return 'zizoo_boat_details';
    }
}
