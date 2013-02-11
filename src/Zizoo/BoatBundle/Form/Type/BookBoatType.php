<?php
namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Zizoo\BoatBundle\Form\EventListener\AddTermsFieldSubscriber;

class BookBoatType extends AbstractType
{
    protected $container;
    
    public function __construct() {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('reservation_from', 'date', array('required'      => false,
                                                        'by_reference'  => false,
                                                        'widget' => 'single_text',
                                                        'format' => 'dd/MM/yyyy'));
        
        $builder->add('reservation_to', 'date', array('required'      => false,
                                                        'by_reference'  => false,
                                                        'widget' => 'single_text',
                                                        'format' => 'dd/MM/yyyy'));
        
        $builder->add('num_guests', 'integer', array('required'      => false,
                                                        'by_reference'  => false));
  
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\BoatBundle\Form\Model\BookBoat',
                        'csrf_protection' => false,
                        'current'      => '-1');
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
