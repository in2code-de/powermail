<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Repository\FieldRepository;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFieldLabelFromUidViewHelper
 */
class GetFieldLabelFromUidViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'UID', true);
    }

    /**
     * get tx_powermail_domain_model_field.title from .uid
     *
     * @return string
     */
    public function render(): string
    {
        $result = '';
        $fieldRepository = ObjectUtility::getObjectManager()->get(FieldRepository::class);
        $field = $fieldRepository->findByUid($this->arguments['uid']);
        if (method_exists($field, 'getTitle')) {
            $result = $field->getTitle();
        }
        return $result;
    }
}
