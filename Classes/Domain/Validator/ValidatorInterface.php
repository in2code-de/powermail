<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;

/**
 * Interface Validator
 */
interface ValidatorInterface
{

    /**
     * @param Field $field
     * @param string $label
     * @return void
     */
    public function setErrorAndMessage(Field $field, string $label): void;

    /**
     * @return bool
     */
    public function isServerValidationEnabled(): bool;

    /**
     * @param bool $validState
     * @return void
     */
    public function setValidState(bool $validState): void;

    /**
     * @return bool
     */
    public function isValidState(): bool;

    /**
     * @param array $configuration
     * @return ValidatorInterface
     */
    public function setConfiguration(array $configuration): ValidatorInterface;

    /**
     * @return array
     */
    public function getConfiguration(): array;
}
