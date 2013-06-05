<?php
// src/Zizoo/UserBundle/Form/Type/UserNewPasswordType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserNewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'repeated', array('first_name' => 'password',
                                                    'second_name' => 'confirm',
                                                    'type' => 'password',
                                                    'invalid_message' => 'zizoo_user.error.password_mismatch',
                                                    'first_options'  => array('label' => 'zizoo_user.label.new_password'),
                                                    'second_options' => array('label' => 'zizoo_user.label.new_password_repeat')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class' => 'Zizoo\UserBundle\Form\Model\UserNewPassword',
                                        'validation_groups' => function(FormInterface $form) {
                                            $data = $form->getParent()->getData();
                                            if ($data->getPassword()!='' && $form->getData()->getPassword()!='') {
                                                return array('change_password');
                                            } else {
                                                return array('default');
                                            }
                                        }));
    }

    public function getName()
    {
        return 'user_new_password';
    }
}
?>
