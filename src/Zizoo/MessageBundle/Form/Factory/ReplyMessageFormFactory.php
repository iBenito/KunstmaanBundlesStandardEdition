<?php

namespace Zizoo\MessageBundle\Form\Factory;

use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\FormFactory\AbstractMessageFormFactory;

/**
 * Instanciates message forms
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ReplyMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a reply message
     *
     * @param ThreadInterface the thread we answer to
     * @return Form
     */
    public function create(ThreadInterface $thread)
    {
        $message = $this->createModelInstance();
        $message->setThread($thread);

        return $this->formFactory->createNamed($this->formName, $this->formType, $message);
    }
}
