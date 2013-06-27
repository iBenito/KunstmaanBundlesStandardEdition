<?php

// src/Zizoo/CharterBundle/Form/Type/CharterRegistrationType.php
namespace Zizoo\CharterBundle\Form\Type;

use Zizoo\CharterBundle\Form\Type\CharterType;
use Zizoo\UserBundle\Form\Type\RegistrationType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\Container;

class CharterRegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('charter', 'zizoo_charter', array(  'label'             => ' ',
                                                            'map_show'          => $options['map_show'],
                                                            'map_update'        => $options['map_update'],
                                                            'map_drag'          => $options['map_drag']));
        $builder->add('registration', new RegistrationType(), array('label' => ' '));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\CharterBundle\Form\Model\CharterRegistration',
                                        'cascade_validation'    => true,
                                        'validation_groups'     => 'registration',
                                        'map_show'              => true,
                                        'map_update'            => false,
                                        'map_drag'              => false
                                        ));
    }
    
    public function getName()
    {
        return 'charter_registration';
    }
}

?>
