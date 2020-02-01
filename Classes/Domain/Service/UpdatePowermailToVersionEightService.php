<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Service;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Utility\DatabaseUtility;

/**
 * Class UpdatePowermailToVersionEightService
 * copies values of
 * tx_powermail_domain_model_field.pages to .page and
 * tx_powermail_domain_model_pages.forms to .form
 */
class UpdatePowermailToVersionEightService
{
    /**
     * @return bool
     * @throws DBALException
     * @throws DatabaseFieldMissingException
     */
    public function isUpdateNeeded(): bool
    {
        return $this->areOldFieldsExistingAndFilled() && $this->areNewFieldsEmptyOrNotExisting();
    }

    /**
     * @return void
     * @throws DBALException
     */
    public function updateDatastructure(): void
    {
        $connection = DatabaseUtility::getConnectionForTable(Field::TABLE_NAME);
        $connection->executeQuery('update ' . Field::TABLE_NAME . ' set page=pages;');
        $connection = DatabaseUtility::getConnectionForTable(Page::TABLE_NAME);
        $connection->executeQuery('update ' . Page::TABLE_NAME . ' set form=forms;');
    }

    /**
     * @return bool
     * @throws DBALException
     */
    protected function areOldFieldsExistingAndFilled(): bool
    {
        return DatabaseUtility::isFieldFilled('pages', Field::TABLE_NAME)
            && DatabaseUtility::isFieldFilled('forms', Page::TABLE_NAME);
    }

    /**
     * @return bool
     * @throws DBALException
     * @throws DatabaseFieldMissingException
     */
    protected function areNewFieldsEmptyOrNotExisting(): bool
    {
        if (DatabaseUtility::isFieldExistingInTable('page', Field::TABLE_NAME) === false) {
            throw new DatabaseFieldMissingException(
                'Field tx_powermail_domain_model_field.page is missing. Did you forget a database compare?',
                1580560323
            );
        }
        if (DatabaseUtility::isFieldExistingInTable('form', Page::TABLE_NAME) === false) {
            throw new DatabaseFieldMissingException(
                'Field tx_powermail_domain_model_page.form is missing. Did you forget a database compare?',
                1580560354
            );
        }
        return DatabaseUtility::isFieldFilled('page', Field::TABLE_NAME) === false &&
            DatabaseUtility::isFieldFilled('form', Page::TABLE_NAME) === false;
    }
}
