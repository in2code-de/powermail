<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Domain\Repository\FormRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CheckWrongLocalizedFormsViewHelper
 */
class CheckWrongLocalizedFormsViewHelper extends AbstractViewHelper
{

    /**
     * Check if there are localized records with
     *        tx_powermail_domain_model_form.pages = ""
     *
     * @return bool
     */
    public function render(): bool
    {
        $formRepository = ObjectUtility::getObjectManager()->get(FormRepository::class);
        $forms = $formRepository->findAllWrongLocalizedForms();
        return count($forms) === 0;
    }
}
