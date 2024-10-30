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
    protected int $displayRangeStart = 0;

    protected int $displayRangeEnd = 0;

    protected bool $hasLessPages = false;

    protected bool $hasMorePages = false;

    protected int $maximumNumberOfLinks = 0;

    public function __construct(protected PaginatorInterface $paginator, int $maximumNumberOfLinks = 0)
    {
        if ($maximumNumberOfLinks > 0) {
            $this->maximumNumberOfLinks = $maximumNumberOfLinks;
        }

        $this->calculateDisplayRange();
    }

    public function getPreviousPageNumber(): ?int
    {
        $previousPage = $this->paginator->getCurrentPageNumber() - 1;

        if ($previousPage > $this->paginator->getNumberOfPages()) {
            return null;
        }

        return $previousPage >= $this->getFirstPageNumber() ? $previousPage : null;
    }

    public function getNextPageNumber(): ?int
    {
        $nextPage = $this->paginator->getCurrentPageNumber() + 1;

        return $nextPage <= $this->paginator->getNumberOfPages() ? $nextPage : null;
    }

    public function getFirstPageNumber(): int
    {
        return 1;
    }

    public function getLastPageNumber(): int
    {
        return $this->paginator->getNumberOfPages();
    }

    public function getStartRecordNumber(): int
    {
        if ($this->paginator->getCurrentPageNumber() > $this->paginator->getNumberOfPages()) {
            return 0;
        }

        return $this->paginator->getKeyOfFirstPaginatedItem() + 1;
    }

    public function getEndRecordNumber(): int
    {
        if ($this->paginator->getCurrentPageNumber() > $this->paginator->getNumberOfPages()) {
            return 0;
        }

        return $this->paginator->getKeyOfLastPaginatedItem() + 1;
    }

    public function getAllPageNumbers(): array
    {
        return range($this->displayRangeStart, $this->displayRangeEnd);
    }

    public function getDisplayRangeStart(): int
    {
        return $this->displayRangeStart;
    }

    public function getDisplayRangeEnd(): int
    {
        return $this->displayRangeEnd;
    }

    public function getHasLessPages(): bool
    {
        return $this->hasLessPages;
    }

    public function getHasMorePages(): bool
    {
        return $this->hasMorePages;
    }

    public function getMaximumNumberOfLinks(): int
    {
        return $this->maximumNumberOfLinks;
    }

    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

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
