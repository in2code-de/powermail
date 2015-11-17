<?php
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Finisher\AbstractFinisher;
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
 * Load individual finisher classes
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class FinisherService
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * Classname
     *
     * @var string
     */
    protected $class = '';

    /**
     * Path that should be required
     *
     * @var null|string
     */
    protected $requirePath = null;

    /**
     * Finisher Configuration
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Was form already submitted
     *
     * @var bool
     */
    protected $formSubmitted = false;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     *
     * @var null
     */
    protected $actionMethodName = null;

    /**
     * @var string
     */
    protected $finisherInterface = 'In2code\Powermail\Finisher\FinisherInterface';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return FinisherService
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRequirePath()
    {
        return $this->requirePath;
    }

    /**
     * Set require path and do a require_once
     *
     * @param null|string $requirePath
     * @return FinisherService
     */
    public function setRequirePath($requirePath)
    {
        $this->requirePath = $requirePath;
        if ($this->getRequirePath() && file_exists($this->getRequirePath())) {
            require_once($this->getRequirePath());
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     * @return FinisherService
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return FinisherService
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return FinisherService
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFormSubmitted()
    {
        return $this->formSubmitted;
    }

    /**
     * @param boolean $formSubmitted
     * @return FinisherService
     */
    public function setFormSubmitted($formSubmitted)
    {
        $this->formSubmitted = $formSubmitted;
        return $this;
    }

    /**
     * @return null
     */
    public function getActionMethodName()
    {
        return $this->actionMethodName;
    }

    /**
     * @param null $actionMethodName
     * @return FinisherService
     */
    public function setActionMethodName($actionMethodName)
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * Start implementation
     *
     * @throws \Exception
     * @return void
     */
    public function start()
    {
        if (!class_exists($this->getClass())) {
            throw new \Exception(
                'Class ' . $this->getClass() . ' does not exists - check if file was loaded with autoloader'
            );
        }
        if (is_subclass_of($this->getClass(), $this->finisherInterface)) {
            /** @var AbstractFinisher $finisher */
            $finisher = $this->objectManager->get(
                $this->getClass(),
                $this->getMail(),
                $this->getConfiguration(),
                $this->getSettings(),
                $this->isFormSubmitted(),
                $this->getActionMethodName(),
                $this->contentObject
            );
            $finisher->initializeFinisher();
            $this->callFinisherMethods($finisher);
        } else {
            throw new \Exception('Finisher does not implement ' . $this->finisherInterface);
        }
    }

    /**
     * Call methods in finisher class
     *
     * @param AbstractFinisher $finisher
     * @return void
     */
    protected function callFinisherMethods(AbstractFinisher $finisher)
    {
        foreach (get_class_methods($finisher) as $method) {
            if (!StringUtility::endsWith($method, 'Finisher') || strpos($method, 'initialize') === 0) {
                continue;
            }
            $this->callInitializeFinisherMethod($finisher, $method);
            $finisher->{$method}();
        }
    }

    /**
     * Call initializeFinisherMethods like "initializeSaveFinisher()"
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
     * @param Mail $mail
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(Mail $mail, array $settings, ContentObjectRenderer $contentObject)
    {
        $this->setMail($mail);
        $this->setSettings($settings);
        $this->contentObject = $contentObject;
    }
}
