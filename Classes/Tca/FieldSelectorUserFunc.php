<?php

declare(strict_types=1);
namespace In2code\Powermail\Tca;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Repository\FormRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldSelectorUserFunc
 * Powermail Field Selector for Pi2 (powermail_frontend) - used in FlexForm
 */
class FieldSelectorUserFunc
{
    /**
     * Create Array for Field Selector
     *
     * @throws DBALException
     */
    public function getFieldSelection(array &$params): void
    {
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        $formUid = $this->getFormUidFromTtContentUid((int)$params['row']['uid']);
        if ($formUid === 0) {
            $params['items'] = [
                [
                    'label' => 'Please select a form (Main Settings)',
                    'value' => '',
                ],
            ];
            return;
        }

        foreach ((array)$formRepository->getFieldsFromFormWithSelectQuery($formUid) as $field) {
            $params['items'][] = [
                'label' => $field['title'] . ' {' . $field['marker'] . '}',
                'value' => $field['uid'],
            ];
        }
    }

    /**
     * Return Form Uid from content element
     */
    protected function getFormUidFromTtContentUid(int $ttContentUid): int
    {
        $row = BackendUtilityCore::getRecord('tt_content', $ttContentUid, 'pi_flexform', '', false);
        if (isset($row['pi_flexform'])) {
            $flexform = GeneralUtility::xml2array($row['pi_flexform']);
            if (isset($flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
                return (int)$flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'];
            }
        }

        return 0;
    }
}
