<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanupExportsCommand
 */
class CleanupExportsCommand extends AbstractCleanupCommand
{
    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Remove all export files in typo3temp/assets/tx_powermail/');
        $this->addArgument(
            'period',
            InputArgument::REQUIRED,
            'Define how old the files could be (in seconds) that should be deleted (0 = delete all)'
        );
    }

    /**
     * This task will clean up all (!) files which are located in typo3temp/assets/tx_powermail/
     * e.g.: old captcha images and old export files (from export task - if stored in typo3temp folder)
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->removeFilesFromRelativeDirectory(
            $output,
            'typo3temp/assets/tx_powermail/',
            (int)$input->getArgument('period')
        );
        // todo implement error handling
        return Command::SUCCESS;
    }
}
