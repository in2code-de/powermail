<?php

declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use Exception;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\ClassDoesNotExistException;
use In2code\Powermail\Exception\InterfaceNotImplementedException;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class DataProcessorRunner
 */
class DataProcessorRunner
{
    protected string $interface = \In2code\Powermail\DataProcessor\DataProcessorInterface::class;

    /**
     * @throws Exception
     */
    public function callDataProcessors(
        Mail $mail,
        string $actionMethodName,
        array $settings,
        ContentObjectRenderer $contentObject
    ): void {
        foreach ($this->getDataProcessorClasses($settings) as $dpSettings) {
            $class = $dpSettings['class'];
            $this->requireFile($dpSettings);
            if (!class_exists($class)) {
                throw new ClassDoesNotExistException(
                    'Data processor class ' . $class . ' does not exists - check if file was loaded correctly',
                    1578601123
                );
            }

            if (is_subclass_of($class, $this->interface)) {
                $dpSettings['config'] = isset($dpSettings['config']) ? (array)$dpSettings['config'] : [];

                /** @var AbstractDataProcessor $dataProcessor */
                $dataProcessor =  GeneralUtility::makeInstance(
                    $dpSettings['class'],
                    $mail,
                    $dpSettings['config'],
                    $settings,
                    $actionMethodName,
                    $contentObject
                );
                $dataProcessor->initializeDataProcessor();
                $this->callDataProcessorMethods($dataProcessor);
            } else {
                throw new InterfaceNotImplementedException(
                    'DataProcessor does not implement ' . $this->interface,
                    1578601128
                );
            }
        }
    }

    /**
     * Call methods in dataProcessor class
     *      *DataProcessor()
     */
    protected function callDataProcessorMethods(AbstractDataProcessor $dataProcessor): void
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
     */
    protected function callInitializeDataProcessorMethod(
        AbstractDataProcessor $dataProcessor,
        string $finisherMethod
    ): void {
        if (method_exists($dataProcessor, 'initialize' . ucfirst($finisherMethod))) {
            $dataProcessor->{'initialize' . ucfirst($finisherMethod)}();
        }
    }

    /**
     * Get all finisher classes from typoscript and sort them
     */
    protected function getDataProcessorClasses(array $settings): array
    {
        $dataProcessors = (array)$settings['dataProcessors'];
        ksort($dataProcessors);
        return $dataProcessors;
    }

    protected function requireFile(array $dpSettings): void
    {
        if (!empty($dpSettings['require']) && file_exists($dpSettings['require'])) {
            /** @noinspection PhpIncludeInspection */
            require_once($dpSettings['require']);
        }
    }
}
