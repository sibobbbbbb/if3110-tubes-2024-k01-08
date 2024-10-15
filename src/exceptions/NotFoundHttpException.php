<?php

namespace src\exceptions;

class NotFoundHttpException extends BaseHttpException
{
    public function __construct(string $message)
    {
        parent::__construct(404, $message);
    }
}
