<?php

namespace Zizoo\CrewBundle\Form;

use Zizoo\CrewBundle\Form\DataTransformer\LicenseTypeTransformer;
use Zizoo\CrewBundle\Form\LicenseType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new LicenseTypeTransformer();
        $builder
            ->add('skill_type', 'entity', array(
                'class' => 'ZizooCrewBundle:SkillType',
                'property' => 'name'))
            ->add($builder->create('license', 'zizoo_media', array(
                'label' => false,
                'data_class' => 'Zizoo\CrewBundle\Entity\SkillLicense',
                'file_path'         => 'webPath',
                'aspect_ratio'      => 1.48,
                'crop_js'           => 'licenseCrop',
                'delete_js'         => 'licenseDelete',
                'allow_delete'      => true,
                'dropzone'          => array(
                    'upload_url'        => 'ZizooCrewBundle_Skills_SetLicense',
                    'upload_params'     => array(),
                    'upload_params_cb'  => 'licensePreUpload',
                    'upload_param_name' => 'licenseFile',
                    'upload_error_js'   => 'licenseUploadError',
                    'upload_success_js' => 'licenseUploadSuccess',
                )))->addViewTransformer($transformer))
            ->add('experience', 'number', array('attr' => array('placeholder'=>'in years')))
            ->add('boats', 'entity', array(
                'class' => 'ZizooBoatBundle:BoatType',
                'multiple'  => true,
                'attr'  => array('title'=>'select', 'no-select-box-it'=>'no-select-box-it'),
                'property' => 'name',
                'property_path' => 'boatTypes'))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\CrewBundle\Entity\Skills',
            'validation_groups'     => array('license'),
            'cascade_validation'    => true
        ));
    }

    public function getName()
    {
        return 'zizoo_crewbundle_skillstype';
    }
}
