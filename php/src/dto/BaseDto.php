<?php

namespace src\dto;

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
