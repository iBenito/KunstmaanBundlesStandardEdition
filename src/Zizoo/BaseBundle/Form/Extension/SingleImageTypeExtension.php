<?php
// src/Zizoo/BaseBundle/Form/Extension/SingleImageTypeExtension.php
namespace Zizoo\BaseBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;

class SingleImageTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'file';
    }
}
?>
