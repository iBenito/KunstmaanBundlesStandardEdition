<?php
// src/Zizoo/UserBundle/Form/Type/UserNewPasswordType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class UserNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'repeated', array(
           'first_name' => 'password',
           'second_name' => 'confirm',
           'type' => 'password',
           'invalid_message' => 'The password fields must match.',
           'first_options'  => array('label' => 'New password'),
           'second_options' => array('label' => 'Repeat password')
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\UserBundle\Entity\User');
    }

    public function getName()
    {
        return 'usernewpassword';
    }
}
?>
