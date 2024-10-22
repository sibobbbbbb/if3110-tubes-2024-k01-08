<?php

namespace src\services;

use DateTime;
use Exception;
use src\dao\{LocationType, JobType, JobDao, CompanyDetailDao};
use src\exceptions\HttpExceptionFactory;
use src\repositories\{ApplicationRepository, JobRepository, UserRepository};

/* Job seeker */

class JobService extends Service
{

    // Dependency injection
    private JobRepository $jobRepository;
    private ApplicationRepository $applicationRepository;
    private UserRepository $userRepository;

    public function __construct(JobRepository $jobRepository, ApplicationRepository $applicationRepository, UserRepository $userRepository)
    {
        $this->jobRepository = $jobRepository;
        $this->applicationRepository = $applicationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get many jobs with filter (job type, location type, is open)
     * Company id is validated through middleware
     */
    public function getJobs(?array $jobTypes, ?array $locationTypes, ?DateTime $createdAtFrom, ?DateTime $createdAtTo, ?string $search, bool $isCreatedAtAsc, ?int $page): array
    {
        // Set limit to only 10
        $limit = 10;

        // Get company's jobs with filter
        try {
            [$jobs, $meta] = $this->jobRepository->getJobsWithFilter($jobTypes, $locationTypes, $createdAtFrom, $createdAtTo, $search, $isCreatedAtAsc, $page, $limit);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching company's job postings");
        }

        return [$jobs, $meta];
    }

    /**
     * Get a job by id (includes the company detail & attachments)
     */
    public function getJobDetail(?string $currentUserId, int $jobId)
    {
        // Get job detail with attachments
        try {
            $job = $this->jobRepository->getJobByIdWithAttachments($jobId);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching job detail");
        }

        // If job is closed
        if (!$job->getIsOpen()) {
            throw HttpExceptionFactory::createNotFound("Job posting not found");
        }

        // Get comapny detail
        try {
            $companyDetail = $this->userRepository->findCompanyDetailByUserId($job->getCompanyId());
            $job->setCompanyDetail($companyDetail);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching company detail");
        }

        // If current user is not null, get application if any
        if ($currentUserId != null) {
            try {
                $application = $this->applicationRepository->getJobAplicationByUserIdJobId($currentUserId, $jobId);
            } catch (Exception $e) {
                throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching user's job application");
            }

            return [$job, $application];
        }

        return [$job, null];
    }


    /**
     * Get user's job application history (paginated)
     * Userid is validated through middleware
     */
    public function getApplicationsHistory(int $user_id, int $page): array
    {
        // Set limit to only 10
        $limit = 10;

        // Get user's job application history
        try {
            [$applications, $meta] = $this->applicationRepository->getUserApplications($user_id, $page, $limit);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching user's job applications");
        }

        return [$applications, $meta];
    }
}
