<?php

declare(strict_types=1);
namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Service\UploadFolderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractCleanupCommand
 */
abstract class AbstractCleanupCommand extends Command
{
    protected function removeFilesFromRelativeDirectory(
        OutputInterface $output,
        string $directory,
        int $period
    ): void {
        // todo: should have a return value (success / error)
        $service = GeneralUtility::makeInstance(UploadFolderService::class);
        if ($service->isFalCombinedIdentifier($directory)) {
            $this->removeFilesFromFolder($output, $directory, $period, $service);
            return;
        }

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

    protected function removeFilesFromFolder(
        OutputInterface $output,
        string $folder,
        int $period,
        UploadFolderService $service
    ): void {
        $fileNames = $service->getFileNames($folder);
        $counter = 0;
        foreach ($fileNames as $fileName) {
            $mtime = $service->getModificationTime($folder, $fileName);
            if ($period === 0 || ($period > 0 && (time() - $mtime > $period))) {
                if ($service->deleteFile($folder, $fileName)) {
                    $counter++;
                }
            }
        }

        $output->writeln($counter . ' files removed from your system');
    }
}
