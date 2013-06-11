<?php
namespace Zizoo\BaseBundle\Util;

class ZizooMath
{

    const EPSILON = 0.00001;
    
    public static function floatcmp($a, $b)
    {
        return abs($a -$b) < ZizooMath::EPSILON;
    }
    

}
?>
