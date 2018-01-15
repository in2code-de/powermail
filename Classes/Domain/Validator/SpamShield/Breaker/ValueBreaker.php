<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use In2code\Powermail\Domain\Model\Answer;

/**
 * Class ValueBreaker
 */
class ValueBreaker extends AbstractBreaker
{

    /**
     * @return bool
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
     */
    protected function checkConfiguration(array $configuration)
    {
        if (empty($configuration['value'])) {
            throw new \UnexpectedValueException('No value given to check for', 1516025541289);
        }
    }
}
