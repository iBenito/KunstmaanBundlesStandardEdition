<?php

namespace Zizoo\ProfileBundle\Form\Type;

use Zizoo\BaseBundle\Form\Type\MediaType;
use Zizoo\ProfileBundle\Form\EventListener\ProfileSubscriber;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => array('value' => 'zizoo_profile.label.first_name', 'class' => 'first_name')))
            ->add('lastName', 'text', array('label' => array('value' => 'zizoo_profile.label.last_name', 'class' => 'last_name')))
            ->add('about', 'textarea', array('label' => array('value' => 'zizoo_profile.label.about', 'class' => 'description')))
            ->add('phone', 'text', array(
                                            'attr'  => array('oninvalid'=>"setCustomValidity('Please enter a valid Phone Number')"),
                                            'label' => array('value' => 'zizoo_profile.label.phone', 'class' => 'phone')))
            ->add('languages', 'entity', array(
                'class' => 'ZizooAddressBundle:Language',
                'multiple'  => true,
                'attr'  => array('title'=>'select'),
                'property' => 'name',
                'label' => array('value' => 'zizoo_profile.label.languages', 'class' => 'languages')))
            ->add('avatar', 'zizoo_media_collection', array(    'type'              => 'zizoo_media',
                                                                'property_path'     => 'avatar',
                                                                'label'             => false,
                                                                'file_path'         => 'webPath',
                                                                'aspect_ratio'      => 1.48,
                                                                'crop_js'           => 'avatarCrop',
                                                                'delete_js'         => 'avatarDelete',
                                                                'dropzone'          => array(
                                                                    'upload_url'        => 'ZizooProfileBundle_Profile_AddAvatar',
                                                                    'upload_params'     => array(),
                                                                    'upload_param_name' => 'avatarFile',
                                                                    'upload_error_js'   => 'avatarUploadError',
                                                                    'upload_success_js' => 'avatarUploadSuccess',
                                                                ),
                                                                'allow_delete'      => true
                                                                ))
            ->add('profile_address', 'zizoo_address', array('label'             => false,
                                                            'property_path'     => 'address',
                                                            'validation_groups' => $options['validation_groups'],
                                                            'data_class'        => 'Zizoo\AddressBundle\Entity\ProfileAddress',
                                                            'map_show'          => $options['map_show'],
                                                            'map_update'        => $options['map_update'],
                                                            'map_drag'          => $options['map_drag']))
            ;
        
        $profileSubscriber = $this->container->get('zizoo_profile.profile_subscriber');
        $builder->addEventSubscriber($profileSubscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\ProfileBundle\Entity\Profile',
            'cascade_validation'    => true,
            'validation_groups'     => array('Default', 'avatars'),
            'map_show'              => true,
            'map_update'            => false,
            'map_drag'              => false
        ));
    }

    public function getName()
    {
        return 'zizoo_profile';
    }
}
