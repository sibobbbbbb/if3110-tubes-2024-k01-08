<?php

namespace src\services;

use src\dao\UserDao;
use src\exceptions\HttpExceptionFactory;
use src\repositories\UserRepository;

class AuthService extends Service
{
    // Dependency injection
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Sign in a user
     */
    public function signIn(string $email, string $password): UserDao
    {
        // Find the user by email
        $user = $this->userRepository->findUserByEmail($email);

        // Check if the user exists
        if (!isset($user)) {
            throw HttpExceptionFactory::create(400, 'Username or password is incorrect');
        }

        // Check if the password is correct
        if (!password_verify($password, $user->getHashedPassword())) {
            throw HttpExceptionFactory::create(400, 'Username or password is incorrect');
        }

        return $user;
    }

    /**
     * Register a company
     */
    public function signUpCompany(string $name, string $email, string $password, string $location, string $about): string
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Find the user by email
        $transaction = $this->userRepository->insertintouserandcompany($name, $email, $hashed_password, $location, $about);

        $user = $this->userRepository->findUserByEmail($email);

        // Check if the user exists
        // if (!empty($transaction)) {
        //     throw HttpExceptionFactory::create(400, $transaction);
        // }

        return $transaction;
    }

}
