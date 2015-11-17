<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\FinisherService;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
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
 * Call all finishers
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
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
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * TypoScript settings
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Call finisher classes after submit
     *
     * @param Mail $mail
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     * @return void
     */
    public function callFinishers(
        Mail $mail,
        $formSubmitted,
        $actionMethodName,
        $settings,
        ContentObjectRenderer $contentObject
    ) {
        $this->initialize($settings, $contentObject);
        $this->callLocalFinishers($mail, $formSubmitted, $actionMethodName);
        $this->callForeignFinishers($mail, $formSubmitted, $actionMethodName);
    }

    /**
     * Call own finisher classes after submit
     *
     * @param Mail $mail
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @return void
     */
    protected function callLocalFinishers(Mail $mail, $formSubmitted = false, $actionMethodName = null)
    {
        $ownClasses = $this->getOwnFinisherClasses();
        foreach ($ownClasses as $className) {
            /** @var FinisherService $finisherService */
            $finisherService = $this->objectManager->get(
                'In2code\\Powermail\\Domain\\Service\\FinisherService',
                $mail,
                $this->settings,
                $this->contentObject
            );
            $finisherService->setClass(__NAMESPACE__ . '\\' . $className);
            $finisherService->setRequirePath(null);
            $finisherService->setConfiguration(array());
            $finisherService->setFormSubmitted($formSubmitted);
            $finisherService->setActionMethodName($actionMethodName);
            $finisherService->start();
        }
    }

    /**
     * Call foreign finisher classes after submit
     *
     * @param Mail $mail
     * @param bool $formSubmitted
     * @param string $actionMethodName
     * @return void
     */
    protected function callForeignFinishers(Mail $mail, $formSubmitted = false, $actionMethodName = null)
    {
        if (is_array($this->settings['finishers'])) {
            foreach ($this->settings['finishers'] as $finisherSettings) {
                /** @var FinisherService $finisherService */
                $finisherService = $this->objectManager->get(
                    'In2code\\Powermail\\Domain\\Service\\FinisherService',
                    $mail,
                    $this->settings,
                    $this->contentObject
                );
                $finisherService->setClass($finisherSettings['class']);
                $finisherService->setRequirePath((string) $finisherSettings['require']);
                $finisherService->setConfiguration((array) $finisherSettings['config']);
                $finisherService->setFormSubmitted($formSubmitted);
                $finisherService->setActionMethodName($actionMethodName);
                $finisherService->start();
            }
        }
    }

    /**
     * Get all finisher classes in same directory
     *
     * @return array
     */
    protected function getOwnFinisherClasses()
    {
        $classNames = array();
        foreach (GeneralUtility::getFilesInDir(__DIR__) as $fileName) {
            $className = basename($fileName, '.php');
            if (StringUtility::endsWith($className, 'Finisher') && $className !== 'AbstractFinisher') {
                $classNames[] = $className;
            }
        }
        return $classNames;

    }

    /**
     * Initialize
     *
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     * @return void
     */
    public function initialize(array $settings, ContentObjectRenderer $contentObject)
    {
        $this->settings = $settings;
        $this->contentObject = $contentObject;
    }
}
