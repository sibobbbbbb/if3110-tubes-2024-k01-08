<?php

namespace src\services;

use DateTime;
use Exception;
use src\dao\CompanyDetailDao;
use src\dao\JobDao;
use src\dao\JobType;
use src\dao\LocationType;
use src\exceptions\HttpExceptionFactory;
use src\repositories\JobRepository;
use src\repositories\UserRepository;
use src\utils\FileUploader;
use src\utils\UserSession;

class CompanyService extends Service
{
    // Dependency injection
    private UserRepository $userRepository;
    private JobRepository $jobRepository;
    private UploadService $uploadService;

    public function __construct(UserRepository $userRepository, JobRepository $jobRepository, UploadService $uploadService)
    {
        $this->userRepository = $userRepository;
        $this->jobRepository = $jobRepository;
        $this->uploadService = $uploadService;
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
        // // Validate if company detail exists
        // $companyDetail = $this->userRepository->findCompanyDetailByUserId($userId);
        // if ($companyDetail == null) {
        //     throw HttpExceptionFactory::create(404, "Company profile not found");
        // }

        // Update company profile
        try {
            $updatedCompany = new CompanyDetailDao($userId, $name, $location, $about);
            $this->userRepository->updateCompanyDetail($updatedCompany);
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(500, "An error occurred while updating company profile");
        }
    }

    /**
     * Create a new job posting for current logged in company
     */
    public function createJob(string $currentUserId, string $position, string $description, JobType $jobType, LocationType $locationType, array $attachments): void
    {
        // Upload files to server
        try {
            $directoryFromPublic = '/uploads/job-attachments/';
            $pathFiles = $this->uploadService->uploadMultipleFiles($attachments, $directoryFromPublic);
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(500, "An error occurred while uploading job attachments");
        }

        // Create job and its attachments
        try {
            $job = new JobDao(0, $currentUserId, $position, $description, $jobType, $locationType, true, new DateTime(), new DateTime());
            $this->jobRepository->createJobAndAttachments($job, $pathFiles);
        } catch (Exception $e) {
            throw HttpExceptionFactory::create(500, "An error occurred while creating job posting");
        }
    }
}
