<?php
// src/Zizoo/BoatBundle/Form/Extension/BoatImageTypeExtension.php
namespace Zizoo\BoatBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoatImageTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_boat_image';
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('boat_id'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('boat_id', $options)) {
            $view->vars['boat_id'] = $options['boat_id'];
        }
        
    }
    
    
    

}
?>
