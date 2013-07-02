<?php

namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BaseBundle\Form\Type\NumberNullableType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatDetailsType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Zizoo\BoatBundle\Entity\Boat',
            'validation_groups' => array('boat_details', 'boat_new')
        ));
    }

    public function getName()
    {
        return 'zizoo_boat';
    }
}
