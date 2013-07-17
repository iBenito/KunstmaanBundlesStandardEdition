<?php
// src/Zizoo/UserBundle/Form/Type/UserType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => array('value' => 'zizoo_user.label.username', 'class' => 'charter')));
        $builder->add('email', 'email', array('label' => array('value' => 'zizoo_user.label.email', 'class' => 'email')));
        $builder->add('password', 'repeated', array(
           'label'              => false,
           'first_name'         => 'password',
           'second_name'        => 'confirm',
           'type'               => 'password',
           'invalid_message'    => 'zizoo_user.error.password_mismatch',
           'first_options'      => array('label' => 'zizoo_user.label.password'),
           'second_options'     => array('label' => 'zizoo_user.label.password_repeat')
        ));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\UserBundle\Entity\User',
                                    'validation_groups' => 'registration'));
    }

    public function getName()
    {
        return 'user';
    }
}
?>
