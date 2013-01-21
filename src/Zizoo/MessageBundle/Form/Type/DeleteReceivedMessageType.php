<?php
// src/Zizoo/MessageBundle/Form/Type/DeleteSentMessageType.php
namespace Zizoo\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteSentMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confirm_delete', 'checkbox', array('label' => 'zizoo_message.label.confirm_delete_sent_message'));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\MessageBundle\Entity\Message',
                     'validation_groups' => 'delete_message');
    }

    public function getName()
    {
        return 'deletesentmessage';
    }
}
?>
