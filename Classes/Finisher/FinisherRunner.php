<?php

declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class FinisherRunner
 */
class FinisherRunner
{
    protected string $interface = FinisherInterface::class;

    /**
     * Call finisher classes after submit
     *
     * @throws ClassDoesNotExistException
     * @throws InterfaceNotImplementedException
     */
    public function callFinishers(
        Mail $mail,
        bool $formSubmitted,
        string $actionMethodName,
        array $settings,
        ContentObjectRenderer $contentObject
    ): void {
        foreach ($this->getFinisherClasses($settings) as $finisherSettings) {
            $class = $finisherSettings['class'];
            $this->requireFile($finisherSettings);
            if (!class_exists($class)) {
                throw new ClassDoesNotExistException(
                    'Finisher class ' . $class . ' does not exists - check if file was loaded correctly',
                    1578644684
                );
            }

            $contentObjectParent = clone $contentObject;
            if (is_subclass_of($class, $this->interface)) {
                /** @var AbstractFinisher $finisher */
                $finisher = GeneralUtility::makeInstance(
                    $class,
                    $mail,
                    $finisherSettings['config'] ?? [],
                    $settings,
                    $formSubmitted,
                    $actionMethodName,
                    $contentObject
                );
                $finisher->initializeFinisher();
                $this->callFinisherMethods($finisher);
            } else {
                throw new InterfaceNotImplementedException(
                    'Finisher does not implement ' . $this->interface,
                    1578644680
                );
            }

            $contentObject = $contentObjectParent;
        }
    }

    /**
     * Call methods in finisher class
     *      *Finisher()
     */
    protected function callFinisherMethods(AbstractFinisher $finisher): void
    {
        foreach (get_class_methods($finisher) as $method) {
            if (StringUtility::endsWith($method, 'Finisher') && !StringUtility::startsWith($method, 'initialize')) {
                $this->callInitializeFinisherMethod($finisher, $method);
                $finisher->{$method}();
            }
        }
    }

    /**
     * Call initializeFinisherMethods like "initializeUploadFinisher()"
     */
    protected function callInitializeFinisherMethod(AbstractFinisher $finisher, string $finisherMethod): void
    {
        if (method_exists($finisher, 'initialize' . ucfirst($finisherMethod))) {
            $finisher->{'initialize' . ucfirst($finisherMethod)}();
        }
    }

    /**
     * Get all finisher classes from typoscript and sort them
     */
    protected function getFinisherClasses(array $settings): array
    {
        $finishers = (array)$settings['finishers'];
        ksort($finishers);
        return $finishers;
    }

    protected function requireFile(array $finisherSettings): void
    {
        if (!empty($finisherSettings['require']) && file_exists($finisherSettings['require'])) {
            /** @noinspection PhpIncludeInspection */
            require_once($finisherSettings['require']);
        }
    }
}
