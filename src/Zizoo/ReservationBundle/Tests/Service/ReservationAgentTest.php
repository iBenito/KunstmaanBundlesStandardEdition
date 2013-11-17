<?php

namespace Zizoo\ReservationBundle\Tests\Service;

use Zizoo\BaseBundle\Tests\KernelAwareTest;
use Zizoo\ReservationBundle\Service\ReservationAgent;

class BoatServiceTest extends KernelAwareTest
{
    /** @var \Zizoo\ReservationBundle\Service\ReservationAgent */
    protected $reservationAgent;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
//        --fixtures=src/Acme/FormBundle/DataFixtures/ORM
        $options = array('command' => 'doctrine:fixtures:load', '--purge-with-truncate' => true, '--fixtures' => 'src/Zizoo/BoatBundle/DataFixtures/ORM');
        self::runCommand($options);

        $this->reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->reservationAgent);
    }

    public function testTotalDefaultPrice()
    {
        $boatId = 3;
        $from = new \DateTime("2013-12-1");
        $to = new \DateTime("2013-12-2");

        $boat = $this->entityManager->getRepository('ZizooBoatBundle:Boat')->findOneById($boatId);
        $total = $this->reservationAgent->getTotalPrice($boat, $from, $to, TRUE);

        $this->assertEquals($total, 99.99);

    }

    public function testTotalPriceOnePriceRange()
    {
        $boatId = 3;
        $from = new \DateTime("2013-12-1");
        $to = new \DateTime("2013-12-2");

        $boat = $this->entityManager->getRepository('ZizooBoatBundle:Boat')->findOneById($boatId);
        $total = $this->reservationAgent->getTotalPrice($boat, $from, $to, TRUE);

        $this->assertEquals($total, 99.99);

    }
}
