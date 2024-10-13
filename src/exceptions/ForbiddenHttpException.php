<?php

class ForbiddenHttpException extends BaseHttpException
{
    public function __construct(string $message)
    {
        parent::__construct(403, $message);
    }
}
