<?php
// src/Zizoo/UserBundle/Form/Type/UserType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email');
        $builder->add('password', 'repeated', array(
           'first_name' => 'password',
           'second_name' => 'confirm',
           'type' => 'password'
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\UserBundle\Entity\User');
    }

    public function getName()
    {
        return 'zizoo_user';
    }
}
?>
