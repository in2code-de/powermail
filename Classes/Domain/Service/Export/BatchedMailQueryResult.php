<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service\Export;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Exception\ReadOnlyException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * A QueryResultInterface that loads its records in batches while being iterated.
 *
 * The regular Extbase QueryResult maps the complete result set to objects on first access
 * (QueryResult::initialize()), which makes exporting a few hundred mails with all their answers exhaust
 * the memory limit. This implementation keeps only one batch alive at a time and drops the previous one
 * from the persistence session, so memory stays flat no matter how many records are exported.
 *
 * It is a drop-in replacement wherever a QueryResultInterface is expected - especially in Fluid, where
 * f:for only needs count() and iteration.
 *
 * This result is read only by nature: it is meant for exporting, not for modifying records.
 *
 * @implements QueryResultInterface<int, Mail>
 */
class BatchedMailQueryResult implements QueryResultInterface
{
    /**
     * Measured with 1000 mails and 30 columns: 50 needs ~132 MB, 100 ~162 MB, 250 ~208 MB, unbatched
     * runs out of a 256 MB limit. Smaller batches cost more queries without saving relevant memory.
     */
    public const DEFAULT_BATCH_SIZE = 50;

    protected int $batchSize;

    /**
     * Absolute position within the whole result set
     */
    protected int $position = 0;

    /**
     * @var array<int, Mail>
     */
    protected array $batch = [];

    protected ?int $batchOffset = null;

    /**
     * @var int<0, max>|null
     */
    protected ?int $numberOfResults = null;

    /**
     * @param QueryInterface<Mail> $query
     */
    public function __construct(
        protected QueryInterface $query,
        int $batchSize = self::DEFAULT_BATCH_SIZE,
        protected ?PersistenceManagerInterface $persistenceManager = null
    ) {
        $this->batchSize = max(1, $batchSize);
    }

    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
        $this->numberOfResults = null;
        $this->reset();
    }

    public function getQuery(): QueryInterface
    {
        return clone $this->query;
    }

    public function count(): int
    {
        if ($this->numberOfResults === null) {
            // max() only narrows the type to a non-negative integer, the value itself never changes
            $this->numberOfResults = max(0, $this->createQuery()->execute()->count());
        }

        return $this->numberOfResults;
    }

    public function getFirst(): ?Mail
    {
        $query = $this->createQuery();
        $query->setLimit(1);

        return $query->execute()->toArray()[0] ?? null;
    }

    /**
     * Loads every record at once - only implemented to satisfy the interface. Iterate instead, that is
     * the whole point of this class.
     *
     * @return array<int, Mail>
     */
    public function toArray(): array
    {
        return $this->createQuery()->execute()->toArray();
    }

    /**
     * Iterator::current() may be called on an invalid position, so null is a valid answer here even
     * though the generic type of the interface does not express that.
     */
    public function current(): mixed
    {
        $this->loadBatchForCurrentPosition();

        /** @phpstan-ignore return.type */
        return $this->batch[$this->position] ?? null;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->reset();
    }

    public function valid(): bool
    {
        if ($this->position < 0 || $this->position >= $this->count()) {
            return false;
        }

        $this->loadBatchForCurrentPosition();

        return isset($this->batch[$this->position]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return (int)$offset >= 0 && (int)$offset < $this->count();
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->offsetExists($offset) === false) {
            return null;
        }

        $offset = (int)$offset;
        $this->loadBatch($this->batchOffsetForPosition($offset));

        return $this->batch[$offset] ?? null;
    }

    /**
     * @throws ReadOnlyException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new ReadOnlyException('A ' . self::class . ' cannot be written to', 1755420001);
    }

    /**
     * @throws ReadOnlyException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new ReadOnlyException('A ' . self::class . ' cannot be written to', 1755420002);
    }

    protected function loadBatchForCurrentPosition(): void
    {
        $offset = $this->batchOffsetForPosition($this->position);
        if ($this->batchOffset !== $offset) {
            $this->loadBatch($offset);
        }
    }

    protected function loadBatch(int $offset): void
    {
        if ($this->batchOffset === $offset) {
            return;
        }

        // Drop the records of the previous batch. Extbase keeps a hard reference to every mapped object
        // in its persistence session, so without this the batches would pile up and nothing would be
        // gained. Safe here because this result set is read only, no pending changes can get lost.
        $this->reset();
        $this->getPersistenceManager()->clearState();

        $query = $this->createQuery();
        $query->setOffset($offset);
        $query->setLimit($this->batchSize);

        foreach ($query->execute()->toArray() as $index => $record) {
            $this->batch[$offset + $index] = $record;
        }

        $this->batchOffset = $offset;
    }

    protected function batchOffsetForPosition(int $position): int
    {
        return intdiv(max(0, $position), $this->batchSize) * $this->batchSize;
    }

    protected function reset(): void
    {
        $this->batch = [];
        $this->batchOffset = null;
    }

    /**
     * Every batch needs its own query object, otherwise the limit and offset of one batch would leak
     * into the next one and into count().
     *
     * @return QueryInterface<Mail>
     */
    protected function createQuery(): QueryInterface
    {
        return clone $this->query;
    }

    protected function getPersistenceManager(): PersistenceManagerInterface
    {
        if ($this->persistenceManager === null) {
            $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManagerInterface::class);
        }

        return $this->persistenceManager;
    }
}
