<?php
declare(strict_types=1);
namespace In2code\Powermail\DataProcessor;

use In2code\Powermail\Utility\SessionUtility;

/**
 * Class SessionDataProcessor
 */
class SessionDataProcessor extends AbstractDataProcessor
{

    /**
     * Save values to session to prefill forms if needed
     *
     * @return void
     */
    public function saveSessionDataProcessor()
    {
        if ($this->getActionMethodName() === 'createAction') {
            SessionUtility::saveSessionValuesForPrefill($this->getMail(), $this->getSettings());
        }
    }
}
