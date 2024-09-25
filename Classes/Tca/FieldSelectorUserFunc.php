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
     * @param array $params
     * @return void
     * @throws DBALException
     */
    public function getFieldSelection(array &$params): void
    {
        $formRepository = GeneralUtility::makeInstance(FormRepository::class);
        $formUid = $this->getFormUidFromTtContentUid((int)$params['row']['uid']);
        if (!$formUid) {
            $params['items'] = [
                [
                    'Please select a form (Main Settings)',
                    '',
                ],
            ];
            return;
        }
        foreach ((array)$formRepository->getFieldsFromFormWithSelectQuery($formUid) as $field) {
            $params['items'][] = [
                $field['title'] . ' {' . $field['marker'] . '}',
                $field['uid'],
            ];
        }
    }

    /**
     * Return Form Uid from content element
     *
     * @param int $ttContentUid
     * @return int
     */
    protected function getFormUidFromTtContentUid(int $ttContentUid): int
    {
        $row = BackendUtilityCore::getRecord('tt_content', (int)$ttContentUid, 'pi_flexform', '', false);
        if (isset($row['pi_flexform'])) {
            $flexform = GeneralUtility::xml2array($row['pi_flexform']);
            if (isset($flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'])) {
                return (int)$flexform['data']['main']['lDEF']['settings.flexform.main.form']['vDEF'];
            }
        }
        return 0;
    }
}
