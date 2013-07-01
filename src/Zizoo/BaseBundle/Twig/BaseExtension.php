<?php

namespace Zizoo\BaseBundle\Twig;

class BaseExtension extends \Twig_Extension
{
    
    public function getFilters()
    {
        return array(
            'displayAmount'      => new \Twig_Filter_Method($this, 'displayAmount'),
        );
    }

    public function displayAmount($dummy=null, $amount){
        return number_format($amount, 2);
    }
   
    public function getName()
    {
        return 'base_extension';
    }
}
?>
