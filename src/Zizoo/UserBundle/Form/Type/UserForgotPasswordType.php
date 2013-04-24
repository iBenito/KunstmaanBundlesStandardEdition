<?php
// src/Zizoo/UserBundle/Form/Type/UserForgotPasswordType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_or_email', 'text', array('label' => 'zizoo_user.label.username_or_email'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('virtual' => true));
    }

    public function getName()
    {
        return 'user_forgot_password';
    }
}
?>
