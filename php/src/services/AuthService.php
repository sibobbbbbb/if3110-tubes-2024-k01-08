<?php

namespace src\services;

use src\dao\{UserDao, CompanyDetailDao};
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
    public function createCompany(string $name, string $email, string $password, string $location, string $about): void
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $user = new UserDao(0, $name, $email, $hashed_password, 'company');
            $companyDetails = new CompanyDetailDao(0,  $name, $location, $about);
            $this->userRepository->createUserandCompany($user, $companyDetails);
            return;
        } catch (\PDOException $e) {
            if ($e->getCode() == "23505") {
                throw HttpExceptionFactory::createBadRequest($e->getMessage());
            }
            throw HttpExceptionFactory::createInternalServerError("An error occurred while creating your account");
        }
    }
    /**
     * Register a job seeker
     */
    public function createJobSeeker(string $name, string $email, string $password): void
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $user = new UserDao(0, $name, $email, $hashed_password, 'jobseeker');
            $this->userRepository->createUser($user);
        } catch (\PDOException $e) {
            if ($e->getCode() == "23505") {
                throw HttpExceptionFactory::createBadRequest($e->getMessage());
            }
            throw HttpExceptionFactory::createInternalServerError("An error occurred while creating account");
        }
    }
}
