<?php

namespace Zizoo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumberRangeType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value_from', 'number',   array( 'property_path'  => $options['from_property_path'] ));
        $builder->add('value_to', 'number',     array( 'property_path'  => $options['to_property_path'] ));                
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual'               => true,
            'from_property_path'    => null,
            'to_property_path'      => null,
            'validation_groups'     => array('Default'),
            'min'                   => 1,
            'max'                   => 1000,
            'unit'                  => 'm',
            'options'               => null,
            'callback'              => null
        ));
    }

    public function getName()
    {
        return 'zizoo_number_range';
    }
}

