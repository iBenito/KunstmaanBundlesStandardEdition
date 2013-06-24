<?php

namespace Zizoo\ProfileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('about')
            ->add('phone', NULL, array(
                'attr'  => array('oninvalid'=>"setCustomValidity('Please enter a valid Phone Number')"),))
            ->add('languages', 'entity', array(
                'class' => 'ZizooAddressBundle:Language',
                'multiple'  => true,
                'attr'  => array('title'=>'select'),
                'property' => 'name',))
            ->add('file', 'file',array('label' => 'Avatar','required' => false))
            ->add('profile_address', 'zizoo_address', array('label' => 'zizoo_profile.label.profile_address',
                    'property_path'     => 'address',
                    'validation_groups' => 'registration',
                    'data_class'        => 'Zizoo\AddressBundle\Entity\ProfileAddress',
                    'map_show'          => $options['map_show'],
                    'map_update'        => $options['map_update'],
                    'map_drag'          => $options['map_drag']));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\ProfileBundle\Entity\Profile',
            'map_show'              => true,
            'map_update'            => false,
            'map_drag'              => false
        ));
    }

    public function getName()
    {
        return 'zizoo_profiletype';
    }
}
