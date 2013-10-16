<?php

namespace Zizoo\ReservationBundle\DataFixtures\ORM;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\BoatBundle\Form\Model\ReservationRange;
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


class ReservationFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
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
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        
        $boat1 = $this->getReference('boat-1');
        $user1 = $this->getReference('user-1');
        $user2 = $this->getReference('user-2');
        
        $from   = new \DateTime();
        $to     = new \DateTime();
        $from->modify('-1 week');
        $to->modify('+1 week');

        $bookBoat = new BookBoat($boat1->getId(), !$boat1->getCrewOptional());
        $bookBoat->setNumGuests(5);       
        $bookBoat->setGuestId($user2->getId());
        $reservationRange = new ReservationRange();
        $reservationRange->setReservationFrom($from);
        $reservationRange->setReservationTo($to);
        $bookBoat->setReservationRange($reservationRange);
        
        $cost = $reservationAgent->getTotalPrice($boat1, $from, $to, true);
        
        //public function makeReservation(Boat $boat, BookBoat $bookBoat, $cost, User $guest, $flush=false)
        //$reservation = $reservationAgent->makeReservation($boat1, $bookBoat, $cost, $user1, true);
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