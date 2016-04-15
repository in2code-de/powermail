<?php
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * PowermailVersionViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class LastUpdateExtensionRepositoryViewHelper extends AbstractViewHelper
{

    const TABLE_NAME = 'tx_extensionmanager_domain_model_repository';

    /**
     * Return timestamp from last updated TER
     *
     * @return int
     */
    public function render()
    {
        if (!$this->extensionTableExists()) {
            return 0;
        }
        $select = 'last_update';
        $from = self::TABLE_NAME;
        $where = 1;
        $res = ObjectUtility::getDatabaseConnection()->exec_SELECTquery($select, $from, $where, '', '', 1);
        if ($res) {
            $row = ObjectUtility::getDatabaseConnection()->sql_fetch_assoc($res);
            if (!empty($row['last_update'])) {
                return (int)$row['last_update'];
            }
        }
        return 0;
    }

    /**
     * @return bool
     */
    protected function extensionTableExists()
    {
        $allTables = ObjectUtility::getDatabaseConnection()->admin_get_tables();
        if (array_key_exists(self::TABLE_NAME, $allTables)) {
            return true;
        }
        return false;
    }
}
