<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Exception\ConfigurationIsMissingException;

/**
 * Class ValueBreaker
 */
class ValueBreaker extends AbstractBreaker
{

    /**
     * @return bool
     * @throws ConfigurationIsMissingException
     */
    public function isDisabled(): bool
    {
        $configuration = $this->getConfiguration();
        $this->checkConfiguration($configuration);
        foreach ($this->getMail()->getAnswers() as $answer) {
            /** @var $answer Answer */
            if ($answer->getValue() === $configuration['value']) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $configuration
     * @return void
     * @throws ConfigurationIsMissingException
     */
    protected function checkConfiguration(array $configuration): void
    {
        if (empty($configuration['value'])) {
            throw new ConfigurationIsMissingException('No value given to check for', 1516025541289);
        }
    }
}
