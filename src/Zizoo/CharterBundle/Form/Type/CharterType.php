<?php
// src/Zizoo/CharterBundle/Form/Type/CharterType.php
namespace Zizoo\CharterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('charter_name', 'text', array('label' => 'zizoo_charter.label.charter_name', 'property_path' => 'charterName'));
        $builder->add('charter_number', 'text', array('label' => 'zizoo_charter.label.charter_number', 'property_path' => 'charterNumber'));

    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class' => 'Zizoo\CharterBundle\Entity\Charter',
                                        'validation_groups' => 'registration'));
    }

    public function getName()
    {
        return 'charter';
    }
}
?>
