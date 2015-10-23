<?php
namespace In2code\Powermail\ViewHelpers\Condition;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\FrontendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Check if logged in User is allowed to edit
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class IsAllowedToEditViewHelper extends AbstractViewHelper
{

    /**
     * Check if logged in User is allowed to edit
     *
     * @param Mail $mail
     * @param array $settings TypoScript and FlexForm Settings
     * @return bool
     */
    public function render($mail, $settings = array())
    {
        return FrontendUtility::isAllowedToEdit($settings, $mail);
    }

}
