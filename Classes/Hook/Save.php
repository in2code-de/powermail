<?php
declare(strict_types=1);
namespace In2code\Powermail\Hook;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Save to reset mandatory of changed fields
 */
class Save
{

    /**
     * Hook action
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     * @return void
     */
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
        foreach ($pObj->datamap as $table => $data) {
            if ($table == 'tx_powermail_domain_model_form') {
                foreach ($data as $id => $fields) {
                    if (strpos((string) $id, 'NEW') === false) {
                        // Get pages
                        $pagesUids = $this->getPages($id);

                        // Get fields
                        $fields = $this->getFields($pagesUids);

                        // Update fields
                        if ($fields) {
                            $this->updateFields($fields);
                        }
                    }
                }
            } elseif ($table == 'tx_powermail_domain_model_page') {
                foreach ($data as $id => $fields) {
                    if (strpos((string) $id, 'NEW') === false) {
                        // Get fields
                        $fields = $this->getFields([
                            $id,
                        ]);

                        // Update fields
                        if ($fields) {
                            $this->updateFields($fields);
                        }
                    }
                }
            } elseif ($table == 'tx_powermail_domain_model_field') {
                foreach ($data as $id => $fields) {
                    if (strpos((string) $id, 'NEW') === false) {
                        // Get field
                        $field = $this->getField($id);

                        // Update field
                        if ($field) {
                            $this->updateFields($field);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get pages
     *
     * @param int $id
     * @return array
     */
    private function getPages($id)
    {
        $ids = [];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_powermail_domain_model_page');
        $rows = $queryBuilder
            ->select('uid')
            ->from('tx_powermail_domain_model_page')
            ->where(
                $queryBuilder->expr()->eq('forms', (int) $id)
            )
            ->execute()
            ->fetchAll();

        foreach ($rows as $row) {
            $ids[] = $row['uid'];
        }

        return $ids;
    }

    /**
     * Get fields
     *
     * @param array $ids
     * @return array
     */
    private function getFields($ids)
    {
        $fields = [];
        foreach ($ids as $pUid) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_powermail_domain_model_field');
            $rows = $queryBuilder
                ->select('uid', 'type', 'mandatory')
                ->from('tx_powermail_domain_model_field')
                ->where(
                    $queryBuilder->expr()->eq('pages', (int) $pUid)
                )
                ->execute()
                ->fetchAll();

            foreach ($rows as $row) {
                // Check
                $check = $this->checkType($row['type']);

                if ($check) {
                    $fields[] = [
                        'uid' => $row['uid'],
                        'mandatory' => $row['mandatory'],
                    ];
                }
            }
        }

        return $fields;
    }

    /**
     * Get field
     *
     * @param string $field
     * @return array
     */
    private function getField($field)
    {
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_powermail_domain_model_field')
            ->select(
                ['uid', 'type', 'mandatory'],
                'tx_powermail_domain_model_field',
                ['uid' => (int) $field]
            )
            ->fetch();

        // Check
        $check = $this->checkType($row['type']);

        if ($check) {
            $fieldArr[] = [
                'uid' => $row['uid'],
                'mandatory' => $row['mandatory'],
            ];

            return $fieldArr;
        }
    }

    /**
     * Check type
     *
     * @param string $type
     * @return bool TRUE if type is checked, FALSE otherwise
     */
    private function checkType($type)
    {
        $checkTypes = [
            'submit',
            'captcha',
            'reset',
            'text',
            'content',
            'html',
            'hidden',
            'location',
            'typoscript',
        ];

        if (in_array($type, $checkTypes)) {
            return true;
        }
    }

    /**
     * Update fields
     *
     * @param array $fields
     * @return void
     */
    private function updateFields($fields)
    {
        foreach ($fields as $field) {
            if ($field['mandatory']) {
                GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_powermail_domain_model_field')
                    ->update(
                        'tx_powermail_domain_model_field',
                        ['mandatory' => 0],
                        ['uid' => (int) $field['uid']]
                    );
            }
        }
    }

}