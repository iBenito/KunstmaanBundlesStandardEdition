<?php

namespace Zizoo\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for a reply
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ReplyMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', 'textarea', array('label' => false));
            /**->add('message_type', 'entity', array(
                                                'class' => 'ZizooMessageBundle:MessageType',
                                                'property' => 'id',
                                            ));*/
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'reply',
        ));
    }

    public function getName()
    {
        return 'zizoo_message_reply_message';
    }
}
