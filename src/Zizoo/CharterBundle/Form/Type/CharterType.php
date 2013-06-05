<?php
// src/Zizoo/CharterBundle/Form/Type/CharterType.php
namespace Zizoo\CharterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('charter_name', 'text', array('label' => 'zizoo_charter.label.charter_name', 'property_path' => 'charterName'));
        //$builder->add('charter_number', 'text', array('label' => 'zizoo_charter.label.charter_number', 'property_path' => 'charterNumber'));
        $builder->add('charter_address', 'zizoo_address', array('label'             => 'zizoo_charter.label.charter_address', 
                                                                'property_path'     => 'address',
                                                                'validation_groups' => 'registration',
                                                                'data_class'        => 'Zizoo\AddressBundle\Entity\CharterAddress',
                                                                'map_show'          => $options['map_show'],
                                                                'map_update'        => $options['map_update'],
                                                                'map_drag'          => $options['map_drag']));
        $builder->add('charter_phone', 'text', array('label' => 'zizoo_charter.label.charter_phone', 'property_path' => 'phone'));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\CharterBundle\Entity\Charter',
                                        'cascade_validation'    => true,
                                        'validation_groups'     => 'registration',
                                        'map_show'              => true,
                                        'map_update'            => false,
                                        'map_drag'              => false));
    }

    public function getName()
    {
        return 'charter';
    }
}
?>
