<?php

declare(strict_types=1);
namespace In2code\Powermail\Update;

use Doctrine\DBAL\DBALException;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\DatabaseUtility;
use Throwable;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class PowermailLanguageUpdateWizard
 * to enforce
 * - tx_powermail_domain_model_mail.sys_language_uid=-1 and
 * - tx_powermail_domain_model_answer.sys_language_uid=-1
 * for existing values
 */
class PowermailLanguageUpdateWizard implements UpgradeWizardInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'powermailLanguageUpdateWizard';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Powermail: Update language settings in mails and answers (relevant for entries from < 8.0.0)';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Basicly this upgrade wizard set tx_powermail_domain_model_mail.sys_language_uid=-1 and ' .
            'tx_powermail_domain_model_answer.sys_language_uid=-1 for existing values';
    }

    /**
     * @return bool
     */
    public function executeUpdate(): bool
    {
        try {
            $connection = DatabaseUtility::getConnectionForTable(Mail::TABLE_NAME);
            $connection->executeQuery('update ' . Mail::TABLE_NAME . ' set sys_language_uid=-1;');
            $connection = DatabaseUtility::getConnectionForTable(Answer::TABLE_NAME);
            $connection->executeQuery('update ' . Answer::TABLE_NAME . ' set sys_language_uid=-1;');
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws DBALException
     */
    public function updateNecessary(): bool
    {
        return $this->areMailsExistingInWrongLanguage() || $this->areAnswersExistingInWrongLanguage();
    }

    /**
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * @return bool
     * @throws DBALException
     */
    protected function areMailsExistingInWrongLanguage(): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Mail::TABLE_NAME);
        return (int)$queryBuilder
            ->count('sys_language_uid')
            ->from(Mail::TABLE_NAME)
            ->where('sys_language_uid > -1')
            ->executeQuery()
            ->rowCount() > 0;
    }

    /**
     * @return bool
     * @throws DBALException
     */
    protected function areAnswersExistingInWrongLanguage(): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Answer::TABLE_NAME);
        return (int)$queryBuilder
            ->count('sys_language_uid')
            ->from(Answer::TABLE_NAME)
            ->where('sys_language_uid > -1')
            ->executeQuery()
            ->rowCount() > 0;
    }
}
