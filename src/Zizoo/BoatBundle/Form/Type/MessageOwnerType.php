<?php
namespace Zizoo\BoatBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\Container;

class MessageOwnerType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('reservation_range', new ReservationRangeType(), array(   'required'      => false));
        
        $builder->add('num_guests', 'integer', array('required'         => false,
                                                        'by_reference'  => false,
                                                        'attr'          => array('autocomplete' => 'off', 'placeholder' => 'Guests')));
        
        $builder->add('message', 'textarea', array('required' => true));
        
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\BoatBundle\Form\Model\MessageOwner',
                                        'validation_groups'     => array('message_owner'),
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
        return 'zizoo_message_owner';
    }
}
?>
