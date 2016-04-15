<?php
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class DataProcessorRunner
 * @package In2code\Powermail\DataProcessor
 */
class DataProcessorRunner
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

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
                throw new \Exception(
                    'Data processor class ' . $class . ' does not exists - check if file was loaded correctly'
                );
            }
            if (is_subclass_of($class, $this->interface)) {
                /** @var AbstractDataProcessor $dataProcessor */
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $dataProcessor = $this->objectManager->get(
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
                throw new \Exception('DataProcessor does not implement ' . $this->interface);
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
            if (
                StringUtility::endsWith($method, 'DataProcessor') &&
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
