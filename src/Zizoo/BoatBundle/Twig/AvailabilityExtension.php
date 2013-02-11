<?php

namespace Zizoo\BoatBundle\Twig;


class AvailabilityExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'Availability_priceFormatted' => new \Twig_Filter_Method($this, 'priceFormatted'),
        );
    }
    
    public function priceFormatted($availability){
        return number_format($availability->getPrice(), 2);
    }
    
    public function getName()
    {
        return 'availability_extension';
    }
}
?>
