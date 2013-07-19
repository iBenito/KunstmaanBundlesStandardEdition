<?php
// src/Zizoo/UserBundle/Form/Type/UserNewEmailType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserNewEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'    => 'Zizoo\UserBundle\Form\Model\UserNewEmail',
                                        'label'         => 'Email',
                                        'validation_groups' => function(FormInterface $form) {
                                            $data = $form->getParent()->getData();
                                            if ($data->getPassword()!='' && $data->getNewEmail()!='') {
                                                return array('change_email');
                                            } else {
                                                return array('Default');
                                            }
                                        }));
    }
    
    public function getParent()
    {
        return 'email';
    }

    public function getName()
    {
        return 'zizoo_change_email';
    }
}
?>
