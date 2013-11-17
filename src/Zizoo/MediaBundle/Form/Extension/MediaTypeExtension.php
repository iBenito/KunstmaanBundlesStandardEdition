<?php
// src/Zizoo/MediaBundle/Form/Extension/MediaTypeExtension.php
namespace Zizoo\MediaBundle\Form\Extension;

use Zizoo\MediaBundle\Form\Type\MediaCollectionType;

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
        $resolver->setOptional(array('file_path', 'aspect_ratio', 'dropzone', 'crop_js', 'delete_js'));
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
        
        $parentInnerType = $config->getType()->getInnerType();
        if ($parentInnerType instanceof MediaCollectionType){
            $view->vars['part_of_collection'] = true;
        } else {
            $view->vars['part_of_collection'] = false;
        }
        
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
        
        if (array_key_exists('dropzone', $parentOptions)){
            $view->vars['dropzone'] = $parentOptions['dropzone'];
        } else {
            $view->vars['dropzone'] = $options['dropzone'];
        }
        
        if (array_key_exists('crop_js', $parentOptions)){
            $view->vars['crop_js'] = $parentOptions['crop_js'];
        } else {
            $view->vars['crop_js'] = $options['crop_js'];
        }
        
        if (array_key_exists('delete_js', $parentOptions)){
            $view->vars['delete_js'] = $parentOptions['delete_js'];
        } else {
            $view->vars['delete_js'] = $options['delete_js'];
        }
        
        
        if (array_key_exists('file_path', $options)) {
            $media = $form->getData();

            if (null !== $media){
                $accessor = PropertyAccess::getPropertyAccessor();
                $fileUrl = $accessor->getValue($media, $options['file_path']);
                $view->vars['file_url'] = $fileUrl;
                $view->vars['version']  = $media->getUpdated()->format('Y_m_d_H_i_s');
                $view->vars['mime_type'] = $media->getMimeType();
                $view->vars['filename'] = $media->getOriginalFilename();
            } else {
                $view->vars['file_url'] = null;
                $view->vars['version']  = null;
                $view->vars['mime_type']  = null;
                $view->vars['filename'] = null;
            }

        } else {
            
            if (array_key_exists('file_path', $parentOptions)) {
                $media = $form->getData();

                if (null !== $media){
                    $accessor = PropertyAccess::getPropertyAccessor();
                    $fileUrl = $accessor->getValue($media, $parentOptions['file_path']);
                    $view->vars['file_url'] = $fileUrl;
                    $view->vars['version']  = $media->getUpdated()->format('Y_m_d_H_i_s');
                    $view->vars['mime_type'] = $media->getMimeType();
                    $view->vars['filename'] = $media->getOriginalFilename();
                } else {
                    $view->vars['file_url'] = null;
                    $view->vars['version']  = null;
                    $view->vars['mime_type']  = null;
                    $view->vars['filename'] = null;
                }

            } else {
                $view->vars['file_url'] = null;
                $view->vars['version']  = null;
                $view->vars['mime_type']  = null;
                $view->vars['filename'] = null;
            }
        }
    }

}
?>
