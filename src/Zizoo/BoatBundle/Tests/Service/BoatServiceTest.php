<?php

namespace Zizoo\BoatBundle\Tests\Service;

use Zizoo\BaseBundle\Tests\KernelAwareTest;
use Zizoo\BoatBundle\Service\BoatService;

class BoatServiceTest extends KernelAwareTest
{
    /** @var \Zizoo\BoatBundle\Service\BoatService */
    protected $boatService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->boatService = $this->container->get('boat_service');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->boatService);
    }

    public function testPrice()
    {
        $boat = 1;
        $from = "2013-12-1";
        $to = "2013-12-2";

        $this->assertEquals($this->boatService->getPrice($boat, $from, $to), 230);

    }
}
