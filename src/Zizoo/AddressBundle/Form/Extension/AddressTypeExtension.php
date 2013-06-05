<?php
// src/Zizoo/AddressBundle/Form/Extension/AddressTypeExtension.php
namespace Zizoo\AddressBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_address';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('map_show', 'map_update', 'map_drag'));
    }

    /**
     * Pass the image url to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('map_show', $options)) {
            $view->vars['map_show'] = $options['map_show'];
        }
        if (array_key_exists('map_update', $options)) {
            $view->vars['map_update'] = $options['map_update'];
        }
        if (array_key_exists('map_drag', $options)) {
            $view->vars['map_drag'] = $options['map_drag'];
        }
    }

}
?>
