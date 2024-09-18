<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class SendMailServiceCreateEmailBodyEvent
{
    /**
     * @var StandaloneView
     */
    protected StandaloneView $standaloneView;

    /**
     * @var array
     */
    protected array $email;

    /**
     * @var SendMailService
     */
    protected SendMailService $sendMailService;

    /**
     * @param StandaloneView $standaloneView
     * @param array $email
     * @param SendMailService $sendMailService
     */
    public function __construct(StandaloneView $standaloneView, array $email, SendMailService $sendMailService)
    {
        $this->standaloneView = $standaloneView;
        $this->email = $email;
        $this->sendMailService = $sendMailService;
    }

    /**
     * @return StandaloneView
     */
    public function getStandaloneView(): StandaloneView
    {
        return $this->standaloneView;
    }

    /**
     * @param StandaloneView $standaloneView
     * @return SendMailServiceCreateEmailBodyEvent
     */
    public function setStandaloneView(StandaloneView $standaloneView): SendMailServiceCreateEmailBodyEvent
    {
        $this->standaloneView = $standaloneView;
        return $this;
    }

    /**
     * @return array
     */
    public function getEmail(): array
    {
        return $this->email;
    }

    /**
     * @param array $email
     * @return SendMailServiceCreateEmailBodyEvent
     */
    public function setEmail(array $email): SendMailServiceCreateEmailBodyEvent
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return SendMailService
     */
    public function getSendMailService(): SendMailService
    {
        return $this->sendMailService;
    }
}
