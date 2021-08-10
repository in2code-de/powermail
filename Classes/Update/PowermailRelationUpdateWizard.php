<?php
declare(strict_types = 1);
namespace In2code\Powermail\Update;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Page;
use In2code\Powermail\Exception\DatabaseFieldMissingException;
use In2code\Powermail\Utility\DatabaseUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class PowermailRelationUpdateWizard
 * to copy values of
 * - tx_powermail_domain_model_field.pages to .page and
 * - tx_powermail_domain_model_pages.forms to .form
 */
class PowermailRelationUpdateWizard implements UpgradeWizardInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'powermailRelationUpdateWizard';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Powermail: Update relations in database (relevant for entries from < 8.0.0)';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Basicly this upgrade wizard copies values from tx_powermail_domain_model_field.pages to .page and ' .
            'tx_powermail_domain_model_pages.forms to .form with two simple queries';
    }

    /**
     * @return bool
     */
    public function executeUpdate(): bool
    {
        try {
            $connection = DatabaseUtility::getConnectionForTable(Field::TABLE_NAME);
            $connection->executeQuery('update ' . Field::TABLE_NAME . ' set page=pages;');
            $connection = DatabaseUtility::getConnectionForTable(Page::TABLE_NAME);
            $connection->executeQuery('update ' . Page::TABLE_NAME . ' set form=forms;');
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws DBALException
     * @throws DatabaseFieldMissingException
     */
    public function updateNecessary(): bool
    {
        return $this->areOldFieldsExistingAndFilled() && $this->areNewFieldsEmpty();
    }

    /**
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
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
    protected function areNewFieldsEmpty(): bool
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
