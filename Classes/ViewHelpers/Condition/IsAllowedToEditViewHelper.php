<?php
declare(strict_types = 1);
namespace In2code\Powermail\ViewHelpers\Condition;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IsAllowedToEditViewHelper
 */
class IsAllowedToEditViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('mail', Mail::class, 'Mail object', true);
        $this->registerArgument('settings', 'array', 'TypoScript settings', false, []);
    }

    /**
     * Check if logged in User is allowed to edit
     *
     * @return bool
     * @throws DBALException
     * @throws Exception
     */
    public function render(): bool
    {
        return FrontendUtility::isAllowedToEdit($this->arguments['settings'], $this->arguments['mail']);
    }
}
