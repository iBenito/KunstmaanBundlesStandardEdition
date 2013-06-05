<?php
namespace Zizoo\EventBundle\Command;

use Zizoo\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zizoo:list_users')
            ->setDescription('List Users');
            //->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer();
        $em         = $container->get('doctrine.orm.entity_manager');
        
        $users       = $em->getRepository('ZizooUserBundle:User')->findAll();

        foreach ($users as $user){
            $output->writeln($user->getEmail());
        }
    }
}
?>
