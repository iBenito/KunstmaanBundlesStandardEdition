<?php

namespace Zizoo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumberNullableType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nullable', 'choice', array(  'property_path'     => $options['has_property_path'],
                                                    'expanded'          => true,
                                                    'multiple'          => false,
                                                    'choices'           => array(false => 'Off', true => 'On')));
        
        $builder->add('nullable_value', 'number', array('property_path' => $options['value_property_path']));
                
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual'               => true,
            'has_property_path'     => null,
            'value_property_path'   => null,
            'validation_groups'     => array('Default')
        ));
    }

    public function getName()
    {
        return 'zizoo_number_nullable';
    }
}
