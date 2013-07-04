<?php

namespace Zizoo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermsType extends AbstractType
{
  
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => array('class' => 'checkbox'),
        ));
    }
    
    public function getParent()
    {
        return 'checkbox';
    }

    public function getName()
    {
        return 'zizoo_terms';
    }
}
