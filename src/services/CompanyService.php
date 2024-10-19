<?php

namespace src\services;

use DateTime;
use Exception;
use src\dao\{LocationType, JobType, JobDao, CompanyDetailDao};
use src\exceptions\HttpExceptionFactory;
use src\exceptions\NotFoundHttpException;
use src\repositories\{JobRepository, UserRepository};

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
     * @param int $userId - The user id
     * @throws HttpException
     * @return CompanyDetailDao - The company profile
     */
    public function getCompanyProfile(int $userId): CompanyDetailDao
    {
        // Find company detail by user id
        try {
            $companyDetail = $this->userRepository->findCompanyDetailByUserId($userId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching company profile");
        }

        if ($companyDetail == null) {
            throw HttpExceptionFactory::createNotFound("Company profile not found");
        }

        return $companyDetail;
    }

    /**
     * Update company profile
     * @param int $userId - The user id
     * @param string $name - The company name
     * @param string $location - The company location
     * @param string $about - The company about
     * @throws HttpException
     * @return void
     */
    public function updateCompanyProfile(int $userId, string $name, string $location, string $about): void
    {
        // Validate company
        try {
            $companyDetail = $this->userRepository->findCompanyDetailByUserId($userId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching company profile");
        }

        // If company not found
        if ($companyDetail == null) {
            throw HttpExceptionFactory::createNotFound("Company profile not found");
        }

        // Check if authorized
        if ($companyDetail->getUserId() != $userId) {
            throw HttpExceptionFactory::createForbidden("You are not authorized to update this company profile");
        }

        try {
            // Update company profile
            $companyDetail->setName($name);
            $companyDetail->setLocation($location);
            $companyDetail->setAbout($about);
            $this->userRepository->updateCompanyDetail($companyDetail);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while updating company profile");
        }
    }

    /**
     * Create a new job posting for current logged in company
     * @param string $userId - The user id
     * @param string $position - The job position
     * @param string $description - The job description
     * @param JobType $jobType - The job type
     * @param LocationType $locationType - The location type
     * @param array $rawAttachments - Array of raw attachments
     * @throws HttpException
     * @return [job, attachments] - The new job and attachments
     */
    public function createJob(string $userId, string $position, string $description, JobType $jobType, LocationType $locationType, array $rawAttachments): array
    {
        // Upload files to server
        try {
            $directoryFromPublic = '/uploads/job-attachments/';
            $uploadedFilePaths = $this->uploadService->uploadMultipleFiles($directoryFromPublic, $rawAttachments);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while uploading job attachments");
        }

        // Create job and its attachments
        try {
            [$job, $jobAttachments] = $this->jobRepository->createJobAndAttachments($userId, $position, $description, $jobType, $locationType, $uploadedFilePaths);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while creating job posting");
        }

        return [$job, $jobAttachments];
    }

    /**
     * Get job detail posting
     * @param string $jobId - The job id
     * @throws HttpException
     * @return JobDao - The job posting (with attachments)
     */
    public function getJobDetail(string $jobId): JobDao
    {
        // Get job by id
        try {
            $job = $this->jobRepository->getJobByIdWithAttachments($jobId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching job posting");
        }

        // If job not found
        if ($job == null) {
            throw HttpExceptionFactory::createNotFound("Job posting not found");
        }

        return $job;
    }


    /**
     * Edit a job posting for current logged in company
     * @param string $currentUserId - The user id
     * @param string $jobId - The job id
     * @param string $position - The job position
     * @param string $description - The job description
     * @param bool $isOpen - The job status
     * @param JobType $jobType - The job type
     * @param LocationType $locationType - The location type
     * @param array $attachments - Array of raw attachments
     * @throws HttpException
     * @return [job, attachments] - The edited job and attachments
     */
    public function editJob(string $currentUserId, string $jobId, string $position, string $description, bool $isOpen, JobType $jobType, LocationType $locationType, array $attachments): array
    {
        // Validate job
        try {
            $job = $this->jobRepository->getJobById($jobId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching job posting");
        }

        // If job not found
        if ($job == null) {
            throw HttpExceptionFactory::createNotFound("Job posting not found");
        }

        // Check if authorized
        if ($job->getCompanyId() != $currentUserId) {
            throw HttpExceptionFactory::createForbidden("You are not authorized to edit this job posting");
        }

        $job->setPosition($position);
        $job->setDescription($description);
        $job->setIsOpen($isOpen);
        $job->setJobType($jobType);
        $job->setLocationType($locationType);

        $isAttachmentEmpty = $attachments['error'][0] == 4;
        if (!$isAttachmentEmpty) {
            // If new attachments are uploaded
            // Upload files to server
            try {
                $directoryFromPublic = '/uploads/job-attachments/';
                $uploadedFilePaths = $this->uploadService->uploadMultipleFiles($directoryFromPublic, $attachments);
            } catch (Exception $e) {
                throw HttpExceptionFactory::createInternalServerError("An error occurred while uploading job attachments");
            }

            // Edit job and its attachments
            try {
                $jobAttachments = $this->jobRepository->editJobAndCreateAttachments($job, $uploadedFilePaths);
            } catch (Exception $e) {
                throw HttpExceptionFactory::createInternalServerError("An error occurred while editing job posting");
            }

            return [$job, $jobAttachments];
        } else {
            // If no new attachments are uploaded
            // Edit job
            try {
                $this->jobRepository->editJob($job);
            } catch (Exception $e) {
                throw HttpExceptionFactory::createInternalServerError("An error occurred while editing job posting");
            }

            return [$job, []];
        }
    }

    /**
     * Delete a job posting for current logged in company
     */


    /**
     * Delete a job attachment
     */
    public function deleteJobAttachment(string $currentUserId, string $jobAttachmentId): void
    {
        // Validate job attachment
        try {
            $jobAttachment = $this->jobRepository->getJobAttachmentById($jobAttachmentId);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching job attachment");
        }

        // If job attachment not found
        if ($jobAttachment == null) {
            throw HttpExceptionFactory::createNotFound("Job attachment not found");
        }

        // Validate job
        try {
            $job = $this->jobRepository->getJobByIdWithAttachments($jobAttachment->getJobId());
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching job posting");
        }

        // If job not found
        if ($job == null) {
            throw HttpExceptionFactory::createNotFound("Job posting not found");
        }

        // Check if authorized
        if ($job->getCompanyId() != $currentUserId) {
            throw HttpExceptionFactory::createForbidden("You are not authorized to delete this job attachment");
        }

        // If attachment is the only one
        if (count($job->getAttachments()) == 1) {
            throw HttpExceptionFactory::createBadRequest("Job posting must have at least one attachment");
        }

        // Delete job attachment
        try {
            $this->jobRepository->deleteJobAttachment($jobAttachment);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while deleting job attachment");
        }

        // Delete file from server
        try {
            $this->uploadService->deleteOneFile($jobAttachment->getFilePath());
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while deleting job attachment");
        }
    }
}
