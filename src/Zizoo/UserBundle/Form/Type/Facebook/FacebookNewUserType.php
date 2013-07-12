<?php
// src/Zizoo/UserBundle/Form/Type/FacebookNewUserType.php
namespace Zizoo\UserBundle\Form\Type\Facebook;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacebookNewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => 'zizoo_user.label.username'));
        $builder->add('email', 'hidden', array());
        $builder->add('facebookUID', 'hidden', array());
        $builder->add('password', 'hidden', array());
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\UserBundle\Entity\User',
                     'validation_groups' => 'registration'));
    }

    public function getName()
    {
        return 'facebooknewuser';
    }
}
?>
