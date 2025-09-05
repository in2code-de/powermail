<?php

declare(strict_types=1);
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
     */
    #[\TYPO3\CMS\Extbase\Annotation\ORM\Lazy]
    protected $feuser;

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
    protected $time = 0;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Answer>
     */
    #[\TYPO3\CMS\Extbase\Annotation\ORM\Lazy]
    protected $answers;

    /**
     * @var DateTime
     */
    protected $crdate;

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
    protected $answersByFieldMarker;

    /**
     * @var array
     */
    protected $answersByFieldUid;

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
     */
    protected ?int $_languageUid = -1;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initializeObject();
    }

    protected function initializeObject(): void
    {
        $this->answers = new ObjectStorage();
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): Mail
    {
        $this->senderName = $senderName;
        return $this;
    }

    public function getSenderMail(): string
    {
        return $this->senderMail;
    }

    public function setSenderMail(string $senderMail): Mail
    {
        $this->senderMail = $senderMail;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Mail
    {
        $this->subject = $subject;
        return $this;
    }

    public function getReceiverMail(): string
    {
        return $this->receiverMail;
    }

    public function setReceiverMail(string $receiverMail): Mail
    {
        $this->receiverMail = $receiverMail;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

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

    public function setFeuser(User $feuser): Mail
    {
        $this->feuser = $feuser;
        return $this;
    }

    public function getSpamFactor(): string
    {
        return $this->spamFactor;
    }

    public function setSpamFactor(string $spamFactor): Mail
    {
        $this->spamFactor = $spamFactor;
        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): Mail
    {
        $this->time = $time;
        return $this;
    }

    public function getSenderIp(): string
    {
        return $this->senderIp;
    }

    public function setSenderIp(string $senderIp): Mail
    {
        $this->senderIp = $senderIp;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

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

    public function setForm(Form $form): Mail
    {
        $this->form = $form;
        return $this;
    }

    public function getAnswers(): ObjectStorage
    {
        return $this->answers;
    }

    /**
     * @var ObjectStorage
     */
    public function setAnswers(ObjectStorage $answers): Mail
    {
        $this->answers = $answers;
        return $this;
    }

    public function addAnswer(Answer $answer): void
    {
        $this->answers->attach($answer);
    }

    public function removeAnswer(Answer $answerToRemove): void
    {
        $this->answers->detach($answerToRemove);
    }

    public function getCrdate(): ?DateTime
    {
        return $this->crdate;
    }

    public function setCrdate(DateTime $crdate): Mail
    {
        $this->crdate = $crdate;
        return $this;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): Mail
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function setMarketingBrowserLanguage(string $marketingBrowserLanguage): Mail
    {
        $this->marketingBrowserLanguage = $marketingBrowserLanguage;
        return $this;
    }

    public function getMarketingBrowserLanguage(): string
    {
        return $this->marketingBrowserLanguage;
    }

    public function setMarketingCountry(string $marketingCountry): Mail
    {
        $this->marketingCountry = $marketingCountry;
        return $this;
    }

    public function getMarketingCountry(): string
    {
        return $this->marketingCountry;
    }

    public function setMarketingFrontendLanguage(int $marketingFrontendLanguage): Mail
    {
        $this->marketingFrontendLanguage = $marketingFrontendLanguage;
        return $this;
    }

    public function getMarketingFrontendLanguage(): int
    {
        return $this->marketingFrontendLanguage;
    }

    public function setMarketingMobileDevice(bool $marketingMobileDevice): Mail
    {
        $this->marketingMobileDevice = $marketingMobileDevice;
        return $this;
    }

    public function getMarketingMobileDevice(): bool
    {
        return $this->marketingMobileDevice;
    }

    public function setMarketingPageFunnel(array $marketingPageFunnel): Mail
    {
        $marketingPageFunnel = json_encode($marketingPageFunnel);

        $this->marketingPageFunnel = $marketingPageFunnel;
        return $this;
    }

    public function getMarketingPageFunnel(): array
    {
        if (ArrayUtility::isJsonArray($this->marketingPageFunnel)) {
            return json_decode($this->marketingPageFunnel, true);
        }

        return (array)$this->marketingPageFunnel;
    }

    /**
     * Returns the UID of the last page that the user has opened.
     */
    public function getMarketingPageFunnelLastPage(): int
    {
        $pageFunnel = $this->getMarketingPageFunnel();
        if ($pageFunnel !== []) {
            return (int)$pageFunnel[count($pageFunnel) - 1];
        }

        return 0;
    }

    /**
     * Return marketing pagefunnel as commaseparated list
     */
    public function getMarketingPageFunnelString(string $glue = ', '): string
    {
        $pageFunnel = $this->getMarketingPageFunnel();
        return implode($glue, $pageFunnel);
    }

    public function setMarketingReferer(string $marketingReferer): Mail
    {
        $this->marketingReferer = $marketingReferer;
        return $this;
    }

    public function getMarketingReferer(): string
    {
        return $this->marketingReferer;
    }

    public function setMarketingRefererDomain(string $marketingRefererDomain): Mail
    {
        $value = '';
        if ($marketingRefererDomain !== '' && $marketingRefererDomain !== '0') {
            $value = $marketingRefererDomain;
        }

        $this->marketingRefererDomain = $value;
        return $this;
    }

    public function getMarketingRefererDomain(): string
    {
        return $this->marketingRefererDomain;
    }

    public function setPid(int $pid): void
    {
        parent::setPid($pid);
    }

    /**
     * Returns answers as an array with field marker of related field as key.
     *
     * To get value of answer for field with marker "myMarker" use
     * $mail->getAnswersByFieldMarker()['myMarker']->getValue()
     * {mail.answersByFieldMarker.myMarker.value}
     */
    public function getAnswersByFieldMarker(): array
    {
        if (is_null($this->answersByFieldMarker)) {
            $answersArray = $this->getAnswers()->toArray();
            $this->answersByFieldMarker = array_combine(array_map(fn (Answer $answer): string => $answer->getField()->getMarker(), $answersArray), $answersArray);
        }

        return $this->answersByFieldMarker;
    }

    /**
     * Returns answers as an array with uid of related field as key.
     *
     * To get value of answer for field with uid 42 use
     * $mail->getAnswersByFieldUid()[42]->getValue()
     * {mail.answersByFieldUid.42.value}
     */
    public function getAnswersByFieldUid(): array
    {
        if (is_null($this->answersByFieldUid)) {
            $answersArray = $this->getAnswers()->toArray();
            $this->answersByFieldUid = array_combine(array_map(fn (Answer $answer): ?int => $answer->getField()->getUid(), $answersArray), $answersArray);
        }

        return $this->answersByFieldUid;
    }

    /**
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

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function setAdditionalData(array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function addAdditionalData(string $key, mixed $value): void
    {
        $this->additionalData[$key] = $value;
    }
}
