<?php
declare(strict_types = 1);
namespace In2code\Powermail\Signal;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Trait SignalTrait
 */
trait SignalTrait
{
    /**
     * @var bool
     */
    protected bool $signalEnabled = true;

    /**
     * Instance a new signalSlotDispatcher and offer a signal
     *
     * @param string $signalClassName
     * @param string $signalName
     * @param array $arguments
     * @return void
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws Exception
     */
    protected function signalDispatch(string $signalClassName, string $signalName, array $arguments): void
    {
        if ($this->isSignalEnabled()) {
            $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
            $signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
        }
    }

    /**
     * @return bool
     */
    protected function isSignalEnabled(): bool
    {
        return $this->signalEnabled;
    }

    /**
     * Signal can be disabled for testing
     *
     * @return void
     */
    protected function disableSignals(): void
    {
        $this->signalEnabled = false;
    }
}
