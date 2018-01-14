<?php
declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class DataProcessorRunner
 */
class DataProcessorRunner
{

    /**
     * @var string
     */
    protected $interface = 'In2code\Powermail\DataProcessor\DataProcessorInterface';

    /**
     * Call all data processors
     *
     * @param Mail $mail
     * @param string $actionMethodName
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     * @throws \Exception
     */
    public function callDataProcessors(
        Mail $mail,
        $actionMethodName,
        array $settings,
        ContentObjectRenderer $contentObject
    ) {
        foreach ($this->getDataProcessorClasses($settings) as $dpSettings) {
            $class = $dpSettings['class'];
            $this->requireFile($dpSettings);
            if (!class_exists($class)) {
                throw new \UnexpectedValueException(
                    'Data processor class ' . $class . ' does not exists - check if file was loaded correctly'
                );
            }
            if (is_subclass_of($class, $this->interface)) {
                /** @var AbstractDataProcessor $dataProcessor */
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $dataProcessor =  ObjectUtility::getObjectManager()->get(
                    $dpSettings['class'],
                    $mail,
                    (array)$dpSettings['config'],
                    $settings,
                    $actionMethodName,
                    $contentObject
                );
                $dataProcessor->initializeDataProcessor();
                $this->callDataProcessorMethods($dataProcessor);
            } else {
                throw new \UnexpectedValueException('DataProcessor does not implement ' . $this->interface);
            }
        }
    }

    /**
     * Call methods in dataProcessor class
     *      *DataProcessor()
     *
     * @param AbstractDataProcessor $dataProcessor
     * @return void
     */
    protected function callDataProcessorMethods(AbstractDataProcessor $dataProcessor)
    {
        foreach (get_class_methods($dataProcessor) as $method) {
            if (StringUtility::endsWith($method, 'DataProcessor') &&
                !StringUtility::startsWith($method, 'initialize')
            ) {
                $this->callInitializeDataProcessorMethod($dataProcessor, $method);
                $dataProcessor->{$method}();
            }
        }
    }

    /**
     * Call initializeDataProcessorMethods like "initializeUploadDataProcessor()"
     *
     * @param AbstractDataProcessor $dataProcessor
     * @param string $finisherMethod
     * @return void
     */
    protected function callInitializeDataProcessorMethod(AbstractDataProcessor $dataProcessor, $finisherMethod)
    {
        if (method_exists($dataProcessor, 'initialize' . ucFirst($finisherMethod))) {
            $dataProcessor->{'initialize' . ucFirst($finisherMethod)}();
        }
    }

    /**
     * Get all finisher classes from typoscript and sort them
     *
     * @param array $settings
     * @return array
     */
    protected function getDataProcessorClasses($settings)
    {
        $dataProcessors = (array)$settings['dataProcessors'];
        ksort($dataProcessors);
        return $dataProcessors;
    }

    /**
     * @param array $dpSettings
     */
    protected function requireFile(array $dpSettings)
    {
        if (!empty($dpSettings['require'])) {
            if (file_exists($dpSettings['require'])) {
                /** @noinspection PhpIncludeInspection */
                require_once($dpSettings['require']);
            }
        }
    }
}
