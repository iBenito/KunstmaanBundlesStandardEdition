<?php

namespace Zizoo\CrewBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'hidden')
            ->add('license')
            ->add('experience')
            ->add('skill_type', 'entity', array(
                'class' => 'ZizooCrewBundle:SkillType',
                'property' => 'name'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\CrewBundle\Entity\Skills'
        ));
    }

    public function getName()
    {
        return 'zizoo_crewbundle_skillstype';
    }
}
