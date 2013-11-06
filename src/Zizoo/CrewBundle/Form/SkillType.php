<?php

namespace Zizoo\CrewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skills', 'collection', array(
                'label' => false,
                'type' => new SkillsType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Zizoo\UserBundle\Entity\User',
            'validation_groups'     => array('license'),
            'cascade_validation'    => true
        ));
    }

    public function getName()
    {
        return 'zizoo_crewbundle_skilltype';
    }
}
