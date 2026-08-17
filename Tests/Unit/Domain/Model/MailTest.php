<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Domain\Model;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class MailTest
 * @coversDefaultClass \In2code\Powermail\Domain\Model\Mail
 */
class MailTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * @covers ::getAnswersGroupedByFieldUid
     */
    public function testAnswersAreGroupedByTheUidOfTheirField(): void
    {
        $mail = $this->buildMail([10 => 'first', 20 => 'second']);

        $grouped = $mail->getAnswersGroupedByFieldUid();

        self::assertSame([10, 20], array_keys($grouped));
        self::assertSame('first', $grouped[10][0]->getValue());
        self::assertSame('second', $grouped[20][0]->getValue());
    }

    /**
     * A field can hold more than one answer. All of them have to be kept, the export renders every
     * single one of them - in contrast to getAnswersByFieldUid(), which only keeps the last.
     *
     * @covers ::getAnswersGroupedByFieldUid
     */
    public function testAllAnswersOfTheSameFieldAreKeptInOrder(): void
    {
        $mail = $this->buildMail([10 => 'one', 10 => 'ignored']);
        $field = new Field();
        $field->_setProperty('uid', 10);
        foreach (['two', 'three'] as $value) {
            $answer = new Answer();
            $answer->setField($field);
            $answer->setValue($value);
            $mail->addAnswer($answer);
        }

        $grouped = $mail->getAnswersGroupedByFieldUid();

        self::assertCount(3, $grouped[10]);
        self::assertSame(['ignored', 'two', 'three'], array_map(
            fn (Answer $answer): string => (string)$answer->getValue(),
            $grouped[10]
        ));
    }

    /**
     * @covers ::getAnswersGroupedByFieldUid
     */
    public function testAnswersWithoutAFieldAreSkipped(): void
    {
        $mail = $this->buildMail([10 => 'with field']);
        $mail->addAnswer(new Answer());

        $grouped = $mail->getAnswersGroupedByFieldUid();

        self::assertSame([10], array_keys($grouped));
    }

    /**
     * @covers ::getAnswersGroupedByFieldUid
     */
    public function testMailWithoutAnswersReturnsAnEmptyArray(): void
    {
        self::assertSame([], $this->buildMail([])->getAnswersGroupedByFieldUid());
    }

    /**
     * The index is built once per mail - that is what makes the export fast.
     *
     * @covers ::getAnswersGroupedByFieldUid
     */
    public function testIndexIsBuiltOnlyOnce(): void
    {
        $mail = $this->buildMail([10 => 'first']);

        $first = $mail->getAnswersGroupedByFieldUid();
        $mail->addAnswer(new Answer());

        self::assertSame($first, $mail->getAnswersGroupedByFieldUid());
    }

    /**
     * @param array<int, string> $answerValuesByFieldUid
     */
    protected function buildMail(array $answerValuesByFieldUid): Mail
    {
        $mail = new Mail();
        foreach ($answerValuesByFieldUid as $fieldUid => $value) {
            $field = new Field();
            $field->_setProperty('uid', $fieldUid);
            $answer = new Answer();
            $answer->setField($field);
            $answer->setValue($value);
            $mail->addAnswer($answer);
        }

        return $mail;
    }
}
