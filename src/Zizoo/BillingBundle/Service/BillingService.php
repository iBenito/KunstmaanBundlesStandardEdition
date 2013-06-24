<?php
namespace Zizoo\BillingBundle\Service;

class BillingService {

    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

}
?>
