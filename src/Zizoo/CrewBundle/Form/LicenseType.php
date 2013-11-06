<?php

namespace Zizoo\CrewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array( 'property_path'  => 'id', 'attr' => array('class' => 'media_id') ))
        ;
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\CrewBundle\Entity\SkillLicense',
                                     'label'  => null));
    }

    
    public function getName()
    {
        return 'zizoo_license';
    }
}
