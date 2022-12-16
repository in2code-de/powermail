<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Events\CustomValidatorEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CustomValidator
 */
class CustomValidator extends StringValidator
{
    /**
     * Custom validation of given Params
     *
     * @param Mail $mail
     * @return bool
     */
    public function isValid($mail): void
    {
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(
            GeneralUtility::makeInstance(CustomValidatorEvent::class, $mail, $this)
        );
    }
}
