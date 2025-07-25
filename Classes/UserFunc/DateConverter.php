<?php

declare(strict_types=1);
namespace In2code\Powermail\UserFunc;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class DateConverter
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
    protected $date;

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
     */
    public function convert(string $content = '', array $configuration = []): string
    {
        unset($content);
        $this->initialize($configuration);
        $this->createDateFromFormat();
        if ($this->getDate() instanceof \DateTime) {
            return $this->getDate()->format($this->getOutputFormat());
        }

        return '';
    }

    protected function createDateFromFormat(): void
    {
        $date = \DateTime::createFromFormat($this->getInputFormat(), $this->getInput());
        if ($date !== false) {
            $this->setDate($date);
        }
    }

    /**
     * Init function
     */
    protected function initialize(array $configuration): void
    {
        $this->configuration = $configuration;
        $this->setInput()->setInputFormat()->setOutputFormat();
    }

    protected function setInput(): DateConverter
    {
        $input = $this->cObj->cObjGetSingle($this->configuration['input'], $this->configuration['input.']);
        if (!empty($input)) {
            $this->input = $input;
        }

        return $this;
    }

    public function getInput(): string
    {
        return $this->input;
    }

    protected function setInputFormat(): DateConverter
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

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    protected function setOutputFormat(): DateConverter
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

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }
}
