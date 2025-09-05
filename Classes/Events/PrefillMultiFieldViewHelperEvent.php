<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;

final class PrefillMultiFieldViewHelperEvent
{
    public function __construct(protected bool $selected, protected Field $field, protected ?Mail $mail, protected int $cycle, protected bool $default)
    {
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): PrefillMultiFieldViewHelperEvent
    {
        $this->selected = $selected;
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

    public function getCycle(): int
    {
        return $this->cycle;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}
