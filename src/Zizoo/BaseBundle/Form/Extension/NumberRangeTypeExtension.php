<?php
namespace Zizoo\BaseBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumberRangeTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_number_range';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('min', 'max', 'options', 'callback'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['min']          = $options['min'];
        $view->vars['max']          = $options['max'];
        $view->vars['options']      = $options['options'];
        $view->vars['callback']     = $options['callback'];
    }
    
    
    

}
?>
