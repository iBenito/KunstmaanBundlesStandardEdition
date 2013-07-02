<?php
namespace Zizoo\BillingBundle\Service;

use Zizoo\BillingBundle\Entity\Payout;
use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BookingBundle\Entity\Booking;

use Symfony\Component\DependencyInjection\Container;

class BillingService {

    private $container;

    public function __construct(Container $container)
            {
        $this->container = $container;
    }
    
    public function createPayout(Charter $charter, $flush=false)
    {
        try {
            $em = $this->container->get('doctrine.orm.entity_manager');

            $qb = $em->createQueryBuilder()->from('ZizooBookingBundle:Booking', 'booking')
                                            ->leftJoin('booking.reservation', 'reservation')
                                            ->leftJoin('booking.payment', 'payment')
                                            ->leftJoin('reservation.boat', 'boat')
                                            ->leftJoin('boat.charter', 'charter')
                                            ->select('booking')
                                            ->where('charter = :charter')
                                            ->andWhere('reservation.status = :reservation_status')
                                            ->andWhere('booking.status = :booking_status')
                                            ->andWhere('booking.payout IS NULL')
                                            ->setParameter('charter', $charter)
                                            ->setParameter('reservation_status', Reservation::STATUS_ACCEPTED)
                                            ->setParameter('booking_status', Booking::STATUS_PAID);

            $bookings = $qb->getQuery()->getResult();
           
            if (count($bookings)>0){
                $payout = new Payout();

                $userService    = $this->container->get('zizoo_user_user_service');
                //$bookingAgent   = $this->container->get('zizoo_booking_booking_agent');
                
                // Get braintree customer of charter billing user
                $billingUser = $charter->getBillingUser();
                $braintreeCustomer  = $userService->getPaymentUser($billingUser);

                if (!$braintreeCustomer){
                    throw new \Exception('Could not connect to Braintree to get charter payout information');
                }
                if (!is_array($braintreeCustomer->customFields)){
                    throw new \Exception('Charter payout information from Braintree does not contain payout method');
                }
                if (!array_key_exists('payout_method', $braintreeCustomer->customFields)){
                    throw new \Exception('Charter payout information from Braintree does not contain payout method');
                }

                // Set payment method and provider details
                $payoutMethodId = $braintreeCustomer->customFields['payout_method'];
                $payoutMethod = $em->getRepository('ZizooBillingBundle:PayoutMethod')->findOneById($payoutMethodId);
                if (!$payoutMethod){
                    throw new \Exception('Payout method specified by Braintree does not exist');
                }
                $payout->setPaymentMethod($payoutMethod);
                if ($payoutMethodId=='bank_transfer'){
                    $payout->setProvider(Payout::PROVIDER_BANK_TRANSFER);
                    $payout->setProviderId(null);
                    $payout->setProviderStatus(Payout::BANK_TRANSFER_INITIAL);
                } else if ($payoutMethodId=='paypal'){
                    $payout->setProvider(Payout::PROVIDER_PAYPAL);
                    $payout->setProviderId(null);
                    $payout->setProviderStatus(Payout::PAYPAL_INITIAL);
                } else {
                    throw new \Exception('Unsupported payout method: ' . $payoutMethodId);
                }


                
                $payoutAmount = 0;
                foreach ($bookings as $booking){
                    // Only accept bookings which have been paid in full
                    // Actually the query already implies this (booking.status = Booking::STATUS_PAID)
                    //if (!$bookingAgent->bookingPaidInFull($booking)) continue;
                    
                    $payout->addBooking($booking);
                    $booking->setPayout($payout);
                    
                    $payoutAmount += $booking->getPayoutAmount();
                }

                // If bookings exists, persist payout
                if ($payout->getBooking()->count() > 0){
                    $payout->setAmount($payoutAmount);
                    
                    $em->persist($payout);
                
                    if ($flush) $em->flush();
                    
                    return $payout;
                }
                
                return null;
                
            }
            
        } catch (\Exception $e){
            return $e;
        }
        
    }

}
?>
