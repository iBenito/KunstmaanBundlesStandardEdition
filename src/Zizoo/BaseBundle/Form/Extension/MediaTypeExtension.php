<?php
// src/Zizoo/BaseBundle/Form/Extension/MediaTypeExtension.php
namespace Zizoo\BaseBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_media';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('image_path', 'aspect_ratio'));
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
        
        $parent         = $form->getParent();
        $config         = $parent->getConfig();
        $parentOptions = $config->getOptions();
        if (array_key_exists('aspect_ratio', $parentOptions)) {
            $view->vars['aspect_ratio'] = $parentOptions['aspect_ratio'];
        } else {
            $view->vars['aspect_ratio'] = $options['aspect_ratio'];
        }
        
        if (array_key_exists('allow_delete', $parentOptions)) {
            $view->vars['allow_delete'] = $parentOptions['allow_delete'];
        } else {
            $view->vars['allow_delete'] = $options['allow_delete'];
        }
                
        if (array_key_exists('image_path', $options)) {
            $media = $form->getData();

            if (null !== $media){
                $accessor = PropertyAccess::getPropertyAccessor();
                $imageUrl = $accessor->getValue($media, $options['image_path']);
                $view->vars['image_url']    = $imageUrl;
                $view->vars['version']      = $media->getUpdated()->format('Y_m_d_H_i_s');
            } else {
                $view->vars['image_url']    = null;
                $view->vars['version']      = null;
            }

        } else {
            
            if (array_key_exists('image_path', $parentOptions)) {
                $media = $form->getData();

                if (null !== $media){
                    $accessor = PropertyAccess::getPropertyAccessor();
                    $imageUrl = $accessor->getValue($media, $parentOptions['image_path']);
                    $view->vars['image_url']    = $imageUrl;
                    $view->vars['version']      = $media->getUpdated()->format('Y_m_d_H_i_s');
                } else {
                    $view->vars['image_url']    = null;
                    $view->vars['version']      = null;
                }

            } else {
                $view->vars['image_url']    = null;
                $view->vars['version']      = null;
            }
        }
    }

}
?>
