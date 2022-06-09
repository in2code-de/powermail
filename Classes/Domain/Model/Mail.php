<?php
declare(strict_types = 1);
namespace In2code\Powermail\Domain\Model;

use DateTime;
use In2code\Powermail\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Mail
 */
class Mail extends AbstractEntity
{
    const TABLE_NAME = 'tx_powermail_domain_model_mail';

    /**
     * @var string
     */
    protected $senderName = '';

    /**
     * @var string
     */
    protected $senderMail = '';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $receiverMail = '';

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var \In2code\Powermail\Domain\Model\User
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $feuser = null;

    /**
     * @var string
     */
    protected $senderIp = '';

    /**
     * @var string
     */
    protected $userAgent = '';

    /**
     * @var string
     */
    protected $spamFactor = '';

    /**
     * @var int
     */
    protected $time = null;

    /**
     * @var \In2code\Powermail\Domain\Model\Form
     */
    protected $form = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Answer>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $answers = null;

    /**
     * @var DateTime
     */
    protected $crdate = null;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var string
     */
    protected $marketingRefererDomain = '';

    /**
     * @var string
     */
    protected $marketingReferer = '';

    /**
     * @var string
     */
    protected $marketingCountry = '';

    /**
     * @var bool
     */
    protected $marketingMobileDevice = false;

    /**
     * @var int
     */
    protected $marketingFrontendLanguage = 0;

    /**
     * @var string
     */
    protected $marketingBrowserLanguage = '';

    /**
     * @var string
     */
    protected $marketingPageFunnel = '';

    /**
     * @var array
     */
    protected $answersByFieldMarker = null;

    /**
     * @var array
     */
    protected $answersByFieldUid = null;

    /**
     * This property can be used by extensions to hold some data over a request
     * Use e.g. extension key as array key
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * All mails and answers should be stored with sys_language_uid=-1 to get those values from persisted objects
     * in fe requests in every language (e.g. for optin mails, etc...)
     *
     * @var int
     */
    protected $_languageUid = -1;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * @return void
     */
    protected function initStorageObjects(): void
    {
        $this->answers = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     * @return Mail
     */
    public function setSenderName(string $senderName): Mail
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderMail(): string
    {
        return $this->senderMail;
    }

    /**
     * @param string $senderMail
     * @return Mail
     */
    public function setSenderMail(string $senderMail): Mail
    {
        $this->senderMail = $senderMail;
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
     * @return Mail
     */
    public function setSubject(string $subject): Mail
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverMail(): string
    {
        return $this->receiverMail;
    }

    /**
     * @param string $receiverMail
     * @return Mail
     */
    public function setReceiverMail(string $receiverMail): Mail
    {
        $this->receiverMail = $receiverMail;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Mail
     */
    public function setBody(string $body): Mail
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return User|LazyLoadingProxy|null
     */
    public function getFeuser()
    {
        if ($this->feuser instanceof LazyLoadingProxy) {
            $this->feuser->_loadRealInstance();
        }
        return $this->feuser;
    }

    /**
     * @param User $feuser
     * @return Mail
     */
    public function setFeuser(User $feuser): Mail
    {
        $this->feuser = $feuser;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpamFactor(): string
    {
        return $this->spamFactor;
    }

    /**
     * @param string $spamFactor
     * @return Mail
     */
    public function setSpamFactor(string $spamFactor): Mail
    {
        $this->spamFactor = $spamFactor;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return Mail
     */
    public function setTime(int $time): Mail
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderIp(): string
    {
        return $this->senderIp;
    }

    /**
     * @param string $senderIp
     * @return Mail
     */
    public function setSenderIp(string $senderIp): Mail
    {
        $this->senderIp = $senderIp;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return Mail
     */
    public function setUserAgent(string $userAgent): Mail
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return Form|LazyLoadingProxy|null $form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return Mail
     */
    public function setForm(Form $form): Mail
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAnswers(): ObjectStorage
    {
        return $this->answers;
    }

    /**
     * @var ObjectStorage
     * @return Mail
     */
    public function setAnswers(ObjectStorage $answers): Mail
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * @param Answer $answer
     * @return void
     */
    public function addAnswer(Answer $answer): void
    {
        $this->answers->attach($answer);
    }

    /**
     * @param Answer $answerToRemove
     * @return void
     */
    public function removeAnswer(Answer $answerToRemove): void
    {
        $this->answers->detach($answerToRemove);
    }

    /**
     * @return DateTime|null
     */
    public function getCrdate(): ?DateTime
    {
        return $this->crdate;
    }

    /**
     * @param DateTime $crdate
     * @return Mail
     */
    public function setCrdate(DateTime $crdate): Mail
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return Mail
     */
    public function setHidden(bool $hidden): Mail
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param string $marketingBrowserLanguage
     * @return Mail
     */
    public function setMarketingBrowserLanguage(string $marketingBrowserLanguage): Mail
    {
        $this->marketingBrowserLanguage = $marketingBrowserLanguage;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingBrowserLanguage(): string
    {
        return $this->marketingBrowserLanguage;
    }

    /**
     * @param string $marketingCountry
     * @return Mail
     */
    public function setMarketingCountry(string $marketingCountry): Mail
    {
        $this->marketingCountry = $marketingCountry;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingCountry(): string
    {
        return $this->marketingCountry;
    }

    /**
     * @param int $marketingFrontendLanguage
     * @return Mail
     */
    public function setMarketingFrontendLanguage(int $marketingFrontendLanguage): Mail
    {
        $this->marketingFrontendLanguage = $marketingFrontendLanguage;
        return $this;
    }

    /**
     * @return int
     */
    public function getMarketingFrontendLanguage(): int
    {
        return $this->marketingFrontendLanguage;
    }

    /**
     * @param bool $marketingMobileDevice
     * @return Mail
     */
    public function setMarketingMobileDevice(bool $marketingMobileDevice): Mail
    {
        $this->marketingMobileDevice = $marketingMobileDevice;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMarketingMobileDevice(): bool
    {
        return $this->marketingMobileDevice;
    }

    /**
     * @param array $marketingPageFunnel
     * @return Mail
     */
    public function setMarketingPageFunnel(array $marketingPageFunnel): Mail
    {
        if (is_array($marketingPageFunnel)) {
            $marketingPageFunnel = json_encode($marketingPageFunnel);
        }
        $this->marketingPageFunnel = $marketingPageFunnel;
        return $this;
    }

    /**
     * @return array
     */
    public function getMarketingPageFunnel(): array
    {
        if (ArrayUtility::isJsonArray($this->marketingPageFunnel)) {
            return json_decode($this->marketingPageFunnel, true);
        }
        return (array)$this->marketingPageFunnel;
    }

    /**
     * Returns the UID of the last page that the user has opened.
     *
     * @return int
     */
    public function getMarketingPageFunnelLastPage(): int
    {
        $pageFunnel = $this->getMarketingPageFunnel();
        if (count($pageFunnel)) {
            return $pageFunnel[count($pageFunnel) - 1];
        }
        return 0;
    }

    /**
     * Return marketing pagefunnel as commaseparated list
     *
     * @param string $glue
     * @return string
     */
    public function getMarketingPageFunnelString(string $glue = ', '): string
    {
        $pageFunnel = $this->getMarketingPageFunnel();
        return implode($glue, $pageFunnel);
    }

    /**
     * @param string $marketingReferer
     * @return Mail
     */
    public function setMarketingReferer(string $marketingReferer): Mail
    {
        $this->marketingReferer = $marketingReferer;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingReferer(): string
    {
        return $this->marketingReferer;
    }

    /**
     * @param string $marketingRefererDomain
     * @return Mail
     */
    public function setMarketingRefererDomain(string $marketingRefererDomain): Mail
    {
        $value = '';
        if (!empty($marketingRefererDomain)) {
            $value = $marketingRefererDomain;
        }
        $this->marketingRefererDomain = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingRefererDomain(): string
    {
        return $this->marketingRefererDomain;
    }

    /**
     * @param int $pid
     * @return void
     */
    public function setPid($pid): void
    {
        parent::setPid($pid);
    }

    /**
     * Returns answers as an array with field marker of related field as key.
     *
     * To get value of answer for field with marker "myMarker" use
     * $mail->getAnswersByFieldMarker()['myMarker']->getValue()
     * {mail.answersByFieldMarker.myMarker.value}
     *
     * @return array
     */
    public function getAnswersByFieldMarker(): array
    {
        if (is_null($this->answersByFieldMarker)) {
            $answersArray = $this->getAnswers()->toArray();
            $this->answersByFieldMarker = array_combine(array_map(function (Answer $answer) {
                return $answer->getField()->getMarker();
            }, $answersArray), $answersArray);
        }
        return $this->answersByFieldMarker;
    }

    /**
     * Returns answers as an array with uid of related field as key.
     *
     * To get value of answer for field with uid 42 use
     * $mail->getAnswersByFieldUid()[42]->getValue()
     * {mail.answersByFieldUid.42.value}
     *
     * @return array
     */
    public function getAnswersByFieldUid(): array
    {
        if (is_null($this->answersByFieldUid)) {
            $answersArray = $this->getAnswers()->toArray();
            $this->answersByFieldUid = array_combine(array_map(function (Answer $answer) {
                return $answer->getField()->getUid();
            }, $answersArray), $answersArray);
        }
        return $this->answersByFieldUid;
    }

    /**
     * @param int $type
     * @return Answer[]
     */
    public function getAnswersByValueType(int $type): array
    {
        $answers = [];
        $answersArray = $this->getAnswers()->toArray();
        foreach ($answersArray as $answer) {
            /** @var Answer $answer */
            if ($answer->getValueType() === $type) {
                $answers[] = $answer;
            }
        }
        return $answers;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * @param array $additionalData
     * @return void
     */
    public function setAdditionalData(array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addAdditionalData(string $key, $value): void
    {
        $this->additionalData[$key] = $value;
    }
}
