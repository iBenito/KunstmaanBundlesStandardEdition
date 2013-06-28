<?php

namespace Zizoo\BoatBundle\Form\Type;
use Zizoo\BoatBundle\Form\EventListener\BoatSubscriber;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatImageType extends AbstractType
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', 'zizoo_media_collection', array( 'type' => 'zizoo_media',
                                                            'property_path' => 'image',
                                                            'label'         => 'Image',
                                                            'file_path'     => 'webPath',
                                                            'aspect_ratio'  => 1.48,
                                                            'crop_js'           => 'avatarCrop',
                                                            'delete_js'         => 'avatarDelete',
                                                            'dropzone'          => array(
                                                                'upload_url'        => 'ZizooBoatBundle_Boat_AddImage',
                                                                'upload_param_name' => 'boatFile',
                                                                'upload_error_js'   => 'boatUploadError',
                                                                'upload_success_js' => 'boatUploadSuccess',
                                                            ),
                                                            'allow_delete'      => true
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BoatBundle\Entity\Boat',
            'validation_groups'     => array('default'),
            'cascade_validation'    => true
        ));
    }

    public function getName()
    {
        return 'zizoo_boatbundle_imagetype';
    }
}
