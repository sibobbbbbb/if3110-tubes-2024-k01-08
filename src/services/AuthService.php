<?php

namespace src\services;

use src\dao\UserDao;
use src\exceptions\HttpExceptionFactory;
use src\repositories\UserRepository;
use src\exceptions\BadRequestHttpException;

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
            throw HttpExceptionFactory::createBadRequest('Username or password is incorrect');
        }

        // Check if the password is correct
        if (!password_verify($password, $user->getHashedPassword())) {
            throw HttpExceptionFactory::createBadRequest('Username or password is incorrect');
        }

        return $user;
    }

    /**
     * Register a company
     */
    public function signUpCompany(string $name, string $email, string $password, string $location, string $about): void
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $transaction = $this->userRepository->insertintouserandcompany($name, $email, $hashed_password, $location, $about);
            return;
        } catch (BadRequestHttpException $e) {

            throw HttpExceptionFactory::createBadRequest($e->getMessage());
            return;
        }
    }

    /**
     * Register a job seeker
     */
    public function signUpJobSeeker(string $name, string $email, string $password): void
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $transaction = $this->userRepository->insertintouserjobseeker($name, $email, $hashed_password);
            return;
        } catch (BadRequestHttpException $e) {
            throw HttpExceptionFactory::createBadRequest($e->getMessage());
            return;
        }
    }
}
