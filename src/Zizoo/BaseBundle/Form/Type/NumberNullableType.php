<?php

namespace Zizoo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumberNullableType extends AbstractType
{
  
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            
        ));
    }
    
    public function getParent()
    {
        return 'number';
    }

    public function getName()
    {
        return 'zizoo_number_nullable';
    }
}
