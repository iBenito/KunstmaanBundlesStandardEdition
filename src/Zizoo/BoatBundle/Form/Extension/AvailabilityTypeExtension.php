<?php
// src/Zizoo/BoatBundle/Form/Extension/BookBoatTypeExtension.php
namespace Zizoo\BoatBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AvailabilityTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'zizoo_boat_availability';
    }
    
    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('has_default_price', 'default_price'));
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
        $boat = $options['boat'];
        
        $view->vars['has_default_price'] = $boat->getHasDefaultPrice();
        $view->vars['default_price'] = $boat->getDefaultPrice();
        
        $availability = $form->getData();
        if ($availability!==null){
            $view->vars['overlap_requested_reservations']   = $availability->getOverlappingReservationRequests();
            $view->vars['overlap_external_reservations']    = $availability->getOverlappingExternalReservations();
        }
        
        
    }

}
?>
