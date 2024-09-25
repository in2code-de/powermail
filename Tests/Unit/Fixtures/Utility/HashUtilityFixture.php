<?php

namespace In2code\Powermail\Tests\Unit\Fixtures\Utility;

use In2code\Powermail\Exception\ConfigurationIsMissingException;
use In2code\Powermail\Utility\HashUtility;

/**
 * Class HashUtilityFixture
 */
class HashUtilityFixture extends HashUtility
{
    /**
     * @return string
     * @throws ConfigurationIsMissingException
     */
    public static function getEncryptionKeyForTesting()
    {
        return parent::getEncryptionKey();
    }
}
