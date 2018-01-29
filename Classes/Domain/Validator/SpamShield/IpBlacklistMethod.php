<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpBlacklistMethod
 */
class IpBlacklistMethod extends AbstractMethod
{

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Blacklist IP-Address Check: Check if Senders IP is blacklisted
     *
     * @return bool true if spam recognized
     */
    public function spamCheck()
    {
        return in_array(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $this->getValues());
    }

    /**
     * Get blacklisted values
     *
     * @return array
     */
    protected function getValues()
    {
        $values = ObjectUtility::getContentObject()->cObjGetSingle(
            $this->configuration['values']['_typoScriptNodeValue'],
            $this->configuration['values']
        );
        return GeneralUtility::trimExplode($this->delimiter, $this->reduceDelimiters($values), true);
    }

    /**
     * reduce ; and "\n" to ,
     *
     * @param string $string
     * @return string
     */
    protected function reduceDelimiters($string)
    {
        return str_replace([',', ';', ' ', PHP_EOL], $this->delimiter, $string);
    }
}
