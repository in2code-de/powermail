<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Domain\Service\Export;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Service\Export\BatchedMailQueryResult;
use In2code\Powermail\Exception\ReadOnlyException;
use In2code\Powermail\Tests\Unit\Fixtures\Domain\Service\Export\QueryFixture;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @coversDefaultClass \In2code\Powermail\Domain\Service\Export\BatchedMailQueryResult
 */
class BatchedMailQueryResultTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * @covers ::count
     */
    public function testCountReturnsTheNumberOfAllRecords(): void
    {
        $subject = $this->buildSubject(25, 10);

        self::assertSame(25, $subject->count());
    }

    /**
     * @covers ::count
     */
    public function testCountIsOnlyDeterminedOnce(): void
    {
        $query = new QueryFixture($this->buildRecords(7));
        $subject = new BatchedMailQueryResult($query, 3, $this->buildPersistenceManager());

        $subject->count();
        $subject->count();

        self::assertSame(1, $query->countExecutions());
    }

    /**
     * Every record has to show up exactly once and in the original order, no matter how the batch size
     * relates to the number of records.
     *
     * @covers ::current
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     * @dataProvider iterationReturnsEveryRecordDataProvider
     */
    public function testIterationReturnsEveryRecordOnce(int $numberOfRecords, int $batchSize): void
    {
        $subject = $this->buildSubject($numberOfRecords, $batchSize);

        $identifiers = [];
        foreach ($subject as $record) {
            $identifiers[] = (int)$record->getSubject();
        }

        self::assertSame($numberOfRecords > 0 ? range(0, $numberOfRecords - 1) : [], $identifiers);
    }

    public static function iterationReturnsEveryRecordDataProvider(): array
    {
        return [
            'empty result' => [0, 10],
            'less records than the batch size' => [4, 10],
            'exactly one batch' => [10, 10],
            'one record more than one batch' => [11, 10],
            'one record less than one batch' => [9, 10],
            'several full batches' => [30, 10],
            'several batches with a remainder' => [34, 10],
            'batch size of one' => [5, 1],
        ];
    }

    /**
     * @covers ::rewind
     */
    public function testResultCanBeIteratedMoreThanOnce(): void
    {
        $subject = $this->buildSubject(12, 5);

        $first = iterator_to_array($subject);
        $second = iterator_to_array($subject);

        self::assertCount(12, $first);
        self::assertSame(array_keys($first), array_keys($second));
    }

    /**
     * @covers ::valid
     */
    public function testIterationLoadsOnlyTheBatchesItNeeds(): void
    {
        $query = new QueryFixture($this->buildRecords(30));
        $subject = new BatchedMailQueryResult($query, 10, $this->buildPersistenceManager());

        foreach ($subject as $ignored) {
            // The whole result set gets iterated
        }

        // One count query plus one query per batch
        self::assertSame(4, $query->countExecutions());
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirstReturnsTheFirstRecord(): void
    {
        $subject = $this->buildSubject(12, 5);

        self::assertSame('0', $subject->getFirst()->getSubject());
    }

    /**
     * @covers ::getFirst
     */
    public function testGetFirstReturnsNullOnAnEmptyResult(): void
    {
        $subject = $this->buildSubject(0, 5);

        self::assertNull($subject->getFirst());
    }

    /**
     * @covers ::offsetGet
     * @covers ::offsetExists
     */
    public function testRecordsCanBeReadByTheirOffset(): void
    {
        $subject = $this->buildSubject(12, 5);

        self::assertTrue($subject->offsetExists(7));
        self::assertSame('7', $subject->offsetGet(7)->getSubject());
        self::assertFalse($subject->offsetExists(12));
        self::assertNull($subject->offsetGet(12));
    }

    /**
     * @covers ::offsetSet
     */
    public function testWritingToTheResultIsRejected(): void
    {
        $subject = $this->buildSubject(3, 5);

        $this->expectException(ReadOnlyException::class);
        $subject->offsetSet(0, new Mail());
    }

    /**
     * @covers ::offsetUnset
     */
    public function testRemovingFromTheResultIsRejected(): void
    {
        $subject = $this->buildSubject(3, 5);

        $this->expectException(ReadOnlyException::class);
        $subject->offsetUnset(0);
    }

    /**
     * The persistence session has to be cleared for every batch, otherwise the records of the previous
     * batches would stay referenced and nothing would be gained.
     *
     * @covers ::loadBatch
     */
    public function testEveryBatchClearsThePersistenceState(): void
    {
        $persistenceManager = $this->buildPersistenceManager();
        $persistenceManager->expects(self::exactly(3))->method('clearState');
        $subject = new BatchedMailQueryResult(new QueryFixture($this->buildRecords(30)), 10, $persistenceManager);

        foreach ($subject as $ignored) {
            // The whole result set gets iterated
        }
    }

    /**
     * @covers ::toArray
     */
    public function testToArrayReturnsAllRecords(): void
    {
        $subject = $this->buildSubject(12, 5);

        self::assertCount(12, $subject->toArray());
    }

    protected function buildSubject(int $numberOfRecords, int $batchSize): BatchedMailQueryResult
    {
        return new BatchedMailQueryResult(
            new QueryFixture($this->buildRecords($numberOfRecords)),
            $batchSize,
            $this->buildPersistenceManager()
        );
    }

    /**
     * @return array<int, Mail>
     */
    protected function buildRecords(int $numberOfRecords): array
    {
        $records = [];
        for ($identifier = 0; $identifier < $numberOfRecords; $identifier++) {
            $record = new Mail();
            $record->setSubject((string)$identifier);
            $records[] = $record;
        }

        return $records;
    }

    protected function buildPersistenceManager(): PersistenceManagerInterface
    {
        return $this->getMockBuilder(PersistenceManagerInterface::class)->getMock();
    }
}
