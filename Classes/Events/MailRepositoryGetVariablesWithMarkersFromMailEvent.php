<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Mail;

final class MailRepositoryGetVariablesWithMarkersFromMailEvent
{
    /**
     * @var array
     */
    protected array $variables;

    /**
     * @var Mail
     */
    protected Mail $mail;

    /**
     * Constructor
     *
     * @param array $variables
     * @param Mail $mail
     */
    public function __construct(array $variables, Mail $mail)
    {
        $this->variables = $variables;
        $this->mail = $mail;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return MailRepositoryGetVariablesWithMarkersFromMailEvent
     */
    public function setVariables(array $variables): MailRepositoryGetVariablesWithMarkersFromMailEvent
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @return Mail
     */
    public function getMail(): Mail
    {
        return $this->mail;
    }
}
