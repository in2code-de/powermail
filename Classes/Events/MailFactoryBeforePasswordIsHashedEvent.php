<?php

declare(strict_types=1);
namespace In2code\Powermail\Events;

use In2code\Powermail\Domain\Model\Answer;

final class MailFactoryBeforePasswordIsHashedEvent
{
    /**
     * @var Answer
     */
    protected Answer $answer;

    /**
     * @var bool
     */
    protected bool $passwordShouldBeHashed = true;

    /**
     * @param Answer $answer
     */
    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return Answer
     */
    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    /**
     * @param Answer $answer
     * @return $this
     */
    public function setAnswer(Answer $answer): MailFactoryBeforePasswordIsHashedEvent
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPasswordShouldBeHashed(): bool
    {
        return $this->passwordShouldBeHashed;
    }

    /**
     * @param bool $passwordShouldBeHashed
     * @return void
     */
    public function setPasswordShouldBeHashed(bool $passwordShouldBeHashed): void
    {
        $this->passwordShouldBeHashed = $passwordShouldBeHashed;
    }
}
