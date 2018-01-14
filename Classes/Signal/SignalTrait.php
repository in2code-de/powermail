<?php
declare(strict_types=1);
namespace In2code\Powermail\Signal;

use In2code\Powermail\Utility\ObjectUtility;
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
    protected $signalEnabled = true;

    /**
     * Instance a new signalSlotDispatcher and offer a signal
     *
     * @param string $signalClassName
     * @param string $signalName
     * @param array $arguments
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    protected function signalDispatch($signalClassName, $signalName, array $arguments)
    {
        if ($this->isSignalEnabled()) {
            /** @var Dispatcher $signalSlotDispatcher */
            $signalSlotDispatcher = ObjectUtility::getObjectManager()->get(Dispatcher::class);
            $signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
        }
    }

    /**
     * @return boolean
     */
    protected function isSignalEnabled()
    {
        return $this->signalEnabled;
    }

    /**
     * Signal can be disabled for testing
     *
     * @return void
     */
    protected function disableSignals()
    {
        $this->signalEnabled = false;
    }
}
