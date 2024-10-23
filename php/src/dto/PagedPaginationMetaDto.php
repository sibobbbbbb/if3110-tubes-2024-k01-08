<?php

namespace src\dto;

/**
 * Dto for success response with paged-based pagination data
 */
class PagedPaginationMetaDto
{
    private int $total;
    private int $page;
    private int $pageSize;

    public function __construct(int $total, int $page, int $pageSize)
    {
        $this->total = $total;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
