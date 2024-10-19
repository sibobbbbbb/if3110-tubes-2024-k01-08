<?php

namespace src\dto;

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

    public function toArray(): array
    {
        return [
            "message" => $this->getMessage(),
            "data" => $this->data
        ];
    }
}
