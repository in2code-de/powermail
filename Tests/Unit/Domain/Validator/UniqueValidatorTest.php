<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Domain\Validator;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Domain\Validator\UniqueValidator;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversMethod(UniqueValidator::class, 'isValid')]
final class UniqueValidatorTest extends UnitTestCase
{
    private const MARKER = 'email';

    protected bool $resetSingletonInstances = true;

    private UniqueValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->getAccessibleMock(UniqueValidator::class, ['getStoragePid'], [], '', false);
        $this->validator->method('getStoragePid')->willReturn(1);
        $this->validator->_set('settings', ['validation' => ['unique' => [self::MARKER => '1']]]);
    }

    public static function emptyValueDataProvider(): \Iterator
    {
        yield 'empty string' => ['', Answer::VALUE_TYPE_TEXT];
        yield 'null' => [null, Answer::VALUE_TYPE_TEXT];
        yield 'empty array' => [[], Answer::VALUE_TYPE_ARRAY];
        yield 'array with empty values' => [[''], Answer::VALUE_TYPE_ARRAY];
    }

    #[DataProvider('emptyValueDataProvider')]
    #[Test]
    public function isValidSkipsEmptyValues(string|array|null $value, int $valueType): void
    {
        $result = $this->validator->validate($this->getMail($value, $valueType));

        self::assertFalse($result->hasErrors());
    }

    public static function filledValueDataProvider(): \Iterator
    {
        yield 'already used email' => ['mail@example.org', 1, true];
        yield 'already used "0"' => ['0', 1, true];
        yield 'not yet used email' => ['mail@example.org', 0, false];
    }

    #[DataProvider('filledValueDataProvider')]
    #[Test]
    public function isValidChecksFilledValues(string $value, int $numberOfMails, bool $expectedError): void
    {
        $queryResult = $this->createMock(QueryResultInterface::class);
        $queryResult->method('count')->willReturn($numberOfMails);
        $mailRepository = $this->createMock(MailRepository::class);
        $mailRepository->expects(self::once())
            ->method('findByMarkerValueForm')
            ->with(self::MARKER, $value)
            ->willReturn($queryResult);
        GeneralUtility::setSingletonInstance(MailRepository::class, $mailRepository);

        $result = $this->validator->validate($this->getMail($value, Answer::VALUE_TYPE_TEXT));

        self::assertSame($expectedError, $result->hasErrors());
    }

    private function getMail(string|array|null $value, int $valueType): Mail
    {
        $field = new Field();
        $field->setMarker(self::MARKER);
        $answer = new Answer();
        $answer->setField($field);
        $answer->setValueType($valueType);
        $answer->setValue($value);
        $mail = new Mail();
        $mail->setForm(new Form());
        $mail->addAnswer($answer);
        return $mail;
    }
}
