<?php

namespace src\dto;

class SuccessPagedPaginationDto extends SuccessDto
{
    private PagedPaginationMetaDto $meta;

    public function __construct(string $message, PagedPaginationMetaDto $meta)
    {
        parent::__construct($message);
        $this->meta = $meta;
    }

    public function getMeta(): PagedPaginationMetaDto
    {
        return $this->meta;
    }
}
