<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield\Breaker;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpBreaker
 */
class IpBreaker extends AbstractBreaker
{

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        foreach ($this->getIpAddresses() as $ipAddress) {
            if ($this->isIpMatching(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $ipAddress)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $givenIp like "127.0.0.1"
     * @param string $ipRange like "127.0.0.1" or "192.168.*.*"
     * @return bool
     */
    protected function isIpMatching(string $givenIp, string $ipRange): bool
    {
        if (stristr($ipRange, '*')) {
            $rangeParts = explode('.', $ipRange);
            $givenParts = explode('.', $givenIp);
            if (count($rangeParts) !== count($givenParts)) {
                throw new \UnexpectedValueException(
                    'Number of segments between current ip and compared ip does not match',
                    1516024779382
                );
            }
            foreach (array_keys($rangeParts) as $key) {
                if ($rangeParts[$key] === '*') {
                    $givenParts[$key] = '*';
                }
            }
            $givenIp = implode('.', $givenParts);
        }
        return $givenIp === $ipRange;
    }

    /**
     * @return array
     */
    protected function getIpAddresses(): array
    {
        $configuration = $this->getConfiguration();
        if (empty($configuration['ipWhitelist'])) {
            throw new \UnexpectedValueException(
                'Setup ...spamshield.disable.NO.configuration.ipWhitelist not given',
                1516024283512
            );
        }
        return GeneralUtility::trimExplode(',', $configuration['ipWhitelist'], true);
    }
}
