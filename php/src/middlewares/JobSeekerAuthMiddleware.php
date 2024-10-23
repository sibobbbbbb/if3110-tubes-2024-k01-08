<?php

namespace src\middlewares;

use src\dao\UserRole;
use src\middlewares\AuthMiddleware;

class JobSeekerAuthMiddleware extends AuthMiddleware
{
    public function __construct()
    {
        parent::__construct([UserRole::JOBSEEKER]);
    }
}
