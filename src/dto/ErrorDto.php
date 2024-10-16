<?php

namespace src\dto;

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
