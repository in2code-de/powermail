<?php

declare(strict_types=1);
namespace In2code\Powermail\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanupUploadsCommand
 */
class CleanupUploadsCommand extends AbstractCleanupCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Remove all (!) uploaded files in a given upload folder');
        $this->addArgument(
            'period',
            InputArgument::REQUIRED,
            'Define how old the files could be (in seconds) that should be deleted (0 = delete all)'
        );
        $this->addArgument(
            'uploadPath',
            InputArgument::OPTIONAL,
            'Define the upload Path (relative path or FAL combined identifier like "2:/tx_powermail/")',
            'uploads/tx_powermail/'
        );
    }

    /**
     * This task will clean up all (!) files which are located in the configured upload folder
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->removeFilesFromRelativeDirectory(
            $output,
            (string)$input->getArgument('uploadPath'),
            (int)$input->getArgument('period')
        );
        // todo implement error handling
        return Command::SUCCESS;
    }
}
