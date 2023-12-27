<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Field;

final class ValidationDataAttributeViewHelperEvent
{
    /**
     * @var array
     */
    protected array $additionalAttributes;

    /**
     * @var Field
     */
    protected Field $field;

    /**
     * @var array
     */
    protected array $iteration;

    /**
     * @param array $additionalAttributes
     * @param Field $field
     * @param array $iteration
     */
    public function __construct(array $additionalAttributes, Field $field, array $iteration)
    {
        $this->additionalAttributes = $additionalAttributes;
        $this->field = $field;
        $this->iteration = $iteration;
    }

    /**
     * @return array
     */
    public function getAdditionalAttributes(): array
    {
        return $this->additionalAttributes;
    }

    /**
     * @param array $additionalAttributes
     * @return ValidationDataAttributeViewHelperEvent
     */
    public function setAdditionalAttributes(array $additionalAttributes): ValidationDataAttributeViewHelperEvent
    {
        $this->additionalAttributes = $additionalAttributes;
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
     * @return array
     */
    public function getIteration(): array
    {
        return $this->iteration;
    }

    /**
     * @param array $iteration
     * @return ValidationDataAttributeViewHelperEvent
     */
    public function setIteration(array $iteration): ValidationDataAttributeViewHelperEvent
    {
        $this->iteration = $iteration;
        return $this;
    }
}
