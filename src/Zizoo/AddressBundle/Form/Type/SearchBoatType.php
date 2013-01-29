<?php
// src/Zizoo/UserBundle/Form/Type/UserType.php
namespace Zizoo\AddressBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchBoatType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em         = $this->container->get('doctrine.orm.entity_manager');
        $uniqueLocations = $em->getRepository('ZizooAddressBundle:BoatAddress')->getUniqueLocations();
     
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
        
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\AddressBundle\Form\Model\SearchBoat',
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
