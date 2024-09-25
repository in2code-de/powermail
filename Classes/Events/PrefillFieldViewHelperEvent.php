<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;

final class PrefillFieldViewHelperEvent
{
    /**
     * @var string|array
     */
    protected $value;

    /**
     * @var Field
     */
    protected Field $field;

    /**
     * @var ?Mail
     */
    protected ?Mail $mail;

    /**
     * @var string|array|null
     */
    protected $default;

    /**
     * @param string|array $value
     * @param Field $field
     * @param ?Mail $mail
     * @param string|array|null $default
     */
    public function __construct($value, Field $field, ?Mail $mail, $default)
    {
        $this->value = $value;
        $this->field = $field;
        $this->mail = $mail;
        $this->default = $default;
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array|string $value
     * @return PrefillFieldViewHelperEvent
     */
    public function setValue($value): PrefillFieldViewHelperEvent
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @return ?Mail
     */
    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    /**
     * @return array|string|null
     */
    public function getDefault()
    {
        return $this->default;
    }
}
