<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\MessageBundle\FOSMessageBundle(),
            new PunkAve\FileUploaderBundle\PunkAveFileUploaderBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new JMS\Payment\CoreBundle\JMSPaymentCoreBundle,
            new Zizoo\BaseBundle\ZizooBaseBundle(),
            new Zizoo\BoatBundle\ZizooBoatBundle(),
            new Zizoo\UserBundle\ZizooUserBundle(),
            new Zizoo\BookingBundle\ZizooBookingBundle(),
            new Zizoo\ProfileBundle\ZizooProfileBundle(),
            new Zizoo\AddressBundle\ZizooAddressBundle(),
            new Zizoo\MessageBundle\ZizooMessageBundle(),
            new Zizoo\JqGridCustomBundle\ZizooJqGridCustomBundle(),
            new Zizoo\CrewBundle\ZizooCrewBundle(),
            new Zizoo\BillingBundle\ZizooBillingBundle(),
            new Zizoo\ReservationBundle\ZizooReservationBundle(),
            new Zizoo\NotificationBundle\ZizooNotificationBundle(),
            new Zizoo\AdminBundle\ZizooAdminBundle(),
            new Zizoo\CharterBundle\ZizooCharterBundle(),
            new Zizoo\EventBundle\ZizooEventBundle(),
            new Zizoo\MediaBundle\ZizooMediaBundle(),
            new Zizoo\DatatablesBundle\ZizooDatatablesBundle(),
            new Zizoo\PaymentBundle\ZizooPaymentBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
