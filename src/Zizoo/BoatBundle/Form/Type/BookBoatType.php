<?php
namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookBoatType extends AbstractType
{
    protected $container;
    
    public function __construct() {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('reservation_range', new ReservationRangeType(), array(   'required'      => false));
        
//        $builder->add('reservation_from', 'date', array('required'      => false,
//                                                        'by_reference'  => false,
//                                                        'widget'        => 'single_text',
//                                                        'format'        => 'dd/MM/yyyy',
//                                                        'attr'          => array('autocomplete' => 'off')));
//        
//        $builder->add('reservation_to', 'date', array('required'        => false,
//                                                        'by_reference'  => false,
//                                                        'widget'        => 'single_text',
//                                                        'format'        => 'dd/MM/yyyy',
//                                                        'attr'          => array('autocomplete' => 'off')));
        
        $builder->add('num_guests', 'integer', array('required'         => false,
                                                        'by_reference'  => false,
                                                        'attr'          => array('autocomplete' => 'off')));
  
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\BoatBundle\Form\Model\BookBoat',
                                        'cascade_validation'    => true,
                                        'csrf_protection'       => false,
                                        'current'               => '-1'));
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_boat_book';
    }
}
?>
