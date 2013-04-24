<?php
// src/Zizoo/UserBundle/Form/Type/InvitationType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email1', 'email', array('label' => 'zizoo_user.label.email'));
        $builder->add('email2', 'email', array('label' => 'zizoo_user.label.email'));
        $builder->add('email3', 'email', array('label' => 'zizoo_user.label.email'));
        $builder->add('email4', 'email', array('label' => 'zizoo_user.label.email'));
        $builder->add('email5', 'email', array('label' => 'zizoo_user.label.email'));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array('data_class' => 'Zizoo\UserBundle\Form\Model\Invitation',
                     'validation_groups' => 'invitation'));
    }

    public function getName()
    {
        return 'invitation';
    }
}
?>
