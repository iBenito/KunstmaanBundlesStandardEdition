<?php
// src/Zizoo/CharterBundle/Form/Type/CharterType.php
namespace Zizoo\CharterBundle\Form\Type;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharterType extends AbstractType
{
    
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('charter_name', 'text', array('label' => 'zizoo_charter.label.charter_name', 'property_path' => 'charterName'));
        //$builder->add('charter_number', 'text', array('label' => 'zizoo_charter.label.charter_number', 'property_path' => 'charterNumber'));
        $builder->add('charter_address', 'zizoo_address', array('label'             => false, 
                                                                'property_path'     => 'address',
                                                                'validation_groups' => 'registration',
                                                                'data_class'        => 'Zizoo\AddressBundle\Entity\CharterAddress',
                                                                'map_show'          => $options['map_show'],
                                                                'map_update'        => $options['map_update'],
                                                                'map_drag'          => $options['map_drag']));
        $builder->add('charter_phone', 'text', array('label' => 'zizoo_charter.label.charter_phone', 'property_path' => 'phone'));
        
        $charterSubscriber = $this->container->get('zizoo_charter.charter_subscriber');
        $builder->addEventSubscriber($charterSubscriber);
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\CharterBundle\Entity\Charter',
                                        'cascade_validation'    => true,
                                        'validation_groups'     => 'registration',
                                        'map_show'              => true,
                                        'map_update'            => true,
                                        'map_drag'              => true));
    }

    public function getName()
    {
        return 'zizoo_charter';
    }
}
?>
