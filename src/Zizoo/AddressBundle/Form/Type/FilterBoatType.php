<?php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterBoatType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        $builder->add('boat_type', 'zizoo_boat_type_selector', array('expanded'     => true,
                                                                        'multiple'  => true));
        
        $builder->add('length_from', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('length_to', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('num_cabins_from', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('num_cabins_to', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('price_from', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('price_to', 'hidden', array('required'      => false,
                                                        'by_reference'  => false));
        
        $builder->add('equipment', 'zizoo_equipment_selector', array('expanded'     => true,
                                                                        'multiple'  => true));
        
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\AddressBundle\Form\Model\FilterBoat',
                     'current'      => '-1');
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
