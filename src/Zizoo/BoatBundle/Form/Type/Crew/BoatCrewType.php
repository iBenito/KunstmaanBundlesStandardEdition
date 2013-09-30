<?php

namespace Zizoo\BoatBundle\Form\Type\Crew;

use Zizoo\BaseBundle\Form\Type\NumberNullableType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatCrewType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('crew_optional', 'choice', array( 'required'      => true,
                                                        'label'         => false,
                                                        'expanded'      => true,
                                                        'multiple'      => false,
                                                        'choices'       => array(false => 'Included', true => 'Optional'),
                                                        'property_path' => 'crewOptional'))
                
            ->add('num_crew', 'number', array(  'label'         => 'Number of crew members provided:',
                                                'required'      => true,
                                                'property_path' => 'numCrew'))
                
            ->add('crew_price', 'number', array('label'         => 'Total crew price per day (€):',
                                                'required'      => true,
                                                'property_path' => 'crewPrice'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual'           => true,
            'data_class'        => 'Zizoo\BoatBundle\Entity\Boat',
            'validation_groups' => array('Default')
        ));
    }

    public function getName()
    {
        return 'zizoo_boat_crew';
    }
}
