<?php
namespace Zizoo\BillingBundle\Command;

use Zizoo\CharterBundle\Entity\Charter;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class CreatePayoutsCommand extends ContainerAwareCommand
{
    
    private $container, $em;
    
    protected function configure()
    {
        $this
            ->setName('zizoo:create_payouts')
            ->setDescription('Create Zizoo payouts')
            ->setDefinition(array(
                
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine.orm.entity_manager'); 
        $billingService     = $this->container->get('zizoo_billing.billing_service');
        
        $charters           = $this->em->getRepository('ZizooCharterBundle:Charter')->findAll();
        
        foreach ($charters as $charter){
            try {
                $billingService->createPayout($charter);
                $this->em->flush();
            } catch (\Exception $e){
                // TODO: handle error
            }
            
        }
        

        
    }
}
?>
