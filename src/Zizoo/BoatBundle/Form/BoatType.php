<?php

namespace Zizoo\BoatBundle\Form;
use Zizoo\BoatBundle\Form\Type\BoatTypeType;
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
            ->add('default_price')
            ->add('brand')
            ->add('model')
            ->add('length')
            ->add('cabins')
            ->add('bathrooms')
            ->add('nr_guests')
            ->add('bathrooms')
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
        return 'zizoo_boattype';
    }
}
