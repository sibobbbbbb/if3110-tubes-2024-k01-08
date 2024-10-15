<?php

namespace src\services;

use src\repositories\{UserRepository};

/**
 * Service for handling user-related operations
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
