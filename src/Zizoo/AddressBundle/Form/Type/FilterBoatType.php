<?php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterBoatType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $minMaxBoatValues   = $em->getRepository('ZizooBoatBundle:Boat')->getMaxBoatValues();
        
        $builder->add('boat_type', 'entity', array(
                                                    'class'     => 'ZizooBoatBundle:BoatType',
                                                    'multiple'  => true,
                                                    'expanded'  => true,
                                                    'property'  => 'name',
                                                    'label'     => array(   'value' => 'Boat Type',
                                                                            'class' => 'filter')));
        
        $builder->add('length', 'zizoo_number_range', array(    'required'              => false,
                                                                'from_property_path'    => 'lengthFrom',
                                                                'to_property_path'      => 'lengthTo',
                                                                'min'                   => 1,
                                                                'max'                   => $minMaxBoatValues['max_length'],
                                                                'callback'              => $options['callback'],
                                                                'data_class'            => 'Zizoo\AddressBundle\Form\Model\FilterBoat'));

        $builder->add('cabins', 'zizoo_number_range', array(    'required'              => false,
                                                                'from_property_path'    => 'numCabinsFrom',
                                                                'to_property_path'      => 'numCabinsTo',
                                                                'min'                   => 1,
                                                                'max'                   => $minMaxBoatValues['max_cabins'],
                                                                'callback'              => $options['callback'],
                                                                'data_class'            => 'Zizoo\AddressBundle\Form\Model\FilterBoat'));
        
        $builder->add('price', 'zizoo_number_range', array(     'required'              => false,
                                                                'from_property_path'    => 'priceFrom',
                                                                'to_property_path'      => 'priceTo',
                                                                'min'                   => $minMaxBoatValues['min_lowest_price']?$minMaxBoatValues['min_lowest_price']:1,
                                                                'max'                   => $minMaxBoatValues['max_highest_price']?$minMaxBoatValues['max_highest_price']:10000,
                                                                'callback'              => $options['callback'],
                                                                'data_class'            => 'Zizoo\AddressBundle\Form\Model\FilterBoat'));

        $builder->add('equipment', 'entity', array(
                'class' => 'ZizooBoatBundle:Equipment',
                'multiple'  => true,
                'expanded' => true,
                'property' => 'name',
                'label'     => 'Equipment'));
        
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'        => 'Zizoo\AddressBundle\Form\Model\FilterBoat',
                                        'label'             => 'Filter',
                                        'callback'          => null,
                                        'csrf_protection'   => false));
    }

    public function getParent()
    {
        return 'form';
    }
    
    public function getName()
    {
        return 'zizoo_boat_filter';
    }
}
?>
