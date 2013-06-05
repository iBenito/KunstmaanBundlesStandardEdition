<?php
namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReservationRangeType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('reservation_from', 'date', array('required'      => false,
                                                        'by_reference'  => false,
                                                        'widget'        => 'single_text',
                                                        'format'        => 'dd/MM/yyyy',
                                                        'attr'          => array('autocomplete' => 'off')));
        
        $builder->add('reservation_to', 'date', array('required'        => false,
                                                        'by_reference'  => false,
                                                        'widget'        => 'single_text',
                                                        'format'        => 'dd/MM/yyyy',
                                                        'attr'          => array('autocomplete' => 'off')));
          
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'        => 'Zizoo\BoatBundle\Form\Model\ReservationRange',
                                        'error_bubbling'    => false));
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_reservation_range';
    }
}
?>
