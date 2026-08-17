<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Fixtures\Domain\Service\Export;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Minimal QueryResultInterface returned by QueryFixture::execute()
 */
class QueryResultFixture implements QueryResultInterface
{
    protected int $position = 0;

    /**
     * @param array<int, object> $records
     */
    public function __construct(
        protected array $records,
        protected QueryInterface $query,
        protected int $totalCount
    ) {
        $this->records = array_values($records);
    }

    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    public function getFirst()
    {
        return $this->records[0] ?? null;
    }

    public function toArray(): array
    {
        return $this->records;
    }

    public function count(): int
    {
        return $this->totalCount;
    }

    public function current(): mixed
    {
        return $this->records[$this->position] ?? null;
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
    }

    public function valid(): bool
    {
        return isset($this->records[$this->position]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->records[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->records[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
