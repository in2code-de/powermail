<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;

final class MailRepositoryGetVariablesWithMarkersFromMailEvent
{
    /**
     * Constructor
     */
    public function __construct(protected array $variables, protected Mail $mail)
    {
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): MailRepositoryGetVariablesWithMarkersFromMailEvent
    {
        $this->variables = $variables;
        return $this;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }
}
