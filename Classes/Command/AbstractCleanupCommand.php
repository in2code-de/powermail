<?php
declare(strict_types = 1);
namespace In2code\Powermail\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractCleanupCommand
 */
abstract class AbstractCleanupCommand extends Command
{
    /**
     * @param OutputInterface $output
     * @param string $directory
     * @param int $period
     * @return void
     */
    protected function removeFilesFromRelativeDirectory(
        OutputInterface $output,
        string $directory,
        int $period
    ): void {
        // todo: should have a return value (success / error)
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($directory), '', true);
        $counter = 0;
        foreach ($files as $file) {
            if ($period === 0 || ($period > 0 && (time() - filemtime($file) > $period))) {
                $counter++;
                unlink($file);
            }
        }
        $output->writeln($counter . ' files removed from your system');
    }
}
