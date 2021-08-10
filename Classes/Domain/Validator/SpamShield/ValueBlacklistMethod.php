<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Validator\SpamShield;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class ValueBlacklistMethod
 */
class ValueBlacklistMethod extends AbstractMethod
{

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Blacklist String Check: Check if a blacklisted word is in given values
     *
     * @return bool true if spam recognized
     * @throws Exception
     */
    public function spamCheck(): bool
    {
        foreach ($this->mail->getAnswers() as $answer) {
            if (is_array($answer->getValue())) {
                continue;
            }
            foreach ($this->getValues() as $blackword) {
                if ($this->isStringInString($answer->getValue(), $blackword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get blacklisted values
     *
     * @return array
     * @throws Exception
     */
    protected function getValues(): array
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
    protected function reduceDelimiters(string $string): string
    {
        return str_replace([',', ';', ' ', PHP_EOL], $this->delimiter, $string);
    }

    /**
     * Find string in string but only if it stands alone
     * Search for "sex":
     *        "Sex" => TRUE
     *        "test sex test" => TRUE
     *        "Staatsexamen" => FALSE
     *        "_sex_bla" => TRUE
     *        "tst sex.seems.to.be.nice" => TRUE
     *        "email@sex.org" => TRUE
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function isStringInString(string $haystack, string $needle): bool
    {
        return preg_match('/(?:\A|[@\s\b_-]|\.)' . $needle . '(?:$|[\s\b_-]|\.)/i', $haystack) === 1;
    }
}
