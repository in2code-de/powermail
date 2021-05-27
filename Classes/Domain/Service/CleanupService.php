<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Driver\Statement;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class CleanupService
{
    /** @var array */
    protected $settings;

    /** @param ConfigurationManager $configurationManager */
    public function injectConfigurationManager(ConfigurationManager $configurationManager): void
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    public function deleteMailsOlderThanAgeInPid(int $age, int $pid = null): array
    {
        $tempPath = $this->createTemporaryFolder();
        $uploadPath = $this->getFileUploadFolder();

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                                    ->getConnectionForTable('tx_powermail_domain_model_mail');

        $statement = $this->selectMailsOlderThanAgeInPid($connection, $age, $pid);

        // Collect moved files to easily rollback changes in the FS if the DB transaction fails.
        $movedFiles = [];

        $stats = [
            'mails' => 0,
            'answers' => 0,
            'files' => 0,
        ];

        $connection->beginTransaction();

        foreach ($statement as $mail) {
            // Select answers to type "file" fields to identify the files that have to be removed.
            $subQuery = $connection->createQueryBuilder();
            $subQuery->getRestrictions()->removeAll();
            $subQuery->select('answer.value', 'answer.uid')
                     ->from('tx_powermail_domain_model_answer', 'answer')
                     ->leftJoin('answer', 'tx_powermail_domain_model_field', 'field', 'answer.field = field.uid')
                     ->where($subQuery->expr()->eq('answer.mail', $subQuery->createNamedParameter($mail['uid'])))
                     ->andWhere($subQuery->expr()->eq('field.type', $subQuery->createNamedParameter('file')));
            $subStatement = $subQuery->execute();
            foreach ($subStatement as $answer) {
                foreach (json_decode($answer['value']) as $fileName) {
                    $uploadedFile = $uploadPath . $fileName;
                    if (file_exists($uploadedFile)) {
                        $movedFiles[] = $fileName;
                        rename($uploadedFile, $tempPath . $fileName);
                        $stats['files']++;
                    }
                }
                $stats['answers'] += $connection->delete('tx_powermail_domain_model_answer', ['mail' => $mail['uid']]);
            }
            $stats['mails'] += $connection->delete('tx_powermail_domain_model_mail', ['uid' => $mail['uid']]);
        }

        try {
            $connection->commit();
        } catch (ConnectionException $exception) {
            foreach ($movedFiles as $fileName) {
                $uploadedFile = $uploadPath . $fileName;
                rename($tempPath . $fileName, $uploadedFile);
            }
            throw $exception;
        }

        // Don't use rmdir, it is very slow!
        exec('rm -rf ' . escapeshellarg($tempPath));

        return $stats;
    }

    protected function createTemporaryFolder(): string
    {
        do {
            $tempPath = rtrim(PATH_site, '/') . '/typo3temp/var/powermail/' . uniqid('cleanup_tmp_', true) . '/';
        } while (is_dir($tempPath));
        GeneralUtility::mkdir_deep($tempPath);
        return $tempPath;
    }

    protected function getFileUploadFolder(): string
    {
        return rtrim(PATH_site, '/') . '/' . trim($this->settings['uploadPath'], '/') . '/';
    }

    protected function selectMailsOlderThanAgeInPid(Connection $connection, int $age, ?int $pid): Statement
    {
        $query = $connection->createQueryBuilder();
        $query->getRestrictions()->removeAll();
        $crdate = $GLOBALS['EXEC_TIME'] - $age;
        $query->select('uid')
              ->from('tx_powermail_domain_model_mail')
              ->where($query->expr()->lte('crdate', $query->createNamedParameter($crdate)));
        if (null !== $pid) {
            $query->andWhere($query->expr()->eq('pid', $query->createNamedParameter($pid)));
        }
        return $query->execute();
    }
}
