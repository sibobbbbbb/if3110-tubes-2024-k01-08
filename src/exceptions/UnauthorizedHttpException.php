<?php

class UnauthorizedHttpException extends BaseHttpException
{
    public function __construct(string $message)
    {
        parent::__construct(401, $message);
    }
}
