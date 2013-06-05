<?php
namespace Zizoo\EventBundle\Command;

use Zizoo\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class EventCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zizoo:events')
            ->setDescription('Check and trigger Zizoo events');
            //->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('zizoo:list_users');
        
        $arguments = array('command' => 'zizoo:list_users');
        $input = new ArrayInput($arguments);
        
        $returnCode = $command->run($input, $output);
        var_dump($returnCode);
    }
}
?>
