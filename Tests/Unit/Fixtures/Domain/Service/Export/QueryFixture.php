<?php

declare(strict_types=1);

namespace In2code\Powermail\Tests\Unit\Fixtures\Domain\Service\Export;

use TYPO3\CMS\Extbase\Persistence\Generic\Qom\AndInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * An in-memory QueryInterface for testing BatchedMailQueryResult without a database.
 *
 * It applies limit and offset to a fixed set of records and counts how often it was executed, so tests
 * can assert that only the needed batches are loaded.
 */
class QueryFixture implements QueryInterface
{
    /**
     * Held in an object so that clones of this query - BatchedMailQueryResult clones it for every
     * batch - keep counting into the same place.
     */
    public \stdClass $executions;

    protected ?int $limit = null;

    protected int $offset = 0;

    /**
     * @param array<int, object> $records
     */
    public function __construct(protected array $records = [])
    {
        $this->executions = new \stdClass();
        $this->executions->count = 0;
    }

    public function countExecutions(): int
    {
        return $this->executions->count;
    }

    public function execute($returnRawQueryResult = false)
    {
        $this->executions->count++;
        $records = array_slice($this->records, $this->offset, $this->limit);

        if ($returnRawQueryResult) {
            return $records;
        }

        return new QueryResultFixture($records, $this, count($this->records));
    }

    public function setLimit($limit): QueryInterface
    {
        $this->limit = (int)$limit;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setOffset($offset): QueryInterface
    {
        $this->offset = (int)$offset;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function setOrderings(array $orderings): QueryInterface
    {
        return $this;
    }

    public function getOrderings(): array
    {
        return [];
    }

    public function matching($constraint): QueryInterface
    {
        return $this;
    }

    public function getConstraint(): ?ConstraintInterface
    {
        return null;
    }

    public function logicalAnd(ConstraintInterface ...$constraints): AndInterface
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420010);
    }

    public function logicalOr(ConstraintInterface ...$constraints): OrInterface
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420011);
    }

    public function logicalNot(ConstraintInterface $constraint): ConstraintInterface
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420012);
    }

    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420013);
    }

    public function like($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420014);
    }

    public function contains($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420015);
    }

    public function in($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420016);
    }

    public function lessThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420017);
    }

    public function lessThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420018);
    }

    public function greaterThan($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420019);
    }

    public function greaterThanOrEqual($propertyName, $operand)
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420020);
    }

    public function setType(string $type): void
    {
    }

    public function getType(): string
    {
        return \stdClass::class;
    }

    public function setQuerySettings(QuerySettingsInterface $querySettings): void
    {
    }

    public function getQuerySettings(): QuerySettingsInterface
    {
        throw new \BadMethodCallException('Not needed for this fixture', 1755420021);
    }

    public function getSource(): ?SourceInterface
    {
        return null;
    }

    public function setSource(SourceInterface $source): void
    {
    }

    public function getStatement(): mixed
    {
        return null;
    }
}
