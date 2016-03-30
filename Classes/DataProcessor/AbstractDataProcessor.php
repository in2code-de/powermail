<?php
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Domain\Model\Mail;
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
 * Class AbstractDataProcessor
 * @package In2code\Powermail\Finisher
 */
abstract class AbstractDataProcessor implements DataProcessorInterface
{

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * Extension settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Finisher service configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Controller actionName - usually "createAction" or "confirmationAction"
     *
     * @var null
     */
    protected $actionMethodName = null;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param Mail $mail
     * @return AbstractDataProcessor
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
     * @return AbstractDataProcessor
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
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
     * @return AbstractDataProcessor
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
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
     * @return AbstractDataProcessor
     */
    public function setActionMethodName($actionMethodName)
    {
        $this->actionMethodName = $actionMethodName;
        return $this;
    }

    /**
     * @return void
     */
    public function initializeDataProcessor()
    {
    }

    /**
     * @param Mail $mail
     * @param array $configuration
     * @param array $settings
     * @param ContentObjectRenderer $contentObject
     * @param string $actionMethodName
     */
    public function __construct(
        Mail $mail,
        array $configuration,
        array $settings,
        $actionMethodName,
        ContentObjectRenderer $contentObject
    ) {
        $this->setMail($mail);
        $this->setConfiguration($configuration);
        $this->setSettings($settings);
        $this->setActionMethodName($actionMethodName);
        $this->contentObject = $contentObject;
    }
}
