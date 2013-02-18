<?php

namespace Zizoo\BoatBundle\Form;
use Zizoo\AddressBundle\Form\Type\AddressType;

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
            ->add('brand')
            ->add('model')
            ->add('length')
            ->add('cabins')
            ->add('bathrooms')
            ->add('nr_guests')
            ->add('bathrooms')
            ->add('boat_type', 'entity', array(
                'class' => 'ZizooBoatBundle:BoatType',
                'property' => 'name',))
            ->add('address',new AddressType())
//            ->add('address', 'entity', array(
//                'class' => 'ZizooAddressBundle:Country',
//                'property' => 'name',
//                ))
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
        return 'zizoo_boattype';
    }
}
