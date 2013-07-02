<?php

namespace Zizoo\BillingBundle\Twig;

use Zizoo\BillingBundle\Entity\Payout;

class BillingExtension extends \Twig_Extension
{
    
    public function getFilters()
    {
        return array(
            'displayTotalAmount'      => new \Twig_Filter_Method($this, 'displayTotalAmount'),
        );
    }

    public function displayTotalAmount($payouts){
        $total = 0;
        foreach ($payouts as $payout){
            $total += $payout->getAmount();
        }
        return number_format($total, 2);
    }
   
    public function getName()
    {
        return 'billing_extension';
    }
}
?>
