<?php
// src/Zizoo/MediaBundle/Form/Extension/MediaCollectionTypeExtension.php
namespace Zizoo\MediaBundle\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaCollectionTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_media_collection';
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('file_path', 'max_media', 'aspect_ratio', 'dropzone', 'crop_js', 'delete_js'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('aspect_ratio', $options)) {
            $view->vars['aspect_ratio'] = $options['aspect_ratio'];
        }
        
        if (array_key_exists('dropzone', $options)) {
            $view->vars['dropzone'] = $options['dropzone'];
        }
        
        if (array_key_exists('crop_js', $options)) {
            $view->vars['crop_js'] = $options['crop_js'];
        }
        
        if (array_key_exists('delete_js', $options)) {
            $view->vars['delete_js'] = $options['delete_js'];
        }
        
    }
    
    
    

}
?>
