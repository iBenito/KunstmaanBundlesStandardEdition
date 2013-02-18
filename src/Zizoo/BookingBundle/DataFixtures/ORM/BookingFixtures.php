<?php

namespace Zizoo\BookingBundle\DataFixtures\ORM;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\BookingBundle\Form\Model\Booking;
use Zizoo\BookingBundle\Form\Model\CreditCard;
use Zizoo\BookingBundle\Form\Model\BillingAddress;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;


class BookingFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * Fixture reference repository
     * 
     * @var ReferenceRepository
     */
    protected $referenceRepository;
    
    /**
     * {@inheritdoc}
     */
    public function setReferenceRepository(ReferenceRepository $referenceRepository)
    {
        $this->referenceRepository = $referenceRepository;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        
        /**
        $bookingAgent = $this->container->get('zizoo_booking_booking_agent');
        $boat1 = $this->getReference('boat-1');
        $user1 = $this->getReference('user-1');
        
        $availabilities = $boat1->getAvailability();
        if ($availabilities && $availabilities->offsetExists(0)){
            $availability = $availabilities->first();
            $from   = clone $availability->getAvailableFrom();
            $to     = clone $availability->getAvailableUntil();
            $from->modify('+1 week');
            $to->modify('-1 week');
            
            $bookBoat = new BookBoat($boat1->getID());
            $bookBoat->setNumGuests(5);
            $bookBoat->setReservationFrom($from);
            $bookBoat->setReservationTo($to);
            
            $interval = $from->diff($to);
            $price = $interval->days * $availability->getPrice();
            
            $profile = $user1->getProfile();
            
            $expiryDate = clone $availability->getAvailableUntil();
            $expiryDate->modify('+2 years');
            $creditCard = new CreditCard();
            $creditCard->setCVV('123');
            $creditCard->setCardHolder($profile->getFirstName() . ' ' . $profile->getLastName());
            $creditCard->setCreditCardNumber('4111111111111111');
            $creditCard->setExpiryMonth($expiryDate->format('m'));
            $creditCard->setExpiryYear($expiryDate->format('Y'));
            
            $billingAddress = new BillingAddress();
            $billingAddress->setCountryCodeAlpha2($manager->merge($this->getReference('countryDE'))->getIso());
            $billingAddress->setExtendedAddress('');
            $billingAddress->setFirstName('Mr');
            $billingAddress->setLastName('Smith');
            $billingAddress->setLocality('Berlin');
            $billingAddress->setPostalCode('12345');
            $billingAddress->setRegion('');
            $billingAddress->setStreetAddress('Hauptstrasse 1');
            
            $booking = new Booking();
            $booking->setCreditCard($creditCard);
            $booking->setBilling($billingAddress);
            
            $bookingAgent->braintreeMakeBooking($user1, $booking, $price, $bookBoat, $boat1);
        }
        
        */

    }

    public function getOrder()
    {
        return 7;
    }
    
    /**
     * Set the reference entry identified by $name
     * and referenced to managed $object. If $name
     * already is set, it overrides it
     * 
     * @param string $name
     * @param object $object - managed object
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::setReference
     * @return void
     */
    public function setReference($name, $object)
    {
        $this->referenceRepository->setReference($name, $object);
    }
    
    /**
     * Set the reference entry identified by $name
     * and referenced to managed $object. If $name
     * already is set, it overrides it
     * 
     * @param string $name
     * @param object $object - managed object
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::addReference
     * @return void
     */
    public function addReference($name, $object)
    {
        $this->referenceRepository->addReference($name, $object);
    }
    
    /**
     * Loads an object using stored reference
     * named by $name
     * 
     * @param string $name
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::getReference
     * @return object
     */
    public function getReference($name)
    {
        return $this->referenceRepository->getReference($name);
    }
    
    /**
     * Check if an object is stored using reference
     * named by $name
     * 
     * @param string $name
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::hasReference
     * @return boolean
     */
    public function hasReference($name)
    {
        return $this->referenceRepository->hasReference($name);
    }

}