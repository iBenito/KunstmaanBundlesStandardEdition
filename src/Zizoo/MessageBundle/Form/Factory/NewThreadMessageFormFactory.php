<?php

namespace Zizoo\MessageBundle\Form\Factory;

use FOS\MessageBundle\FormFactory\AbstractMessageFormFactory;

/**
 * Instanciates message forms
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a new thread message
     *
     * @return Form
     */
    public function create($message)
    {
        //$message = $this->createModelInstance();

        return $this->formFactory->createNamed($this->formName, $this->formType, $message);
    }
}
