<?php

namespace Zizoo\BaseBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Zizoo\BaseBundle\Command\InitializeCommand;

/**
 * Test case class helpful with Entity tests requiring the database interaction.
 * For regular entity tests it's better to extend standard \PHPUnit_Framework_TestCase instead.
 */
abstract class KernelAwareTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    protected static $application;

    /**
     * @return null
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel(array('test', true));
        static::$kernel->boot();

        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $options = array('command' => 'zizoo:initialize', '--force' => true, '--load-fixtures' => false, '--install-assets' => false);
//        self::runCommand($options);
        parent::setUp();
    }

    /**
     * @return null
     */
    protected function tearDown()
    {
        static::$kernel->shutdown();

        parent::tearDown();
    }

    protected static function runCommand($command, $interactive = false)
    {
        $input = new ArrayInput($command);
        $input->setInteractive(false);
        $output = new NullOutput();

        if ($interactive) {
            $input->setInteractive(true);
            $output = new ConsoleOutput();
        }

        return self::getApplication()->run($input, $output);
    }

    protected static function getApplication()
    {
        if (null === self::$application) {

            self::$application = new Application(static::$kernel);
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
}