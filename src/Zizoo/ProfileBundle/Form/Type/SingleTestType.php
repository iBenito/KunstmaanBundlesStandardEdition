<?php

namespace Zizoo\ProfileBundle\Form\Type;

use Zizoo\BaseBundle\Form\Type\MediaType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SingleTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', 'zizoo_media', array('image_path'  => 'webPath'));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\ProfileBundle\Entity\SingleTest'
        ));
    }

    public function getName()
    {
        return 'zizoo_single_test';
    }
}
