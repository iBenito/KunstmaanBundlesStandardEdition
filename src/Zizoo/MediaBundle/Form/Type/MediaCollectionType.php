<?php

namespace Zizoo\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaCollectionType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'cascade_validation'    => true,
                                        'aspect_ratio'          => 0));
    }

    
    public function getParent()
    {
        return 'collection';
    }
    
    public function getName()
    {
        return 'zizoo_media_collection';
    }
}
