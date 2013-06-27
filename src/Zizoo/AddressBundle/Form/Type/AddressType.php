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
            ->add('address_line_1')
            ->add('address_line_2', 'text', array(  'label'     => 'Address Line 2 (optional)',
                                                    'required'  => false))
            ->add('postcode', 'text')
            ->add('locality', 'text')
            ->add('sub_locality', 'text', array('required' => false))
            ->add('country', 'entity', array(
                                            'class' => 'ZizooAddressBundle:Country',
                                            'query_builder' => function(EntityRepository $er) {
                                                return $er->createQueryBuilder('c')
                                                    ->orderBy('c.order, c.printableName', 'ASC');
                                            },
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