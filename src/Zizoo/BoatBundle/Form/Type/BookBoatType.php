<?php
namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BoatBundle\Form\EventListener\BookBoatSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\Container;

class BookBoatType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $builder->add('reservation_range', new ReservationRangeType(), array(   'required'          => false,
                                                                                'validation_groups' => $options['validation_groups']));
        
        $builder->add('num_guests', 'integer', array('required'         => false,
                                                        'by_reference'  => false,
                                                        'attr'          => array('autocomplete' => 'off', 'placeholder' => 'Guests')));
        
        $subscriber = new BookBoatSubscriber($builder->getFormFactory(), $this->container);
        $builder->addEventSubscriber($subscriber);
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\BoatBundle\Form\Model\BookBoat',
                                        'cascade_validation'    => true,
                                        'validation_groups'    => array('book'),
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
