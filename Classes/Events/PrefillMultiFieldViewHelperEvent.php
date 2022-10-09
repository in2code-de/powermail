<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;

final class PrefillMultiFieldViewHelperEvent
{
    /**
     * @var bool
     */
    protected bool $selected;

    /**
     * @var Field
     */
    protected Field $field;

    /**
     * @var ?Mail
     */
    protected ?Mail $mail;

    /**
     * @var int
     */
    protected int $cycle;

    /**
     * @var bool
     */
    protected bool $default;

    /**
     * @param bool $selected
     * @param Field $field
     * @param ?Mail $mail
     * @param int $cycle
     * @param bool $default
     */
    public function __construct(bool $selected, Field $field, ?Mail $mail, int $cycle, bool $default)
    {
        $this->selected = $selected;
        $this->field = $field;
        $this->mail = $mail;
        $this->cycle = $cycle;
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     * @return PrefillMultiFieldViewHelperEvent
     */
    public function setSelected(bool $selected): PrefillMultiFieldViewHelperEvent
    {
        $this->selected = $selected;
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
     * @return int
     */
    public function getCycle(): int
    {
        return $this->cycle;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }
}
