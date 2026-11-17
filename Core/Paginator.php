<?php

namespace Core;

class Paginator
{
    public readonly int $page;
    public readonly int $lastPage;

    public function __construct(
        public readonly int $total,
        public readonly int $perPage,
        int $page = 1
    ) {
        $this->lastPage = max(1, (int) ceil($total / $perPage));
        $this->page = min(max(1, $page), $this->lastPage);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function from(): int
    {
        return $this->total === 0 ? 0 : $this->offset() + 1;
    }

    public function to(): int
    {
        return min($this->offset() + $this->perPage, $this->total);
    }

    public function hasPages(): bool
    {
        return $this->lastPage > 1;
    }

    public function pages(int $around = 1): array
    {
        $pages = [];
        $previous = 0;

        for ($page = 1; $page <= $this->lastPage; $page++) {
            $isEdge = $page === 1 || $page === $this->lastPage;

            if (!$isEdge && abs($page - $this->page) > $around) {
                continue;
            }

            if ($page - $previous > 1) {
                $pages[] = null;
            }

            $pages[] = $previous = $page;
        }

        return $pages;
    }
}
