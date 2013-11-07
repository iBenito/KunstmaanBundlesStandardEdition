<?php
namespace Zizoo\AycBundle\Command;

use Zizoo\BoatBundle\Entity\Equipment;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

use Doctrine\Common\Collections\ArrayCollection;

class LoadAssetsCommand extends ContainerAwareCommand
{
    private $container, $em;   
    private $groups, $users, $countries, $marinas, $message_types;
    
    protected function configure()
    {
        $this
            ->setName('ayc:loadassets')
            ->setDescription('Load necessary data into Zizoo');
    }

    private function loadEquipment()
    {
        $getEquipment = file_get_contents(dirname(__FILE__).'/Data/ayc_equipment.json');

        $equipment = json_decode($getEquipment);

        foreach ($equipment as $e)
        {
            $equipmentEntity = new Equipment();
            $equipmentEntity->setId($e->id);
            $equipmentEntity->setName($e->name);
            $equipmentEntity->setOrder($e->order);

            $this->em->persist($equipmentEntity);
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine.orm.entity_manager');     
    
        $this->loadEquipment();
        
        $this->em->flush();
    }
}
?>