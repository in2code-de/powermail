<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Answer;

final class MailFactoryBeforePasswordIsHashedEvent
{
    protected bool $passwordShouldBeHashed = true;

    public function __construct(protected Answer $answer)
    {
    }

    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    public function setAnswer(Answer $answer): MailFactoryBeforePasswordIsHashedEvent
    {
        $this->answer = $answer;
        return $this;
    }

    public function isPasswordShouldBeHashed(): bool
    {
        return $this->passwordShouldBeHashed;
    }

    public function setPasswordShouldBeHashed(bool $passwordShouldBeHashed): void
    {
        $this->passwordShouldBeHashed = $passwordShouldBeHashed;
    }
}
