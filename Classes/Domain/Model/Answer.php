<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Answer
 */
class Answer extends AbstractEntity
{

    const TABLE_NAME = 'tx_powermail_domain_model_answer';
    const VALUE_TYPE_TEXT = 0;
    const VALUE_TYPE_ARRAY = 1;
    const VALUE_TYPE_DATE = 2;
    const VALUE_TYPE_UPLOAD = 3;

    /**
     * @var string
     */
    protected $value = '';

    /**
     * valueType
     *      0 => text
     *      1 => array
     *      2 => date
     *      3 => upload
     *
     * @var int
     */
    protected $valueType = null;

    /**
     * @var \In2code\Powermail\Domain\Model\Mail
     */
    protected $mail = null;

    /**
     * @var \In2code\Powermail\Domain\Model\Field
     */
    protected $field = null;

    /**
     * @return mixed $value
     */
    public function getValue()
    {
        $value = $this->value;

        // if serialized, change to array
        if (ArrayUtility::isJsonArray($this->value)) {
            // only if type multivalue or upload
            if ($this->getValueType() === self::VALUE_TYPE_ARRAY || $this->getValueType() === self::VALUE_TYPE_UPLOAD) {
                $value = json_decode($value, true);
            }
        }

        if ($this->isTypeDateForTimestamp($value)) {
            $value = date(
                LocalizationUtility::translate('datepicker_format_' . $this->getField()->getDatepickerSettings()),
                (int)$value
            );
        }

        if ($this->isTypeMultiple($value)) {
            $value = (empty($value) ? [] : [strval($value)]);
        }

        return $value;
    }

    /**
     * Sets the value
     *
     * @param mixed $value
     * @return Answer
     */
    public function setValue($value)
    {
        $value = $this->convertToJson($value);
        $value = $this->convertToTimestamp($value);
        $this->value = $value;
        return $this;
    }

    /**
     * Returns value and enforces a string
     *        An array will be returned as commaseparated string
     *
     * @param string $glue
     * @return string
     */
    public function getStringValue($glue = ', ')
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $value = implode($glue, $value);
        }
        return (string) $value;
    }

    /**
     * Returns raw value - could be
     *        - Same as getValue()
     *        - Timestamp (Date fields) instead of human readable date
     *        - JSON string for multiple fields instead of array
     *
     * @return string
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @param int $valueType
     * @return Answer
     */
    public function setValueType($valueType)
    {
        $this->valueType = (int)$valueType;
        return $this;
    }

    /**
     * @return int
     */
    public function getValueType()
    {
        if ($this->valueType === null) {
            if ($this->getField() !== null) {
                $field = $this->getField();
                $this->setValueType($field->dataTypeFromFieldType($field->getType()));
            } else {
                $this->setValue(0);
            }
        }
        return $this->valueType;
    }

    /**
     * Returns the mail
     *
     * @return Mail $mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Sets the mail
     *
     * @param Mail $mail
     * @return Answer
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * Returns the field
     *
     * @return Field $field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Sets the field
     *
     * @param Field $field
     * @return Answer
     */
    public function setField(Field $field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isTypeDateForTimestamp($value)
    {
        return $this->getValueType() === self::VALUE_TYPE_DATE && is_numeric($value) && $this->getField() !== null;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isTypeDateForDate($value)
    {
        return !empty($value) && method_exists($this->getField(), 'getType')
            && $this->getValueType() === self::VALUE_TYPE_DATE && !is_numeric($value);
    }

    /**
     * If multitext or upload force array
     *
     * @param string $value
     * @return bool
     */
    protected function isTypeMultiple($value)
    {
        return ($this->getValueType() === self::VALUE_TYPE_ARRAY || $this->getValueType() === self::VALUE_TYPE_UPLOAD)
            && !is_array($value);
    }

    /**
     * If array, encode to JSON string
     *
     * @param string|array $value
     * @return string
     */
    protected function convertToJson($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $value;
    }

    /**
     * Convert string to timestamp for date fields (datepicker)
     *
     * @param $value
     * @return int|string
     */
    protected function convertToTimestamp($value)
    {
        if ($this->isTypeDateForDate($value)) {
            if (empty($this->translateFormat)) {
                $format = LocalizationUtility::translate(
                    'datepicker_format_' . $this->getField()->getDatepickerSettings()
                );
            } else {
                $format = $this->translateFormat;
            }
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                if ($this->getField()->getDatepickerSettings() === 'date') {
                    $date->setTime(0, 0, 0);
                }
                $value = $date->getTimestamp();
            } else {
                try {
                    // fallback html5 date field - always Y-m-d H:i
                    $date = new \DateTime($value);
                } catch (\Exception $e) {
                    // clean value if string could not be converted
                    $value = '';
                }
                if ($date) {
                    if ($this->getField()->getDatepickerSettings() === 'date') {
                        $date->setTime(0, 0, 0);
                    }
                    $value = $date->getTimestamp();
                }
            }
        }
        return $value;
    }
}
