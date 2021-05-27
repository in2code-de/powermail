<?php

declare(strict_types=1);

namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Service\CleanupService;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class CleanupAnswersCommandController extends CommandController
{
    /**
     * @var CleanupService
     */
    protected $cleanupService;

    /**
     * @param CleanupService $cleanupService
     */
    public function injectCleanupService(CleanupService $cleanupService): void
    {
        $this->cleanupService = $cleanupService;
    }

    /**
     * @param int $age Age of the answers in seconds. e.g. 5184000 = 60 days
     * @param int $pid Optional PID. If set, only answers stored on the given PID are cleaned up.
     * @return bool
     */
    public function deleteCommand(int $age, int $pid = null): bool
    {
        $stats = $this->cleanupService->deleteMailsOlderThanAgeInPid($age, $pid);
        $this->outputLine(
            sprintf(
                'Deleted %d mails containing %d answers and %d files',
                $stats['mails'],
                $stats['answers'],
                $stats['files']
            )
        );
        return true;
    }
}
