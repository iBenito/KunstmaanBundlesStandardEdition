<?php

namespace Zizoo\ProfileBundle\Form;

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
            ->add('phone')
            ->add('languages', 'entity', array(
                'class' => 'ZizooAddressBundle:Language',
                'multiple'  => true,
                'attr'  => array('title'=>'select'),
                'property' => 'name',))
            ->add('avatar', 'zizoo_media_collection', array(    'type'          => 'zizoo_media',
                                                                'property_path' => 'avatar',
                                                                'label'         => 'Avatar',
                                                                'file_path'     => 'webPath',
                                                                'allow_delete'  => true
                                                                ))
            ->add('avatar_file', 'file', array(     'required'      => false, 
                                                    'label'         => 'New',
                                                    'property_path' => 'avatarFile'))
            ->add('document_file', 'file', array(   'required'      => false, 
                                                    'label'         => 'New',
                                                    'property_path' => 'documentFile'));
        
        $profileSubscriber = $this->container->get('zizoo_profile.profile_subscriber');
        $builder->addEventSubscriber($profileSubscriber);
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
        return 'zizoo_profile';
    }
}
