<?php
namespace Zizoo\EventBundle\Command;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

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
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $setHoursToRespond  = $this->container->getParameter('zizoo_reservation.reservation_request_response_hours');
        
        $charters           = $this->em->getRepository('ZizooCharterBundle:Charter')->findAll();
        
        foreach ($charters as $charter){
            $then = new \DateTime(); 
            $then->modify('-'.$setHoursToRespond.' hours');
           
            $qb = $this->em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'reservation')
                                                    ->leftJoin('reservation.boat', 'boat')
                                                    ->leftJoin('boat.charter', 'charter')
                                                    ->select('reservation')
                                                    ->where('charter = :charter')
                                                    ->setParameter('charter', $charter)
                                                    ->andWhere('reservation.status = :status')
                                                    ->setParameter('status', Reservation::STATUS_REQUESTED)
                                                    ->andWhere('reservation.created < :then')
                                                    ->setParameter('then', $then);
            
            
            $result = $qb->getQuery()->getResult();
            foreach ($result as $reservation){
                try {
                    $reservationAgent->expireReservation($reservation, false);
                } catch (InvalidReservationException $e){
                    // TODO: handle
                }
            }
            $this->em->flush();
            
        }
        

        
    }
}
?>
