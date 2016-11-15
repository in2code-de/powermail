<?php
namespace In2code\Powermail\Command;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Service\ExportService;
use In2code\Powermail\Domain\Service\GetNewMarkerNamesForFormService;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alex Kellner <alexander.kellner@in2code.de>, in2code.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Controller for powermail tasks
 *
 * @package powermail
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class TaskCommandController extends CommandController
{

    /**
     * @var \In2code\Powermail\Domain\Repository\MailRepository
     * @inject
     */
    protected $mailRepository;

    /**
     * @var \In2code\Powermail\Domain\Repository\AnswerRepository
     * @inject
     */
    protected $answerRepository;

    /**
     * delete Files which are older than this seconds
     *
     * @var int
     */
    protected $period = 3600;

    /**
     * Export of mails as email attachment
     *
     *        This task can send a mail export with an attachment
     *        (XLS or CSV) to a receiver or a group of receivers
     *
     * @param string $receiverEmails comma separated email addresses for export
     * @param string $senderEmail sender email address
     * @param string $subject Mail subject
     * @param int $pageUid Page Id with existing mails
     * @param string $domain Domainname for linkgeneration
     * @param int $period Select mails that are not older than this seconds
     * @param boolean $attachment Add export file as attachment to mail
     * @param string $fieldList Define needed fields with a commasepareted uid list (empty = all default fields)
     * @param string $format Fileformat can be 'xls' or 'csv'
     * @param string $storageFolder path where to save export file
     * @param string $fileName Define a fix filename without extension (empty = random filename)
     * @param string $emailTemplate path and filename of email template
     * @return bool
     */
    public function exportCommand(
        $receiverEmails,
        $senderEmail = 'sender@domain.org',
        $subject = 'New mail export',
        $pageUid = 0,
        $domain = 'http://www.domain.org/',
        $period = 2592000,
        $attachment = true,
        $fieldList = '',
        $format = 'xls',
        $storageFolder = 'typo3temp/tx_powermail/',
        $fileName = null,
        $emailTemplate = 'EXT:powermail/Resources/Private/Templates/Module/ExportTaskMail.html'
    ) {
        /** @var ExportService $exportService */
        $exportService = $this->objectManager->get(
            ExportService::class,
            $this->mailRepository->findAllInPid($pageUid, [], $this->getFilterVariables($period)),
            $format,
            ['domain' => $domain]
        );
        $exportService
            ->setReceiverEmails($receiverEmails)
            ->setSenderEmails($senderEmail)
            ->setSubject($subject)
            ->setFieldList($fieldList)
            ->setAddAttachment($attachment)
            ->setStorageFolder($storageFolder)
            ->setFileName($fileName)
            ->setEmailTemplate($emailTemplate);
        return $exportService->send();
    }

    /**
     * Remove unused uploaded Files with a scheduler task
     *
     *        This task can clean up unused uploaded files
     *        with powermail from your server
     *
     * @param string $uploadPath Define the upload Path
     * @return void
     */
    public function cleanUnusedUploadsCommand($uploadPath = 'uploads/tx_powermail/')
    {
        $usedUploads = $this->getUsedUploads();
        $allUploads = BasicFileUtility::getFilesFromRelativePath($uploadPath);
        $removeCounter = 0;
        foreach ($allUploads as $upload) {
            if (!in_array($upload, $usedUploads)) {
                $absoluteFilePath = GeneralUtility::getFileAbsFileName($uploadPath . $upload);
                if (filemtime($absoluteFilePath) < (time() - $this->period)) {
                    unlink($absoluteFilePath);
                    $removeCounter++;
                }
            }
        }
        $this->outputLine('Overall Files: ' . count($allUploads));
        $this->outputLine('Removed Files: ' . $removeCounter);
    }

    /**
     * Remove all uploaded files in uploads/tx_powermail/
     *
     *        This task will clean up all (!) files which
     *        are located in uploads/tx_powermail/
     *
     * @param int $period Define how old the files could be (in seconds) that should be deleted (0 = delete all)
     * @return void
     */
    public function cleanUploadsFilesCommand($period = 0)
    {
        $this->removeFilesFromRelativeDirectory('uploads/tx_powermail/', $period);
    }

    /**
     * Remove all export files in typo3temp/tx_powermail/
     *
     *        This task will clean up all (!) files which
     *        are located in typo3temp/tx_powermail/
     *        e.g.: old captcha images and old export files
     *        (from export task - if stored in typo3temp folder)
     *
     * @param int $period Define how old the files could be (in seconds) that should be deleted (0 = delete all)
     * @return void
     */
    public function cleanExportFilesCommand($period = 0)
    {
        $this->removeFilesFromRelativeDirectory('typo3temp/tx_powermail/', $period);
    }

    /**
     * Reset all markers in fields within a given form
     *
     *      Reset all marker names in fields if there are broken
     *      Fields without or duplicated markernames.
     *      Note: Only non-hidden and non-deleted fields
     *      in non-hidden and non-deleted pages will be respected.
     *      Attention: If you add "0" as form Uid, all fields in all
     *      forms will be resetted!
     *
     * @param int $formUid Add the form uid, 0 resets markers of all forms
     * @param boolean $forceReset Force to reset markers even if they are already filled
     * @return void
     */
    public function resetMarkerNamesInFormCommand($formUid, $forceReset)
    {
        /** @var GetNewMarkerNamesForFormService $markerService */
        $markerService = $this->objectManager->get(GetNewMarkerNamesForFormService::class);
        $markers = $markerService->getMarkersForFieldsDependingOnForm($formUid, $forceReset);
        foreach ($markers as $formMarkers) {
            foreach ($formMarkers as $uid => $marker) {
                ObjectUtility::getDatabaseConnection()->exec_UPDATEquery(
                    Field::TABLE_NAME,
                    'uid = ' . (int)$uid,
                    ['marker' => $marker]
                );
            }
        }
    }

    /**
     * Remove all files from a directory
     *
     * @param string $directory relative directory
     * @param int $period Define how old the files could be (in seconds) that should be deleted (0 = delete all)
     * @return void
     */
    protected function removeFilesFromRelativeDirectory($directory, $period = 0)
    {
        $files = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName($directory), '', true);
        $counter = 0;
        foreach ($files as $file) {
            if ($period === 0 || ($period > 0 && (time() - filemtime($file) > $period))) {
                $counter++;
                unlink($file);
            }
        }
        $this->outputLine($counter . ' files removed from your system');
    }

    /**
     * Get used uploads
     *
     * @return array
     */
    protected function getUsedUploads()
    {
        $answers = $this->answerRepository->findByAnyUpload();
        $usedUploads = [];
        foreach ($answers as $answer) {
            foreach ((array)$answer->getValue() as $singleUpload) {
                $usedUploads[] = $singleUpload;
            }
        }
        return $usedUploads;
    }

    /**
     * Create a filter array from given period
     *
     * @param int $period
     * @return array
     */
    protected function getFilterVariables($period)
    {
        $variables = ['filter' => []];
        if ($period > 0) {
            $variables = [
                'filter' => [
                    'start' => strftime('%Y-%m-%d %H:%M:%S', (time() - $period)),
                    'stop' => 'now'
                ]
            ];
        }
        return $variables;
    }
}
