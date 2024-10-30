<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Service\Mail\SendMailService;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class SendMailServiceCreateEmailBodyEvent
{
    public function __construct(protected StandaloneView $standaloneView, protected array $email, protected SendMailService $sendMailService)
    {
    }

    public function getStandaloneView(): StandaloneView
    {
        return $this->standaloneView;
    }

    public function setStandaloneView(StandaloneView $standaloneView): SendMailServiceCreateEmailBodyEvent
    {
        $this->standaloneView = $standaloneView;
        return $this;
    }

    public function getEmail(): array
    {
        return $this->email;
    }

    public function setEmail(array $email): SendMailServiceCreateEmailBodyEvent
    {
        $this->email = $email;
        return $this;
    }

    public function getSendMailService(): SendMailService
    {
        return $this->sendMailService;
    }
}
