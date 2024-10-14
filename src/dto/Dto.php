<?php

/**
 * Base DTO class
 */
abstract class BaseDto
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

/**
 * Dto for error response
 */
class ErrorDto extends BaseDto
{
    private array | null $errorFields;

    public function __construct(string $message, array | null $errorFields)
    {
        parent::__construct($message);
        $this->errorFields = $errorFields;
    }

    public function getErrorFields(): array | null
    {
        return $this->errorFields;
    }
}

/**
 * Dto for success response (with data or without)
 */
class SuccessDto extends BaseDto
{
    private object | null $data;

    public function __construct(string $message, object | null $data = null)
    {
        parent::__construct($message);
        $this->data = $data;
    }

    public function getData(): object | null
    {
        return $this->data;
    }
}

/**
 * Dto for success response with paged-based pagination data
 */
class PagedPaginationMeta
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

class SuccessPagedPaginationDto extends SuccessDto
{
    private PagedPaginationMeta $meta;

    public function __construct(string $message, PagedPaginationMeta $meta)
    {
        parent::__construct($message);
        $this->meta = $meta;
    }

    public function getMeta(): PagedPaginationMeta
    {
        return $this->meta;
    }
}
