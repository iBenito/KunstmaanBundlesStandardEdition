<?php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;

class SearchBoatType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $uniqueLocations    = $em->getRepository('ZizooAddressBundle:BoatAddress')->getUniqueLocations();
     
        $builder->add('location', 'zizoo_unique_locations', array('choices'         => $uniqueLocations, 
                                                                    'current'       => $options['current'],
                                                                    'empty_value'   => 'All locations',
                                                                    'empty_data'    => '-1',
                                                                    'required'      => false,
                                                                    'by_reference'  => false));
        
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
        
        $builder->add('page', 'hidden', array('by_reference' => false));
        
        if ($options['filter']){
            $builder->add('filter', 'zizoo_boat_filter');
        }
                
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\AddressBundle\Form\Model\SearchBoat',
                     'filter'       => false,
                     'current'      => '-1');
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_boat_search';
    }
}
?>
