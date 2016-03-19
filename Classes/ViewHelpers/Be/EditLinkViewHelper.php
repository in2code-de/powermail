<?php
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EditLinkViewHelper
 * @package In2code\Powermail\ViewHelpers\Be
 */
class EditLinkViewHelper extends AbstractViewHelper
{

    /**
     * Short writings of table names
     *
     * @var array
     */
    protected $tables = [
        'form' => Form::TABLE_NAME,
        'mail' => Mail::TABLE_NAME
    ];

    /**
     * Create backend edit links
     *
     * @param string $table
     * @param int $identifier
     * @return string
     */
    public function render($table, $identifier)
    {
        if (array_key_exists($table, $this->tables)) {
            $table = $this->tables[$table];
        }
        return BackendUtility::createEditUri($table, $identifier);
    }
}
