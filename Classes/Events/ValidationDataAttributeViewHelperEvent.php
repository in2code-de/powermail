<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;

final class ValidationDataAttributeViewHelperEvent
{
    public function __construct(protected array $additionalAttributes, protected Field $field, protected array $iteration)
    {
    }

    public function getAdditionalAttributes(): array
    {
        return $this->additionalAttributes;
    }

    public function setAdditionalAttributes(array $additionalAttributes): ValidationDataAttributeViewHelperEvent
    {
        $this->additionalAttributes = $additionalAttributes;
        return $this;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function getIteration(): array
    {
        return $this->iteration;
    }

    public function setIteration(array $iteration): ValidationDataAttributeViewHelperEvent
    {
        $this->iteration = $iteration;
        return $this;
    }
}
