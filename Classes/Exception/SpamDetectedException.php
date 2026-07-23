<?php

namespace In2code\Powermail\Exception;

use TYPO3\CMS\Core\Error\Http\AbstractClientErrorException;
use TYPO3\CMS\Core\Utility\HttpUtility;

class SpamDetectedException extends AbstractClientErrorException
{
    /**
     * @var array<string> HTTP Status Header lines
     */
    protected $statusHeaders = [HttpUtility::HTTP_STATUS_406];

    protected $title = 'Spam Detected';

    protected $message = 'The request cannot be fulfilled due to detected spam.';

    public function __construct($message = null, $code = 0)
    {
        if (!empty($message)) {
            $this->message = $message;
        }
        parent::__construct($this->statusHeaders, $this->message, $this->title, $code);
    }
}
