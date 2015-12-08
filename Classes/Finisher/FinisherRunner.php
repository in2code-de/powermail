<?php
namespace In2code\Powermail\Finisher;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\FinisherService;
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
 * Get all finishers classes and call finisher service for each of them
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
        foreach ($this->getFinisherClasses($settings) as $finisherSettings) {
            /** @var FinisherService $finisherService */
            $finisherService = $this->objectManager->get(
                'In2code\\Powermail\\Domain\\Service\\FinisherService',
                $mail,
                $settings,
                $contentObject
            );
            $finisherService->setClass($finisherSettings['class']);
            $finisherService->setRequirePath((string) $finisherSettings['require']);
            $finisherService->setConfiguration((array) $finisherSettings['config']);
            $finisherService->setFormSubmitted($formSubmitted);
            $finisherService->setActionMethodName($actionMethodName);
            $finisherService->start();
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
        $finishers = (array) $settings['finishers'];
        ksort($finishers);
        return $finishers;
    }
}
