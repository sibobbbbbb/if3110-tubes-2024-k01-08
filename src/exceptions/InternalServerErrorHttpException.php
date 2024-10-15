<?php

namespace src\exceptions;

class InternalServerErrorHttpException extends BaseHttpException
{
    public function __construct(string $message)
    {
        parent::__construct(500, $message);
    }
}
