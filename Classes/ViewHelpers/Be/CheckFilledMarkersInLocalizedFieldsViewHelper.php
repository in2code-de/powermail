<?php
namespace In2code\Powermail\ViewHelpers\Be;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Backend Check Viewhelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class CheckFilledMarkersInLocalizedFieldsViewHelper extends AbstractViewHelper
{

    /**
     * fieldRepository
     *
     * @var \In2code\Powermail\Domain\Repository\FieldRepository
     * @inject
     */
    protected $fieldRepository;

    /**
     * Check if there are localized records with
     *        tx_powermail_domain_model_field.markers != ""
     *
     * @return bool
     */
    public function render()
    {
        $forms = $this->fieldRepository->findAllFieldsWithFilledMarkerrsInLocalizedFields();
        if (count($forms) > 0) {
            return false;
        }
        $forms = $this->fieldRepository->findAllWrongLocalizedFields();
        if (count($forms) > 0) {
            return false;
        }
        return true;
    }
}
