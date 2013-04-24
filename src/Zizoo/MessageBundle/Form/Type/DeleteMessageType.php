<?php
// src/Zizoo/MessageBundle/Form/Type/DeleteMessageType.php
namespace Zizoo\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeleteMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confirm_delete', 'checkbox', array('label' => $options['confirm_delete_label']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        return array('data_class'           => 'Zizoo\MessageBundle\Entity\Message',
                     'validation_groups'    => 'delete_message',
                     'confirm_delete_label' => 'missing option \'confirm_delete_label\'');
    }

    public function getName()
    {
        return 'delete_message';
    }
}
?>
