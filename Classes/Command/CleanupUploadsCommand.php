<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class CleanupUploadsCommand
 */
class CleanupUploadsCommand extends AbstractCleanupCommand
{
    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Remove all uploaded files in uploads/tx_powermail/');
        $this->addArgument(
            'period',
            InputArgument::REQUIRED,
            'Define how old the files could be (in seconds) that should be deleted (0 = delete all)'
        );
    }

    /**
     * This task will clean up all (!) files which are located in uploads/tx_powermail/
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->removeFilesFromRelativeDirectory($output, 'uploads/tx_powermail/', (int)$input->getArgument('period'));
        // todo implement error handling
        return Command::SUCCESS;
    }
}
