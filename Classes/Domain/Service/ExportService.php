<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Service;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\BasicFileUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\StringUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class ExportService
 */
class ExportService
{

    /**
     * Contains mails for export
     *
     * @var QueryResult|null
     */
    protected $mails = null;

    /**
     * Receiver email addresses
     *
     * @var array
     */
    protected $receiverEmails = [];

    /**
     * Sender email addresses
     *
     * @var array
     */
    protected $senderEmails = [
        'powermail@domain.org'
    ];

    /**
     * Mail subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Export format can be 'xls' or 'csv'
     *
     * @var string
     */
    protected $format = 'xls';

    /**
     * Fields to export
     *    Can be empty for all fields
     *    can contain:
     *        field uids
     *        "crdate"
     *        "sender_name"
     *        "sender_mail"
     *        "receiver_mail"
     *        "subject"
     *        "marketing_referer_domain"
     *        "marketing_referer"
     *        "marketing_frontend_language"
     *        "marketing_browser_language"
     *        "marketing_country"
     *        "marketing_mobile_device"
     *        "marketing_page_funnel"
     *        "user_agent"
     *        "time"
     *        "sender_ip"
     *        "uid"
     *        "feuser"
     *
     * @var array
     */
    protected $fieldList = [];

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var array
     */
    protected $additionalProperties = [];

    /**
     * @var bool
     */
    protected $addAttachment = true;

    /**
     * @var string
     */
    protected $storageFolder = 'typo3temp/assets/tx_powermail/';

    /**
     * @var string
     */
    protected $emailTemplate = 'Module/ExportTaskMail.html';

    /**
     * @param QueryResultInterface|null $mails Given mails for export
     * @param string $format can be 'xls' or 'csv'
     * @param array $additionalProperties add additional properties
     */
    public function __construct(
        ?QueryResultInterface $mails = null,
        string $format = 'xls',
        array $additionalProperties = []
    ) {
        $this->setMails($mails);
        $this->setFormat($format);
        $this->setAdditionalProperties($additionalProperties);
        $this->setFieldList($this->getDefaultFieldListFromFirstMail($mails));
        $this->createRandomFileName();
    }

    /**
     * @return bool
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     */
    public function send(): bool
    {
        $result = $this->createExportFile();
        if (!$result) {
            return false;
        }
        return $this->sendEmail();
    }

    /**
     * Send the export mail
     *
     * @return bool
     * @throws \Exception
     */
    protected function sendEmail(): bool
    {
        $email = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $email->setTo($this->getReceiverEmails());
        $email->setFrom($this->getSenderEmails());
        $email->setSubject($this->getSubject());
        $email->html($this->createMailBody());
        if ($this->isAddAttachment()) {
            $email->attachFromPath($this->getAbsolutePathAndFileName());
        }
        $email->send();
        return $email->isSent();
    }

    /**
     * Create bodytext for export mail
     *
     * @return string
     * @throws \Exception
     */
    protected function createMailBody(): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->getEmailTemplate()));
        $standaloneView->assign('export', $this);
        return $standaloneView->render();
    }

    /**
     * Create an export file
     *
     * @return bool if file operation could done successfully
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws \Exception
     */
    protected function createExportFile(): bool
    {
        BasicFileUtility::createFolderIfNotExists($this->getStorageFolder(true));
        return GeneralUtility::writeFile($this->getAbsolutePathAndFileName(), $this->getFileContent(), true);
    }

    /**
     * Create export file content
     *
     * @return string
     * @throws InvalidConfigurationTypeException
     * @throws InvalidExtensionNameException
     * @throws Exception
     */
    protected function getFileContent(): string
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(
            TemplateUtility::getTemplatePath($this->getRelativeTemplatePathAndFileName())
        );
        $standaloneView->assignMultiple(
            [
                'mails' => $this->getMails(),
                'fieldUids' => $this->getFieldList()
            ]
        );
        return $standaloneView->render();
    }

    /**
     * @return string
     */
    protected function getRelativeTemplatePathAndFileName(): string
    {
        return 'Module/Export' . ucfirst($this->getFormat()) . '.html';
    }

    /**
     * Get a list with all default fields
     *
     * @param QueryResultInterface $mails
     * @return array
     */
    protected function getDefaultFieldListFromFirstMail(QueryResultInterface $mails = null): array
    {
        $fieldList = [];
        if ($mails !== null) {
            /** @var Mail $mail */
            $mail = $mails->getFirst();
            if ($mail !== null) {
                foreach ($mail->getForm()->getFields(Field::FIELD_TYPE_EXTPORTABLE) as $field) {
                    $fieldList[] = $field->getUid();
                }
            }
        }
        return $fieldList;
    }

    /**
     * @return QueryResultInterface
     */
    public function getMails(): QueryResultInterface
    {
        return $this->mails;
    }

    /**
     * @param QueryResultInterface|null $mails
     * @return ExportService
     */
    public function setMails(?QueryResultInterface $mails): ExportService
    {
        $this->mails = $mails;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        $allowedFormats = [
            'xls',
            'csv'
        ];
        if (!in_array($this->format, $allowedFormats)) {
            return 'xls';
        }
        return $this->format;
    }

    /**
     * @param string $format
     * @return ExportService
     */
    public function setFormat(string $format): ExportService
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return array
     */
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    /**
     * @param string|array $fieldList
     * @return ExportService
     */
    public function setFieldList($fieldList): ExportService
    {
        if (!empty($fieldList)) {
            if (is_string($fieldList)) {
                $fieldList = GeneralUtility::trimExplode(',', $fieldList, true);
            }
            $this->fieldList = $fieldList;
        }
        return $this;
    }

    /**
     * Get an array prepared for mail function
     *        array(
     *            'mail1@mail.org' => '',
     *            'mail2@mail.org' => ''
     *        )
     *
     * @return array
     */
    public function getReceiverEmails(): array
    {
        $mailArray = [];
        foreach ($this->receiverEmails as $email) {
            $mailArray[$email] = '';
        }
        return $mailArray;
    }

    /**
     * @param string|array $emails
     * @return ExportService
     */
    public function setReceiverEmails($emails): ExportService
    {
        if (is_string($emails)) {
            $emails = GeneralUtility::trimExplode(',', $emails, true);
        }
        $this->receiverEmails = $emails;
        return $this;
    }

    /**
     * Get an array prepared for mail function
     *        array(
     *            'mail1@mail.org' => 'Sender',
     *            'mail2@mail.org' => 'Sender'
     *        )
     *
     * @return array
     */
    public function getSenderEmails(): array
    {
        $mailArray = [];
        foreach ($this->senderEmails as $email) {
            $mailArray[$email] = 'Sender';
        }
        return $mailArray;
    }

    /**
     * @param mixed $senderEmails
     * @return ExportService
     */
    public function setSenderEmails($senderEmails): ExportService
    {
        if (is_string($senderEmails)) {
            $senderEmails = GeneralUtility::trimExplode(',', $senderEmails, true);
        }
        $this->senderEmails = $senderEmails;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return ExportService
     */
    public function setSubject(string $subject): ExportService
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Create a random filename
     *
     * @return void
     */
    protected function createRandomFileName(): void
    {
        /**
         * Note:
         *        GeneralUtility::writeFileToTypo3tempDir()
         *        allows only filenames which are max 59 characters long
         */
        $fileName = StringUtility::getRandomString(55);
        $fileName .= '.';
        $fileName .= $this->getFormat();
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Set a user defined filename
     *
     * @param string $fileName
     * @return ExportService
     */
    public function setFileName(string $fileName = null): ExportService
    {
        if ($fileName) {
            $this->fileName = $fileName . '.' . $this->getFormat();
        }
        return $this;
    }

    /**
     * Get relative path and filename
     *
     * @return string
     */
    public function getRelativePathAndFileName(): string
    {
        return $this->getStorageFolder() . $this->getFileName();
    }

    /**
     * Get absolute path and filename
     *
     * @return string
     */
    public function getAbsolutePathAndFileName(): string
    {
        return GeneralUtility::getFileAbsFileName($this->getRelativePathAndFileName());
    }

    /**
     * @return array
     */
    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }

    /**
     * @param array $additionalProperties
     * @return ExportService
     */
    public function setAdditionalProperties(array $additionalProperties): ExportService
    {
        $this->additionalProperties = $additionalProperties;
        return $this;
    }

    /**
     * @param string $additionalProperty
     * @param string $propertyName
     * @return void
     */
    public function addAdditionalProperty(string $additionalProperty, string $propertyName): void
    {
        $this->additionalProperties[$propertyName] = $additionalProperty;
    }

    /**
     * @return bool
     */
    public function isAddAttachment(): bool
    {
        return $this->addAttachment;
    }

    /**
     * @param bool $addAttachment
     * @return ExportService
     */
    public function setAddAttachment(bool $addAttachment): ExportService
    {
        $this->addAttachment = $addAttachment;
        return $this;
    }

    /**
     * @param bool $absolute
     * @return string
     */
    public function getStorageFolder(bool $absolute = false): string
    {
        $storageFolder = $this->storageFolder;
        if ($absolute) {
            $storageFolder = GeneralUtility::getFileAbsFileName($storageFolder);
        }
        return $storageFolder;
    }

    /**
     * @param string $storageFolder
     * @return ExportService
     */
    public function setStorageFolder(string $storageFolder): ExportService
    {
        $this->storageFolder = $storageFolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTemplate(): string
    {
        return $this->emailTemplate;
    }

    /**
     * @param string $emailTemplate
     * @return ExportService
     */
    public function setEmailTemplate(string $emailTemplate): ExportService
    {
        if (!empty($emailTemplate)) {
            $this->emailTemplate = $emailTemplate;
        }
        return $this;
    }
}
