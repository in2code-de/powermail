<?php
declare(strict_types=1);
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class FinisherRunner
 */
class FinisherRunner
{

    /**
     * @var string
     */
    protected $interface = 'In2code\Powermail\Finisher\FinisherInterface';

    /**
     * Call finisher classes after submit
     *
     * @param Mail $mail
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     * @return void
     * @throws \Exception
     */
    public function callFinishers(
        Mail $mail,
        $formSubmitted,
        $actionMethodName,
        $settings,
        ContentObjectRenderer $contentObject
    ) {
        foreach ($this->getFinisherClasses($settings) as $finisherSettings) {
            $class = $finisherSettings['class'];
            $this->requireFile($finisherSettings);
            if (!class_exists($class)) {
                throw new \UnexpectedValueException(
                    'Finisher class ' . $class . ' does not exists - check if file was loaded correctly'
                );
            }
            if (is_subclass_of($class, $this->interface)) {
                /** @var AbstractFinisher $finisher */
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $finisher = ObjectUtility::getObjectManager()->get(
                    $class,
                    $mail,
                    (array)$finisherSettings['config'],
                    $settings,
                    $formSubmitted,
                    $actionMethodName,
                    $contentObject
                );
                $finisher->initializeFinisher();
                $this->callFinisherMethods($finisher);
            } else {
                throw new \UnexpectedValueException('Finisher does not implement ' . $this->interface);
            }
        }
    }

    /**
     * Call methods in finisher class
     *      *Finisher()
     *
     * @param AbstractFinisher $finisher
     * @return void
     */
    protected function callFinisherMethods(AbstractFinisher $finisher)
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
     *
     * @param AbstractFinisher $finisher
     * @param string $finisherMethod
     * @return void
     */
    protected function callInitializeFinisherMethod(AbstractFinisher $finisher, $finisherMethod)
    {
        if (method_exists($finisher, 'initialize' . ucFirst($finisherMethod))) {
            $finisher->{'initialize' . ucFirst($finisherMethod)}();
        }
    }

    /**
     * Get all finisher classes from typoscript and sort them
     *
     * @param array $settings
     * @return array
     */
    protected function getFinisherClasses($settings)
    {
        $finishers = (array)$settings['finishers'];
        ksort($finishers);
        return $finishers;
    }

    /**
     * @param array $finisherSettings
     */
    protected function requireFile(array $finisherSettings)
    {
        if (!empty($finisherSettings['require'])) {
            if (file_exists($finisherSettings['require'])) {
                /** @noinspection PhpIncludeInspection */
                require_once($finisherSettings['require']);
            }
        }
    }
}
