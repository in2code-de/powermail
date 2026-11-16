<?php

declare(strict_types=1);

namespace In2code\Powermail\Update;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\DatabaseUtility;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * If there are any TEXT legacy fields in the database, that have a NULL value
 * update them with an empty string.
 */
#[UpgradeWizard('powermailTextNullUpdateWizard')]
class PowermailTextNullUpdateWizard implements UpgradeWizardInterface, ChattyInterface
{
    protected const TEXT_FIELDS = [
        Field::TABLE_NAME => [
            'settings',
            'text',
            'prefill_value',
            'placeholder',
            'placeholder_repeat',
            'create_from_typoscript',
        ],
        Mail::TABLE_NAME => [
            'body',
            'user_agent',
            'marketing_referer_domain',
            'marketing_referer',
            'marketing_country',
            'marketing_browser_language',
            'marketing_page_funnel',
        ],
        Answer::TABLE_NAME => [
            'value',
        ],
    ];

    protected ?OutputInterface $output = null;

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

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function executeUpdate(): bool
    {
        try {
            foreach (self::TEXT_FIELDS as $table => $fields) {
                foreach ($fields as $field) {
                    $queryBuilder = DatabaseUtility::getQueryBuilderForTable($table, true);
                    $queryBuilder
                        ->update($table)
                        ->set($field, '')
                        ->where($queryBuilder->expr()->isNull($field))
                        ->executeStatement();
                }
            }
        } catch (Throwable $exception) {
            $this->output?->writeln('<error>' . $exception->getMessage() . '</error>');
            return false;
        }
        return true;
    }

    public function updateNecessary(): bool
    {
        foreach (self::TEXT_FIELDS as $table => $fields) {
            foreach ($fields as $field) {
                if ($this->hasNullValues($table, $field)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    protected function hasNullValues(string $table, string $field): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable($table, true);
        return (int)$queryBuilder
            ->count('uid')
            ->from($table)
            ->where($queryBuilder->expr()->isNull($field))
            ->executeQuery()
            ->fetchOne() > 0;
    }
}
