<?php
// src/Zizoo/CharterBundle/Form/Type/CharterType.php
namespace Zizoo\CharterBundle\Form\Type;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CharterType extends AbstractType
{
    
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
       
        $charterSubscriber = $this->container->get('zizoo_charter.charter_subscriber');
        $builder->addEventSubscriber($charterSubscriber);
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\CharterBundle\Entity\Charter',
                                        'cascade_validation'    => true,
                                        'validation_groups'     => array('registration', 'logo', 'Default'),
                                        'map_show'              => true,
                                        'map_update'            => true,
                                        'map_drag'              => true));
    }

    public function getName()
    {
        return 'zizoo_charter';
    }
}
?>
