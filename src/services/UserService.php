<?php

namespace src\services;

use Exception;
use src\dao\CompanyDetailDao;
use src\exceptions\HttpExceptionFactory;
use src\repositories\UserRepository;

/**
 * Service for user operations
 */
class UserService extends Service
{
    // Dependency injection
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
