<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;

/**
 * Interface Validator
 */
interface ValidatorInterface
{
    public function setErrorAndMessage(Field $field, string $label): void;

    public function isServerValidationEnabled(): bool;

    public function setValidState(bool $validState): void;

    public function isValidState(): bool;

    public function setConfiguration(array $configuration): ValidatorInterface;

    public function getConfiguration(): array;
}
