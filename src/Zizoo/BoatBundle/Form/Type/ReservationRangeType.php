<?php
namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReservationRangeType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('reservation_from', 'date', array('required'      => $options['required'],
                                                        'by_reference'  => false,
                                                        'widget'        => 'single_text',
                                                        'format'        => 'dd/MM/yyyy',
                                                        'label'         => $options['from_label'],
                                                        'attr'          => array('autocomplete' => 'off', 'placeholder' => $options['from_placeholder'])));
        
        $builder->add('reservation_to', 'date', array('required'        => $options['required'],
                                                        'by_reference'  => false,
                                                        'widget'        => 'single_text',
                                                        'format'        => 'dd/MM/yyyy',
                                                        'label'         => $options['to_label'],
                                                        'attr'          => array('autocomplete' => 'off', 'placeholder' => $options['to_placeholder'])));
          
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'        => 'Zizoo\BoatBundle\Form\Model\ReservationRange',
                                        'error_bubbling'    => false,
                                        'from_label'        => false,
                                        'to_label'          => false,
                                        'from_placeholder'  => 'From',
                                        'to_placeholder'    => 'Until'
                                        ));
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
