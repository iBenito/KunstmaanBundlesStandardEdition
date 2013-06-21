<?php

namespace Zizoo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array( 'property_path'  => 'id' ))
            ->add('order', 'text')
            ->add('x1', 'hidden', array( 'required'       => false))
            ->add('y1', 'hidden', array( 'required'       => false))
            ->add('x2', 'hidden', array( 'required'       => false))
            ->add('y2', 'hidden', array( 'required'       => false))
            ->add('w', 'hidden', array( 'required'       => false))
            ->add('h', 'hidden', array( 'required'       => false))
        ;
        
        
    }
    

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'cascade_validation'    => true, 
                                        'data_class'            => 'Zizoo\BaseBundle\Entity\Media',
                                        'aspect_ratio'          => 0,
                                        'allow_delete'          => false,
                                        'label'                 => null));
    }

    
    public function getName()
    {
        return 'zizoo_media';
    }
}
