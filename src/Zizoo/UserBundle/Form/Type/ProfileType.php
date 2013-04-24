<?php
// src/Zizoo/UserBundle/Form/Type/ProfileType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', 'text', array('label' => 'zizoo_user.label.first_name', 'property_path' => 'firstName'));
        $builder->add('last_name', 'text', array('label' => 'zizoo_user.label.last_name', 'property_path' => 'lastName'));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\ProfileBundle\Entity\Profile',
                     'validation_groups' => 'registration'));
    }

    public function getName()
    {
        return 'profile';
    }
}
?>
