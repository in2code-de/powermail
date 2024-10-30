<?php

namespace In2code\Powermail\Tests\Unit\Fixtures\Domain\Repository;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;

/**
 * Class MailRepositoryFixture
 */
class MailRepositoryFixture extends MailRepository
{
    /**
     * @param int $uid
     * @return Mail
     */
    public function findByUid($uid): ?\In2code\Powermail\Domain\Model\Mail
    {
        $mail = new Mail();
        $mail->_setProperty('uid', $uid);
        return $mail;
    }

    /**
     * @param object $object
     */
    public function add($object): void
    {
        unset($object);
        throw new \UnexpectedValueException('Object stored', 1515088469408);
    }
}
