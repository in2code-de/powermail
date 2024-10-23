<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;

final class PrefillFieldViewHelperEvent
{
    /**
     * @param string|array $value
     * @param string|array|null $default
     */
    public function __construct(protected $value, protected Field $field, protected ?Mail $mail, protected $default)
    {
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
     */
    public function setValue($value): PrefillFieldViewHelperEvent
    {
        $this->value = $value;
        return $this;
    }

    public function getField(): Field
    {
        return $this->field;
    }

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
