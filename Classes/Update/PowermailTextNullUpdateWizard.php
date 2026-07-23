<?php

declare(strict_types=1);

namespace In2code\Powermail\Update;

use In2code\Powermail\Utility\DatabaseUtility;
use Throwable;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * If there are any TEXT legacy fields in the database, that have a NULL value
 * update them with an empty string.
 */
#[UpgradeWizard('powermailTextNullUpdateWizard')]
class PowermailTextNullUpdateWizard implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'powermailTextNullUpdateWizard';
    }

    public function getTitle(): string
    {
        return 'Powermail: Update all database TEXT fields that have a null value with a default value';
    }

    /**
     * Return the description for this wizard
     */
    public function getDescription(): string
    {
        return 'If there are any TEXT legacy fields in the database, that have a NULL value update them with '
        . 'an empty string';
    }

    public function executeUpdate(): bool
    {
        $tables = [
            'tx_powermail_domain_model_field' => [
                'settings',
                'text',
                'prefill_value',
                'placeholder',
                'placeholder_repeat',
                'create_from_typoscript',
            ],
            'tx_powermail_domain_model_mail' => [
                'body',
                'user_agent',
                'marketing_referer_domain',
                'marketing_referer',
                'marketing_country',
                'marketing_browser_language',
                'marketing_page_funnel',
            ],
            'tx_powermail_domain_model_answer' => [
                'value',
            ],
        ];
        try {
            foreach ($tables as $table => $fields) {
                foreach ($fields as $field) {
                    $connection = DatabaseUtility::getConnectionForTable($table);
                    $connection->executeQuery('UPDATE ' . $table . ' SET ' . $field . '="" WHERE ISNULL(' . $field . ');');
                }
            }
        } catch (Throwable) {
            return false;
        }
        return true;
    }

    public function updateNecessary(): bool
    {
        return true;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
