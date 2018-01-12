<?php
declare(strict_types=1);
namespace In2code\Powermail\ViewHelpers\Be;

use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class LastUpdateExtensionRepositoryViewHelper
 */
class LastUpdateExtensionRepositoryViewHelper extends AbstractViewHelper
{
    const TABLE_NAME = 'tx_extensionmanager_domain_model_repository';

    /**
     * Return timestamp from last updated TER
     *
     * @return int
     */
    public function render(): int
    {
        if ($this->extensionTableExists()) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
            $rows = $queryBuilder->select('last_update')->from(self::TABLE_NAME)->execute()->fetchAll();
            if (!empty($rows[0]['last_update'])) {
                return $rows[0]['last_update'];
            }
        }
        return 0;
    }

    /**
     * @return bool
     */
    protected function extensionTableExists(): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->select('*')->from(self::TABLE_NAME);
        $tableExists = true;
        try {
            $queryBuilder->execute();
        } catch (\Exception $exception) {
            unset($exception);
            $tableExists = false;
        }
        return $tableExists;
    }
}
