<?php
// src/Zizoo/BaseBundle/Form/Type/AccountSettingsType.php
namespace Zizoo\UserBundle\Form\Type;

use Zizoo\UserBundle\Form\Type\UserNewPasswordType;
use Zizoo\UserBundle\Form\Type\UserNewEmailType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'password', array('label'         => array('value' => 'zizoo_user.label.current_password', 'class' => 'password'),
                                                    'property_path' => 'password'));
        
        $builder->add('new_email', new UserNewEmailType(), array(   'label'             => array('value' => 'zizoo_user.label.change_email',
                                                                                                'class' => 'email'),
                                                                    'property_path'     => 'newEmail'));
        
        $builder->add('new_password', new UserNewPasswordType(), array( 'label'         => false,
                                                                        'property_path' => 'newPassword'));
        
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\UserBundle\Form\Model\AccountSettings',
                                        'cascade_validation'    => true));
    }

    public function getName()
    {
        return 'account_settings';
    }
}
?>
