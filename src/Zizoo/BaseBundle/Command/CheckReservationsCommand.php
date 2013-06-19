<?php
namespace Zizoo\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class CheckReservationsCommand extends ContainerAwareCommand
{
    
    private $container, $em;
    
    protected function configure()
    {
        $this
            ->setName('zizoo:check_reservations')
            ->setDescription('Check Zizoo reservations')
            ->setDefinition(array(
                
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine.orm.entity_manager'); 
//        
//        $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'reservation')
//                                        ->leftJoin('reservation.reservation', 'reservation')
//                                        ->leftJoin('booking.payment', 'payment')
//                                        ->leftJoin('reservation.boat', 'boat')
//                                        ->leftJoin('boat.charter', 'charter')
        
    }
}
?>
