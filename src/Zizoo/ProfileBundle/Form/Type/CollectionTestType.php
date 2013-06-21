<?php

namespace Zizoo\ProfileBundle\Form\Type;

use Zizoo\BaseBundle\Form\Type\MediaType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('avatar', 'zizoo_media_collection', array('image_path'  => 'webPath', 'type' => 'zizoo_media', 'max_media' => 1));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\ProfileBundle\Entity\CollectionTest'
        ));
    }
    

    public function getName()
    {
        return 'zizoo_collection_test';
    }
}
