<?php

namespace Zizoo\BookingBundle\Twig;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;

use Symfony\Component\DependencyInjection\Container;

class PaymentExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'Payment_status'      => new \Twig_Filter_Method($this, 'status'),
        );
    }
    
    public function status(Payment $payment)
    {
        return $this->container->get('zizoo_booking_booking_agent')->PaymentStatusToString($payment);
    }
    
    public function getName()
    {
        return 'payment_extension';
    }
}
?>
