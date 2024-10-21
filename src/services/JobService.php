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

    public function __construct(JobRepository $jobRepository, ApplicationRepository $applicationRepository)
    {
        $this->jobRepository = $jobRepository;
        $this->applicationRepository = $applicationRepository;
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
