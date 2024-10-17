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

    /**
     * Get company profile
     */
    public function getCompanyProfile(int $userId): CompanyDetailDao | null
    {
        // Find company detail by user id
        try {
            $companyDetail = $this->userRepository->findCompanyDetailByUserId($userId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(500, "An error occurred while fetching company profile");
        }

        return $companyDetail;
    }

    /**
     * Update company profile
     */
    public function updateCompanyProfile(int $userId, string $name, string $location, string $about): void
    {
        // Validate if company detail exists
        $companyDetail = $this->userRepository->findCompanyDetailByUserId($userId);
        if ($companyDetail == null) {
            throw HttpExceptionFactory::create(404, "Company profile not found");
        }

        // Update company profile
        try {
            $updatedCompany = new CompanyDetailDao($userId, $name, $location, $about);
            $this->userRepository->updateCompanyDetail($updatedCompany);
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(500, "An error occurred while updating company profile");
        }
    }
}
