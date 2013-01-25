<?php

namespace Zizoo\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZizooMessageBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSMessageBundle';
    }
}
