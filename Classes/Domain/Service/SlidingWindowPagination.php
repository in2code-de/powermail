<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Service;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;

/**
 * Class SlidingWindowPagination
 */
final class SlidingWindowPagination implements PaginationInterface
{
    /**
     * @var int
     */
    protected int $displayRangeStart = 0;

    /**
     * @var int
     */
    protected int $displayRangeEnd = 0;

    /**
     * @var bool
     */
    protected bool $hasLessPages = false;

    /**
     * @var bool
     */
    protected bool $hasMorePages = false;

    /**
     * @var int
     */
    protected int $maximumNumberOfLinks = 0;

    /**
     * @var PaginatorInterface
     */
    protected PaginatorInterface $paginator;

    /**
     * @param PaginatorInterface $paginator
     * @param int $maximumNumberOfLinks
     */
    public function __construct(PaginatorInterface $paginator, int $maximumNumberOfLinks = 0)
    {
        $this->paginator = $paginator;

        if ($maximumNumberOfLinks > 0) {
            $this->maximumNumberOfLinks = $maximumNumberOfLinks;
        }

        $this->calculateDisplayRange();
    }

    /**
     * @return int|null
     */
    public function getPreviousPageNumber(): ?int
    {
        $previousPage = $this->paginator->getCurrentPageNumber() - 1;

        if ($previousPage > $this->paginator->getNumberOfPages()) {
            return null;
        }

        return $previousPage >= $this->getFirstPageNumber() ? $previousPage : null;
    }

    /**
     * @return int|null
     */
    public function getNextPageNumber(): ?int
    {
        $nextPage = $this->paginator->getCurrentPageNumber() + 1;

        return $nextPage <= $this->paginator->getNumberOfPages() ? $nextPage : null;
    }

    /**
     * @return int
     */
    public function getFirstPageNumber(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getLastPageNumber(): int
    {
        return $this->paginator->getNumberOfPages();
    }

    /**
     * @return int
     */
    public function getStartRecordNumber(): int
    {
        if ($this->paginator->getCurrentPageNumber() > $this->paginator->getNumberOfPages()) {
            return 0;
        }

        return $this->paginator->getKeyOfFirstPaginatedItem() + 1;
    }

    /**
     * @return int
     */
    public function getEndRecordNumber(): int
    {
        if ($this->paginator->getCurrentPageNumber() > $this->paginator->getNumberOfPages()) {
            return 0;
        }

        return $this->paginator->getKeyOfLastPaginatedItem() + 1;
    }

    /**
     * @return array
     */
    public function getAllPageNumbers(): array
    {
        return range($this->displayRangeStart, $this->displayRangeEnd);
    }

    /**
     * @return int
     */
    public function getDisplayRangeStart(): int
    {
        return $this->displayRangeStart;
    }

    /**
     * @return int
     */
    public function getDisplayRangeEnd(): int
    {
        return $this->displayRangeEnd;
    }

    /**
     * @return bool
     */
    public function getHasLessPages(): bool
    {
        return $this->hasLessPages;
    }

    /**
     * @return bool
     */
    public function getHasMorePages(): bool
    {
        return $this->hasMorePages;
    }

    /**
     * @return int
     */
    public function getMaximumNumberOfLinks(): int
    {
        return $this->maximumNumberOfLinks;
    }

    /**
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @return void
     */
    protected function calculateDisplayRange(): void
    {
        $maximumNumberOfLinks = $this->maximumNumberOfLinks;
        $numberOfPages = $this->paginator->getNumberOfPages();

        if ($maximumNumberOfLinks > $numberOfPages) {
            $maximumNumberOfLinks = $numberOfPages;
        }

        $currentPage = $this->paginator->getCurrentPageNumber();
        $delta = floor($maximumNumberOfLinks / 2);

        $this->displayRangeStart = (int)($currentPage - $delta);
        $this->displayRangeEnd = (int)($currentPage + $delta - ($maximumNumberOfLinks % 2 === 0 ? 1 : 0));

        if ($this->displayRangeStart < 1) {
            $this->displayRangeEnd -= $this->displayRangeStart - 1;
        }

        if ($this->displayRangeEnd > $numberOfPages) {
            $this->displayRangeStart -= $this->displayRangeEnd - $numberOfPages;
        }

        $this->displayRangeStart = (int)max($this->displayRangeStart, 1);
        $this->displayRangeEnd = (int)min($this->displayRangeEnd, $numberOfPages);
        $this->hasLessPages = $this->displayRangeStart > 2;
        $this->hasMorePages = $this->displayRangeEnd + 1 < $this->paginator->getNumberOfPages();
    }
}
