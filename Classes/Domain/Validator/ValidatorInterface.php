<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator;

use In2code\Powermail\Domain\Model\Field;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
    public function setErrorAndMessage(Field $field, $label);

    /**
     * @return bool
     */
    public function isServerValidationEnabled();

    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectTypoScript(ConfigurationManagerInterface $configurationManager);

    /**
     * @return void
     */
    public function setValidState($validState);

    /**
     * @return boolean
     */
    public function isValidState();

    /**
     * @param array $configuration
     * @return AbstractValidator
     */
    public function setConfiguration(array $configuration);

    /**
     * @return array
     */
    public function getConfiguration();
}
