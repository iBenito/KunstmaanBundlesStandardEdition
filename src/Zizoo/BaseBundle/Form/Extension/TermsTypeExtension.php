<?php
namespace Zizoo\BaseBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermsTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_terms';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('terms_url', 'terms_text', 'terms_link'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['terms_url']    = $options['terms_url'];
        $view->vars['terms_text']   = $options['terms_text'];
        $view->vars['terms_link']   = $options['terms_link'];
    }
    
    
    

}
?>
