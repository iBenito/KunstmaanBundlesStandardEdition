<?php
// src/Zizoo/AddressBundle/Form/Type/AddressType.php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', 'text')
            ->add('premise', 'text')
            ->add('postcode', 'text')
            ->add('locality', 'text')
            ->add('sub_locality', 'text')
            ->add('country', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual' => true
        ));
    }

    public function getName()
    {
        return 'address';
    }
}
?>