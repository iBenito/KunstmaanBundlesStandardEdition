<?php

namespace Zizoo\ProfileBundle\Form;

use Zizoo\BaseBundle\Form\Type\MediaType;

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
            ->add('phone')
            ->add('languages', 'entity', array(
                'class' => 'ZizooAddressBundle:Language',
                'multiple'  => true,
                'attr'  => array('title'=>'select'),
                'property' => 'name',))
            ->add('file', 'file', array( 'required' => false))
            ->add('avatar', 'zizoo_media_collection', array(    'type'          => 'zizoo_media',
                                                                'label'         => 'Avatar',
                                                                'image_path'    => 'webPath',
                                                                'allow_delete'  => true
                                                                ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\ProfileBundle\Entity\Profile',
            'cascade_validation'    => true,
            'validation_groups'     => array('default')
        ));
    }

    public function getName()
    {
        return 'zizoo_profiletype';
    }
}
