<?php
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpBlacklistMethod
 * @package In2code\Powermail\Domain\Validator\SpamShield
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
     * @param int $indication Indication if check fails
     * @return int
     */
    public function spamCheck($indication = 3)
    {
        if ($indication) {
            if (in_array(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $this->getValues())) {
                return $indication;
            }
        }
        return 0;
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
