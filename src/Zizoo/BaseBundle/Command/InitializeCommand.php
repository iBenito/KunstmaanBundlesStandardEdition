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
                new InputOption(
                    'install-assets', null, InputOption::VALUE_NONE,
                    'Install web assets.'
                ),
                new InputOption(
                    'clear-cache', null, InputOption::VALUE_NONE,
                    'Clear cache'
                ),
                new InputOption(
                    'warmup-cache', null, InputOption::VALUE_NONE,
                    'Warm up cache'
                ),
                new InputOption(
                    'force', null, InputOption::VALUE_NONE,
                    'Force'
                ),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loadFixtures   = (true === $input->getOption('load-fixtures'));
        $installAssets  = (true === $input->getOption('install-assets'));
        $clearCache     = (true === $input->getOption('clear-cache'));
        $warmupCache    = (true === $input->getOption('warmup-cache'));
        $force          = (true === $input->getOption('force'));
        $interactive    = $input->isInteractive();
        $dialog         = null;
        
        if ($interactive) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Careful, database will be purged. Do you want to continue Y/N ?</question>', false)) {
                return;
            }
        } else if ($force !== true){
            return;
        }
        
        
        // DROP SCHEMA
        $command = $this->getApplication()->find('doctrine:schema:drop');
        $arguments = array('command' => 'doctrine:schema:drop', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        
        // UPDATE (CREATE) SCHEMA
        $command = $this->getApplication()->find('doctrine:schema:update');
        $arguments = array('command' => 'doctrine:schema:update', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        
        // LOAD NECESSARY DATA
        $command = $this->getApplication()->find('zizoo:load');
        $arguments = array('command' => 'zizoo:load', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        
        if ($loadFixtures){
            // APPEND FIXTURES 
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $arguments = array('command' => 'doctrine:fixtures:load', '--append' => true);
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } else if ($interactive){
            // APPEND FIXTURES ?
            if ($dialog->askConfirmation($output, '<question>Do you want to continue load sample data Y/N ?</question>', false)) {
                $command = $this->getApplication()->find('doctrine:fixtures:load');
                $arguments = array('command' => 'doctrine:fixtures:load', '--append' => true);
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
            }
        }
        
        if ($installAssets){
            // INSTALL WEB ASSETS
            $command = $this->getApplication()->find('assets:install');
            $arguments = array('command' => 'assets:install', 'web', '--symlink');
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } else if ($interactive){
            // INSTALL WEB ASSETS ?
            if ($dialog->askConfirmation($output, '<question>Do you want to install web assets Y/N ?</question>', false)) {
                $command = $this->getApplication()->find('assets:install');
                $arguments = array('command' => 'assets:install', 'web', '--symlink');
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
            }
        }
        
        if ($clearCache){
            // CLEAR CACHE
            $command = $this->getApplication()->find('cache:clear');

            $arguments = array('command' => 'cache:clear', '-env=dev');
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);

            $arguments = array('command' => 'cache:clear', '-env=prod');
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } else if ($interactive){
            if ($dialog->askConfirmation($output, '<question>Do you want to clear the cache Y/N ?</question>', false)) {
                $command = $this->getApplication()->find('cache:clear');
                
                $arguments = array('command' => 'cache:clear', '-env=dev');
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                
                $arguments = array('command' => 'cache:clear', '-env=prod');
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
            }
        } 
        
        if ($warmupCache){
            // WARMUP CACHE
            $command = $this->getApplication()->find('cache:warmup');

            $arguments = array('command' => 'cache:warmup', '-env=dev');
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);

            $arguments = array('command' => 'cache:warmup', '-env=prod');
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        } else if ($interactive){
            if ($dialog->askConfirmation($output, '<question>Do you want to warm up the cache Y/N ?</question>', false)) {
                $command = $this->getApplication()->find('cache:warmup');
                
                $arguments = array('command' => 'cache:warmup', '-env=dev');
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
                
                $arguments = array('command' => 'cache:warmup', '-env=prod');
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
            }
        }
   
        
    }
}
?>
