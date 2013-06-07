<?php

namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BoatBundle\Form\Type\BoatTypeType;
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
            ->add('title')
            ->add('name')
            ->add('description')
            ->add('boat_type', 'entity', array(
                'class' => 'ZizooBoatBundle:BoatType',
                'property' => 'name',))
            ->add('default_price', 'zizoo_number_nullable', array())
            ->add('minimum_days', 'zizoo_number_nullable', array())
            ->add('brand')
            ->add('model')
            ->add('length')
            ->add('cabins')
            ->add('bathrooms')
            ->add('nr_guests')
            ->add('bathrooms')
            ->add('crew_optional', 'choice', array( 'required'      => true,
                                                    'label'         => false,
                                                    'expanded'      => true,
                                                    'multiple'      => false,
                                                    'choices'       => array(false => 'Included', true => 'Optional'),
                                                    'property_path' => 'crewOptional'))
            ->add('num_crew', 'number', array('label'   => false))
            ->add('crew_price', 'number', array('label'   => false))
            ->add('address',new BoatAddressType())

        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BoatBundle\Entity\Boat'
        ));
    }

    public function getName()
    {
        return 'zizoo_boat';
    }
}
