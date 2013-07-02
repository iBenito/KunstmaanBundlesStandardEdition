<?php
// src/Zizoo/AddressBundle/Form/Type/AddressType.php
namespace Zizoo\AddressBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address_line_1', 'text', array('label' => array( 'value' => 'zizoo_address.label.address_line1',
                                                                    'class' => 'location')))
            ->add('address_line_2', 'text', array(  'label' => array( 'value' => 'zizoo_address.label.address_line2',
                                                                        'class' => 'location'),
                                                    'required'  => false))
            ->add('postcode', 'text', array('label' => array( 'value' => 'zizoo_address.label.postcode',
                                                                'class' => 'location')))
            ->add('locality', 'text', array('label' => array( 'value' => 'zizoo_address.label.locality',
                                                                    'class' => 'location')))
            ->add('sub_locality', 'text', array('required' => false,
                                                'label'     => array(   'value' => 'zizoo_address.label.sub_locality',
                                                                        'class' => 'location')))
            ->add('country', 'entity', array(
                                            'class' => 'ZizooAddressBundle:Country',
                                            'query_builder' => function(EntityRepository $er) {
                                                return $er->createQueryBuilder('c')
                                                    ->orderBy('c.order, c.printableName', 'ASC');
                                            },
                                            'label'     => array(   'value' => 'zizoo_address.label.country',
                                                                    'class' => 'location')
                                        ))
            ->add('lat', 'hidden', array(   'required'  => false,
                                            'read_only'  => true))
            ->add('lng', 'hidden', array(   'required'  => false,
                                            'read_only'  => true));
                     
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /**$resolver->setDefaults(array(
            'virtual' => true
        ));*/
        $resolver->setDefaults(array(   'cascade_validation'    => true,
                                        'map_show'              => true,
                                        'map_update'            => false,
                                        'map_drag'              => false));
        
    }

    public function getName()
    {
        return 'zizoo_address';
    }
}
?>