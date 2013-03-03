<?php

namespace Zizoo\AddressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'entity', array(
                'class' => 'ZizooAddressBundle:Country',
                'property' => 'name',
                ))
            ->add('locality')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\AddressBundle\Entity\BoatAddress'
        ));
    }

    public function getName()
    {
        return 'zizoo_addressbundle_boataddresstype';
    }
}
