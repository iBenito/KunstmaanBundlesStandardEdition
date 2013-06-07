<?php
// src/Zizoo/UserBundle/Form/Type/InviteType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InviteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emails','collection', array(
                                                'type'          => 'zizoo_invite_single',
                                                'allow_add'     => true,
                                                'allow_delete'  => true, // should render default button, change text with widget_remove_btn
                                                'prototype'     => true,
                                                'options'   => array( // options for collection fields
                                                    'attr' => array('class' => 'input-large'),
                                                )
            ));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'validation_groups'     => 'invitation',
                                        'data_class'            => 'Zizoo\UserBundle\Form\Model\Invite',
                                        'bubble_errors'         => false,
                                        'cascade_validation'    => true));
    }

    public function getName()
    {
        return 'zizoo_invite';
    }
}

class InviteSingleType extends AbstractType 
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array()
            );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'validation_groups' => 'invitation',
                                        'data_class'        => 'Zizoo\UserBundle\Form\Model\InviteSingle',
                                        'bubble_errors'     => false));
    }
    
    public function getName()
    {
        return 'zizoo_invite_single';
    }
}
?>
