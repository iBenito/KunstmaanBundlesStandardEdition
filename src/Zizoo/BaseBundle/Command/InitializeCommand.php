<?php
namespace Zizoo\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class InitializeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zizoo:initialize')
            ->setDescription('Initialize Zizoo')
            ->setDefinition(array(
                new InputOption(
                    'load-fixtures', null, InputOption::VALUE_NONE,
                    'Load fixtures.'
                ),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
       
        $loadFixtures   = (true === $input->getOption('load-fixtures'));
        
        if ($input->isInteractive()) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Careful, database will be purged. Do you want to continue Y/N ?</question>', false)) {
                return;
            }
        }
       
        $command = $this->getApplication()->find('doctrine:schema:drop');
        $arguments = array('command' => 'doctrine:schema:drop', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        $command = $this->getApplication()->find('doctrine:schema:update');
        $arguments = array('command' => 'doctrine:schema:update', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        $command = $this->getApplication()->find('zizoo:load');
        $arguments = array('command' => 'zizoo:load', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        if ($input->isInteractive()) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Do you want to continue load sample data Y/N ?</question>', false)) {
                return;
            }
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $arguments = array('command' => 'doctrine:fixtures:load', '--append' => true);
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } else if ($loadFixtures){
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $arguments = array('command' => 'doctrine:fixtures:load', '--append' => true);
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        }
        
    }
}
?>
