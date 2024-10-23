<?php

namespace src\exceptions;

class ConflictHttpException extends BaseHttpException
{
    public function __construct(string $message)
    {
        parent::__construct(409, $message);
    }
}
