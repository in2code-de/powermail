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
        if ($this->getDate() !== null) {
            return $this->getDate()->format($this->getOutputFormat());
        }
        return '';
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
