<?php

namespace src\dao;

class PaginationMetaDao
{
    private int $totalItem;
    private int $currentPage;
    private int $perPage;
    private int $totalPage;

    public function __construct(int $currentPage, int $perPage, int $totalItem)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->totalItem = $totalItem;
        $this->totalPage = ceil($totalItem / $perPage);
    }

    public function getTotalItem(): int
    {
        return $this->totalItem;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalPage(): int
    {
        return $this->totalPage;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPage;
    }

    public function hasPrevPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getFirstPage(): int
    {
        return 1;
    }

    public function getLastPage(): int
    {
        return $this->totalPage;
    }
}
