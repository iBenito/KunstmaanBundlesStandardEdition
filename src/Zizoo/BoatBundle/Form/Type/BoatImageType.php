<?php

namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', 'zizoo_media_collection', array( 'type' => 'zizoo_media',
                                                            'property_path' => 'image',
                                                            'label'         => 'Image',
                                                            'file_path'     => 'webPath',
                                                            'aspect_ratio'  => 1.48,
                                                            'crop_js'           => 'photoCrop',
                                                            'delete_js'         => 'photoDelete',
                                                            'dropzone'          => array(
                                                                'upload_url'        => 'ZizooBoatBundle_Boat_AddPhoto',
                                                                'upload_params'     => array('id' => $options['boat_id']),
                                                                'upload_param_name' => 'boatFile',
                                                                'upload_error_js'   => 'photoUploadError',
                                                                'upload_success_js' => 'photoUploadSuccess',
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
            'cascade_validation'    => true,
            'boat_id'               => 0
        ));
    }

    public function getName()
    {
        return 'zizoo_boat_image';
    }
}
