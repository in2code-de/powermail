<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EditLinkViewHelper
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
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'Tablename', true);
        $this->registerArgument('identifier', 'int', 'Identifier', true);
    }

    /**
     * Create backend edit links
     *
     * @return string
     */
    public function render(): string
    {
        $table = $this->arguments['table'];
        $identifier = $this->arguments['identifier'];
        if (array_key_exists($table, $this->tables)) {
            $table = $this->tables[$table];
        }
        return BackendUtility::createEditUri($table, $identifier);
    }
}
