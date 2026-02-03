<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Finisher\RateLimitFinisher;
use In2code\Powermail\Storage\RateLimitStorage;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Limit the number of submissions in a given time frame.
 *
 * Marks the submission as spam if the rate limit has been reached.
 * Counting a submission against the rate limit is done in RateLimitFinisher.
 *
 * Exclusion of IP addresses is possible with a powermail breaker configuration.
 */
class RateLimitMethod extends AbstractMethod
{
    /**
     * Check if this form submission is limited or shall be allowed.
     *
     * @return bool true if spam recognized
     */
    public function spamCheck(): bool
    {
        $config = [
            'id'       => 'powermail-ratelimit',
            'policy'   => 'sliding_window',
            'limit'    => $this->getLimit(),
            'interval' => $this->getInterval(),
        ];

        $storage = GeneralUtility::makeInstance(RateLimitStorage::class);

        $factory = new RateLimiterFactory($config, $storage);

        $keyParts = $this->getRestrictionValues($this->getRestrictions());
        $key = implode('-', $keyParts);

        $limiter = $factory->create($key);
        RateLimitFinisher::markForConsumption($limiter);

        if ($limiter->consume(0)->getRemainingTokens() > 0) {
            return false;
        }

        //spam
        return true;
    }

    /**
     * Replace the restriction variables with their values
     *
     * @param string[] $restrictions
     *
     * @return string[]
     */
    protected function getRestrictionValues(array $restrictions): array
    {
        $answers = $this->mail->getAnswersByFieldMarker();

        $values = [];
        foreach ($restrictions as $restriction) {
            if ($restriction === '__ipAddress') {
                $values[$restriction] = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            } elseif ($restriction === '__formIdentifier') {
                $values[$restriction] = $this->mail->getForm()->getUid();
            } elseif ($restriction[0] === '{') {
                //form field
                $fieldName = substr($restriction, 1, -1);
                if (!isset($answers[$fieldName])) {
                    throw new \InvalidArgumentException('Form has no field with variable name ' . $fieldName, 1763046923);
                }
                $values[$restriction] = $answers[$fieldName]->getValue();
            } else {
                //hard-coded value
                $values[$restriction] = $restriction;
            }
        }

        return $values;
    }

    /**
     * Get the configured time interval in which the limit has to be adhered to
     */
    protected function getInterval(): string
    {
        $interval = $this->configuration['interval'];

        if ($interval === null) {
            throw new \InvalidArgumentException('Interval must be set!', 1671448702);
        }
        if (! \is_string($interval)) {
            throw new \InvalidArgumentException('Interval must be a string!', 1671448703);
        }

        if (@\DateInterval::createFromDateString($interval) === false) {
            // @todo Remove check and exception when compatibility of PHP >= 8.3
            // @see https://www.php.net/manual/de/class.datemalformedintervalstringexception.php
            throw new \InvalidArgumentException(
                \sprintf(
                    'Interval is not valid, "%s" given!',
                    $interval,
                ),
                1671448704,
            );
        }

        return $interval;
    }

    /**
     * Get how many form submissions are allowed within the time interval
     */
    protected function getLimit(): int
    {
        $limit = $this->configuration['limit'];

        if ($limit === null) {
            throw new \InvalidArgumentException('Limit must be set!', 1671449026);
        }

        if (! \is_numeric($limit)) {
            throw new \InvalidArgumentException('Limit must be numeric!', 1671449027);
        }

        $limit = (int)$limit;
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater than 0!', 1671449028);
        }

        return $limit;
    }

    /**
     * Get the list of properties that are used to identify the form
     *
     * Supported values:
     * - __ipAddress
     * - __formIdentifier
     * - {email} - Form field names
     * - foo - Hard-coded values
     *
     * @return string[]
     */
    protected function getRestrictions(): array
    {
        $restrictions = $this->configuration['restrictions'];

        if ($restrictions === null) {
            throw new \InvalidArgumentException('Restrictions must be set!', 1671727527);
        }

        if (! \is_array($restrictions)) {
            throw new \InvalidArgumentException('Restrictions must be an array!', 1671727528);
        }

        if ($restrictions === []) {
            throw new \InvalidArgumentException('Restrictions must not be an empty array!', 1671727529);
        }

        foreach ($restrictions as $restriction) {
            if (! \is_string($restriction)) {
                throw new \InvalidArgumentException('A single restrictions must be a string!', 1671727530);
            }
        }

        return \array_values($restrictions);
    }
}
