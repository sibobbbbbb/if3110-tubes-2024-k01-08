<?php

namespace src\middlewares;

use src\middlewares\AuthMiddleware;

class AnyAuthMiddleware extends AuthMiddleware
{
    public function __construct()
    {
        parent::__construct([]);
    }
}
