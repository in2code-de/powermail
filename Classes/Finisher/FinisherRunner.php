<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
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
 * Class FinisherRunner
 * @package In2code\Powermail\Finisher
 */
class FinisherRunner
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
                throw new \Exception(
                    'Finisher class ' . $class . ' does not exists - check if file was loaded correctly'
                );
            }
            if (is_subclass_of($class, $this->interface)) {
                /** @var AbstractFinisher $finisher */
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $finisher = $this->objectManager->get(
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
                throw new \Exception('Finisher does not implement ' . $this->interface);
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
