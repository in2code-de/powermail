<?php

declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Getter;

use In2code\Powermail\Domain\Repository\FieldRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFieldLabelFromUidViewHelper
 */
class GetFieldLabelFromUidViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'UID', true);
    }

    /**
     * get tx_powermail_domain_model_field.title from .uid
     */
    public function render(): string
    {
        $result = '';
        $fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
        $field = $fieldRepository->findByUid($this->arguments['uid']);
        if (method_exists($field, 'getTitle')) {
            return $field->getTitle();
        }

        return $result;
    }
}
