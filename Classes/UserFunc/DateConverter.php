<?php
namespace In2code\Powermail\UserFunc;

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
 * DateConverter
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DateConverter
{

    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * UserFunc configuration TypoScript
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Value to convert
     *
     * @var string
     */
    protected $input = '';

    /**
     * input format
     *
     * @var string
     */
    protected $inputFormat = 'Y-m-d';

    /**
     * format to convert
     *
     * @var string
     */
    protected $outputFormat = 'd.m.Y';

    /**
     * @var \DateTime|null
     */
    protected $date = null;

    /**
     * UserFunc method to convert a datestring from one format into another
     *
     * Example:
     * # Convert 2015-12-31 into 1451516400
     * lib.test = USER
     * lib.test {
     *        userFunc = In2code\Powermail\UserFunc\DateConverter->convert
     *        includeLibs = EXT:powermail/Classes/UserFunc/DateConverter.php
     *
     *        input = TEXT
     *        input.value = 2015-12-31
     *
     *        inputFormat = TEXT
     *        inputFormat.value = Y-m-d
     *
     *        outputFormat = TEXT
     *        outputFormat.value = U
     * }
     *
     * @param string $content normally empty in userFuncs
     * @param array $configuration TypoScript configuration from userFunc
     * @return string
     */
    public function convert($content = '', $configuration = [])
    {
        $this->initialize($configuration);
        $this->createDateFromFormat();
        return $this->getDate()->format($this->getOutputFormat());
    }

    /**
     * Create date from format
     *
     * @return void
     */
    protected function createDateFromFormat()
    {
        $date = \DateTime::createFromFormat($this->getInputFormat(), $this->getInput());
        if ($date !== false) {
            $this->setDate($date);
        }
    }

    /**
     * Init function
     *
     * @param array $configuration
     * @return void
     */
    protected function initialize($configuration)
    {
        $this->configuration = $configuration;
        $this->setInput()->setInputFormat()->setOutputFormat();
    }

    /**
     * @return DateConverter
     */
    protected function setInput()
    {
        $input = $this->cObj->cObjGetSingle($this->configuration['input'], $this->configuration['input.']);
        if (!empty($input)) {
            $this->input = $input;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return DateConverter
     */
    protected function setInputFormat()
    {
        $inputFormat = $this->cObj->cObjGetSingle(
            $this->configuration['inputFormat'],
            $this->configuration['inputFormat.']
        );
        if (!empty($inputFormat)) {
            $this->inputFormat = $inputFormat;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getInputFormat()
    {
        return $this->inputFormat;
    }

    /**
     * @return DateConverter
     */
    protected function setOutputFormat()
    {
        $outputFormat = $this->cObj->cObjGetSingle(
            $this->configuration['outputFormat'],
            $this->configuration['outputFormat.']
        );
        if (!empty($outputFormat)) {
            $this->outputFormat = $outputFormat;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
