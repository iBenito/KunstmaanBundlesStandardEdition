<?php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterBoatType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        $builder->add('boat_type', 'entity', array(
                                                    'class'     => 'ZizooBoatBundle:BoatType',
                                                    'multiple'  => true,
                                                    'expanded'  => true,
                                                    'property'  => 'name',
                                                    'label'     => array(   'value' => 'Boat Type',
                                                                            'class' => 'filter')));
        
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
        
        $builder->add('equipment', 'entity', array(
                'class' => 'ZizooBoatBundle:Equipment',
                'multiple'  => true,
                'expanded' => true,
                'property' => 'name',
                'label'     => 'Equipment'));
        
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'   => 'Zizoo\AddressBundle\Form\Model\FilterBoat',
                     'current'      => '-1'));
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
