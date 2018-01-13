<?php
declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use In2code\Powermail\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
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
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
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
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $form = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Answer>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $answers = null;

    /**
     * @var \DateTime
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
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->answers = new ObjectStorage();
    }

    /**
     * Returns the senderName
     *
     * @return string $senderName
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Sets the senderName
     *
     * @param string $senderName
     * @return Mail
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * Returns the senderMail
     *
     * @return string $senderMail
     */
    public function getSenderMail()
    {
        return $this->senderMail;
    }

    /**
     * Sets the senderMail
     *
     * @param string $senderMail
     * @return Mail
     */
    public function setSenderMail($senderMail)
    {
        $this->senderMail = $senderMail;
        return $this;
    }

    /**
     * Returns the subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject
     *
     * @param string $subject
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Returns the receiverMail
     *
     * @return string $receiverMail
     */
    public function getReceiverMail()
    {
        return $this->receiverMail;
    }

    /**
     * Sets the receiverMail
     *
     * @param string $receiverMail
     * @return Mail
     */
    public function setReceiverMail($receiverMail)
    {
        $this->receiverMail = $receiverMail;
        return $this;
    }

    /**
     * Returns the body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the body
     *
     * @param string $body
     * @return Mail
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Returns the feuser
     *
     * @return \In2code\Powermail\Domain\Model\User $feuser
     */
    public function getFeuser()
    {
        return $this->feuser;
    }

    /**
     * Sets the feuser
     *
     * @param \In2code\Powermail\Domain\Model\User $feuser
     * @return Mail
     */
    public function setFeuser(User $feuser)
    {
        $this->feuser = $feuser;
        return $this;
    }

    /**
     * Returns the spamFactor
     *
     * @return string $spamFactor
     */
    public function getSpamFactor()
    {
        return $this->spamFactor;
    }

    /**
     * Sets the spamFactor
     *
     * @param string $spamFactor
     * @return Mail
     */
    public function setSpamFactor($spamFactor)
    {
        $this->spamFactor = $spamFactor;
        return $this;
    }

    /**
     * Returns the time
     *
     * @return int $time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sets the time
     *
     * @param int $time
     * @return Mail
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Returns the senderIp
     *
     * @return string $senderIp
     */
    public function getSenderIp()
    {
        return $this->senderIp;
    }

    /**
     * Sets the senderIp
     *
     * @param string $senderIp
     * @return Mail
     */
    public function setSenderIp($senderIp)
    {
        $this->senderIp = $senderIp;
        return $this;
    }

    /**
     * Returns the userAgent
     *
     * @return string $userAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the userAgent
     *
     * @param string $userAgent
     * @return Mail
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Returns the form
     *
     * @return \In2code\Powermail\Domain\Model\Form $form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the form
     *
     * @param \In2code\Powermail\Domain\Model\Form $form
     * @return Mail
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Returns the answers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Sets the answers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     * @return Mail
     */
    public function setAnswers(ObjectStorage $answers)
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * Adds an answer
     *
     * @param \In2code\Powermail\Domain\Model\Answer $answer
     * @return void
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers->attach($answer);
    }

    /**
     * Removes an answer
     *
     * @param \In2code\Powermail\Domain\Model\Answer $answerToRemove
     * @return void
     */
    public function removeAnswer(Answer $answerToRemove)
    {
        $this->answers->detach($answerToRemove);
    }

    /**
     * Returns the crdate
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate
     *
     * @param \DateTime $crdate
     * @return Mail
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * Returns the hidden
     *
     * @return bool $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden
     *
     * @param bool $hidden
     * @return Mail
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param string $marketingBrowserLanguage
     * @return Mail
     */
    public function setMarketingBrowserLanguage($marketingBrowserLanguage)
    {
        $this->marketingBrowserLanguage = $marketingBrowserLanguage;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingBrowserLanguage()
    {
        return $this->marketingBrowserLanguage;
    }

    /**
     * @param string $marketingCountry
     * @return Mail
     */
    public function setMarketingCountry($marketingCountry)
    {
        $this->marketingCountry = $marketingCountry;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingCountry()
    {
        return $this->marketingCountry;
    }

    /**
     * @param int $marketingFrontendLanguage
     * @return Mail
     */
    public function setMarketingFrontendLanguage($marketingFrontendLanguage)
    {
        $this->marketingFrontendLanguage = $marketingFrontendLanguage;
        return $this;
    }

    /**
     * @return int
     */
    public function getMarketingFrontendLanguage()
    {
        return $this->marketingFrontendLanguage;
    }

    /**
     * @param boolean $marketingMobileDevice
     * @return Mail
     */
    public function setMarketingMobileDevice($marketingMobileDevice)
    {
        $this->marketingMobileDevice = $marketingMobileDevice;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMarketingMobileDevice()
    {
        return $this->marketingMobileDevice;
    }

    /**
     * @param array $marketingPageFunnel
     * @return Mail
     */
    public function setMarketingPageFunnel($marketingPageFunnel)
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
    public function getMarketingPageFunnel()
    {
        if (ArrayUtility::isJsonArray($this->marketingPageFunnel)) {
            return json_decode($this->marketingPageFunnel);
        }
        return (array)$this->marketingPageFunnel;
    }

    /**
     * Returns the UID of the last page that the user has opened.
     *
     * @return int
     */
    public function getMarketingPageFunnelLastPage()
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
    public function getMarketingPageFunnelString($glue = ', ')
    {
        $pageFunnel = $this->getMarketingPageFunnel();
        return implode($glue, $pageFunnel);
    }

    /**
     * @param string $marketingReferer
     * @return Mail
     */
    public function setMarketingReferer($marketingReferer)
    {
        $this->marketingReferer = $marketingReferer;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketingReferer()
    {
        return $this->marketingReferer;
    }

    /**
     * @param string $marketingRefererDomain
     * @return Mail
     */
    public function setMarketingRefererDomain($marketingRefererDomain)
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
    public function getMarketingRefererDomain()
    {
        return $this->marketingRefererDomain;
    }

    /**
     * @param int $pid
     * @return Mail
     */
    public function setPid($pid)
    {
        parent::setPid($pid);
        return $this;
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
    public function getAnswersByFieldMarker()
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
    public function getAnswersByFieldUid()
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
    public function getAnswersByValueType($type)
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
}
