<?php
// src/Zizoo/BaseBundle/Form/Extension/MediaCollectionTypeExtension.php
namespace Zizoo\BaseBundle\Form\Extension;

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
        $resolver->setOptional(array('image_path', 'max_media', 'aspect_ratio'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        
    }
    
    
    

}
?>
