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
            ->add('street')
            ->add('premise')
            ->add('postcode')
            ->add('locality')
            ->add('subLocality')
            ->add('state')
            ->add('province')
            ->add('extra1')
            ->add('extra2')
            ->add('lat')
            ->add('lng')
            ->add('country', 'entity', array(
                'class' => 'ZizooAddressBundle:Country',
                'property' => 'name',
                ))
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
