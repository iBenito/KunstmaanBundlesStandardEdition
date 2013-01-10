<?php
// src/Zizoo/UserBundle/Form/Type/FacebookUserType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacebookLinkUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => 'zizoo_user.label.username', 'read_only' => true));
        $builder->add('email', 'hidden', array());
        $builder->add('facebookUID', 'hidden', array());
        $builder->add('password', 'hidden', array());
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\UserBundle\Entity\User',
                     'validation_groups' => 'fb_link');
    }

    public function getName()
    {
        return 'facebooklinkuser';
    }
}
?>
