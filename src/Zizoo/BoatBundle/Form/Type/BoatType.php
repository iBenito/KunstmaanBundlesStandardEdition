<?php

namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BoatBundle\Form\Type\BoatTypeType;
use Zizoo\BoatBundle\Form\Type\Crew\BoatCrewType;
use Zizoo\BaseBundle\Form\Type\NumberNullableType;
use Zizoo\AddressBundle\Form\BoatAddressType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'  => $options['required']))
            ->add('boat_type', 'entity', array(
                                                'class'     => 'ZizooBoatBundle:BoatType',
                                                'property'  => 'name',
                                                'required'  => $options['required']))
            ->add('default_price_choice', 'zizoo_number_nullable', array(   'required'              => $options['required'],
                                                                            'has_property_path'     => 'hasDefaultPrice', 
                                                                            'value_property_path'   => 'defaultPrice',
                                                                            'data_class'            => $options['data_class'],
                                                                            'label'                 => array('value' => 'Default Price (€)',
                                                                                                             'help'  => 'Only if there is a constant price for all periods <br/> otherwise set prices per specific dates/periods in the "Price" field'),
                                                                            'attr'                  => array('placeholder'=>'per day')))
            ->add('minimum_days_choice', 'zizoo_number_nullable', array(    'required'              => $options['required'], 
                                                                            'has_property_path'     => 'hasMinimumDays', 
                                                                            'value_property_path'   => 'minimumDays',
                                                                            'data_class'            => $options['data_class'],
                                                                            'label'                 => 'Min. days'))
            ->add('year', 'text', array('required'  => $options['required']))
            ->add('brand', 'text', array('required'  => $options['required']))
            ->add('model', 'text', array('required'  => $options['required']))
            ->add('length', 'number', array('required'  => $options['required'], 'attr' => array('placeholder'=>'in metres')))
            ->add('hull_material', 'text', array('required'  => $options['required']))
            ->add('water_capacity', 'number', array('required'  => $options['required'], 'attr' => array('placeholder'=>'in litres')))
            ->add('cabins', 'number', array('required'  => $options['required'],'label' => '# of Cabins'))
            ->add('berths', 'number', array('required'  => $options['required'],'label' => '# of Berths'))
            ->add('bathrooms', 'number', array('required'  => $options['required'],'label' => '# of Bathrooms'))
            ->add('toilets', 'number', array('required'  => $options['required'],'label' => '# of Toilets'))
            ->add('showers', 'number', array('required'  => $options['required'],'label' => '# of Showers'))
            ->add('nr_guests', 'number', array('required'  => $options['required'],'label' => '# of Guests'))
            ->add('crew', new BoatCrewType(), array('required'  => $options['required'],
                                                    'label' => array('value' => 'Skipper',
                                                                     'help'  => 'Included: This boat can only be rented with the provided skipper</br>
                                                                                 Optional: This boat can be rented with or without the skipper</br>
                                                                                 </br>
                                                                                 If Optional is seleceted, comlete:</br>
                                                                                    - number of crew members provided</br>
                                                                                    - total crew price per day')))
            ->add('engine_type', 'entity', array(
                'class'     => 'ZizooBoatBundle:EngineType',
                'property'  => 'name',
                'required'  => $options['required']))
            ->add('fuel', 'text', array('required'  => $options['required'],'label' => 'Fuel Type'))
            ->add('fuel_capacity', 'number', array('required'  => $options['required']))
            ->add('address', 'zizoo_address', array('label'             => false,
                'property_path'     => 'address',
                'validation_groups' => $options['validation_groups'],
                'data_class'        => 'Zizoo\AddressBundle\Entity\BoatAddress',
                'map_show'          => $options['map_show'],
                'map_update'        => $options['map_update'],
                'map_drag'          => $options['map_drag'],
                'required'          => $options['required']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\BoatBundle\Entity\Boat',
            'cascade_validation'    => true,
            'validation_groups'     => array('boat_edit', 'boat_create'),
            'map_show'              => true,
            'map_update'            => false,
            'map_drag'              => false,
            'required'              => true,
        ));
    }

    public function getName()
    {
        return 'zizoo_boat';
    }
}
