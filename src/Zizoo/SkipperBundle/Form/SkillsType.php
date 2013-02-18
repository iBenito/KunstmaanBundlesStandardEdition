<?php

namespace Zizoo\SkipperBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('license')
            ->add('experience')
            ->add('created')
            ->add('updated')
            ->add('user')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\SkipperBundle\Entity\Skills'
        ));
    }

    public function getName()
    {
        return 'zizoo_skipperbundle_skillstype';
    }
}
