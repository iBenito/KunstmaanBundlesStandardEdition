<?php
// src/Zizoo/UserBundle/Form/Type/UserForgotPasswordType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class UserForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_or_email', 'text', array('label' => 'Username or email'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('virtual' => true);
    }

    public function getName()
    {
        return 'userforgotpassword';
    }
}
?>
