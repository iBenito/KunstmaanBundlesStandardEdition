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
            ->add('avatar', 'zizoo_media_collection', array(    'type'              => 'zizoo_media',
                                                                'property_path'     => 'avatar',
                                                                'label'             => 'Avatar',
                                                                'file_path'         => 'webPath',
                                                                'aspect_ratio'      => 1.48,
                                                                'crop_js'           => 'avatarCrop',
                                                                'delete_js'         => 'avatarDelete',
                                                                'dropzone'          => array(
                                                                    'upload_url'        => 'ZizooProfileBundle_Profile_AddAvatar',
                                                                    'upload_param_name' => 'avatarFile',
                                                                    'upload_error_js'   => 'avatarUploadError',
                                                                    'upload_success_js' => 'avatarUploadSuccess',
                                                                ),
                                                                'allow_delete'      => true
                                                                ))
            ->add('profile_address', 'zizoo_address', array('label' => 'zizoo_charter.label.profile_address',
                    'property_path'     => 'address',
                    'validation_groups' => 'registration',
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
            'validation_groups'     => array('default'),
            'data_class'            => 'Zizoo\ProfileBundle\Entity\Profile',
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
