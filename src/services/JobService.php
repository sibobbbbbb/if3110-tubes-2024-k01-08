<?php

namespace src\services;

use DateTime;
use Exception;
use src\dao\{LocationType, JobType, JobDao, CompanyDetailDao};
use src\dao\ApplicationDao;
use src\exceptions\HttpExceptionFactory;
use src\repositories\{ApplicationRepository, JobRepository, UserRepository};
use src\services\UploadService;
use src\utils\UserSession;

/* Job seeker */

class JobService extends Service
{

    // Dependency injection
    private JobRepository $jobRepository;
    private ApplicationRepository $applicationRepository;
    private UserRepository $userRepository;
    private UploadService $uploadService;

    public function __construct(JobRepository $jobRepository, ApplicationRepository $applicationRepository, UserRepository $userRepository, UploadService $uploadService)
    {
        $this->jobRepository = $jobRepository;
        $this->applicationRepository = $applicationRepository;
        $this->userRepository = $userRepository;
        $this->uploadService = $uploadService;
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
     * Check if user has already applied for the job
     */
    public function isApplied(int $user_id, int $job_id): bool
    {
        // Check if user has already applied for the job
        try {
            $application = $this->applicationRepository->getJobAplicationByUserIdJobId($user_id, $job_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching user's job application");
        }
        return $application != null;
    }

    /**
     * Apply for a job
     * Userid is validated through middleware
     * @param int $user_id
     * @param int $job_id
     * @param string $cv
     * @param string $video
     * @return string - file path of the uploaded
     */
    public function applyJob(int $user_id, int $job_id, array $rawCv, array $rawVideo): ApplicationDao
    {
        // Check if user has already applied for the job
        try {
            $application = $this->applicationRepository->getJobAplicationByUserIdJobId($user_id, $job_id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw HttpExceptionFactory::createInternalServerError("An error occurred while fetching user's job application");
        }

        // If application exists, cannot apply again
        if ($application != null) {
            throw HttpExceptionFactory::createConflict("You have already applied for this job");
        }

        // Upload CV to server
        try {
            $directoryCvFromPublic = '/uploads/applications/jobs/[jobId]/users/[userId]/';
            $uploadedCvPath = $this->uploadService->uploadOneFile($directoryCvFromPublic, $rawCv);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while uploading CV");
        }
        // echo var_dump($rawVideo);
        $isVideoEmpty = $rawVideo['error'] == UPLOAD_ERR_NO_FILE;
        if (!$isVideoEmpty) {
            // Upload video to server
            try {
                $directoryVideoFromPublic = '/uploads/applications/video/';
                $uploadedVideoPath = $this->uploadService->uploadOneFile($directoryVideoFromPublic, $rawVideo);
            } catch (Exception $e) {
                echo $e->getMessage();
                throw HttpExceptionFactory::createInternalServerError("An error occurred while uploading video");
            }
        }        

        // Create application
        try {
            $application = $this->applicationRepository->createApplication($user_id, $job_id, $uploadedCvPath, $uploadedVideoPath);
        } catch (Exception $e) {
            throw HttpExceptionFactory::createInternalServerError("An error occurred while applying for job");
        }

        return $application;
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

    /**
     * Get many jobs Recommendation
     */
    public function getJobsRecommendation(): array
    {   
        // Find the user by email
        $user = $this->userRepository->findUserByEmail(UserSession::getUserEmail());

        try {
            $jobs = $this->jobRepository->selectJobRecommendation($user->getId());
        } catch (\PDOException $e) {
            if ($e->getCode() == "23505") {
                throw HttpExceptionFactory::createBadRequest($e->getMessage());
            }
            throw HttpExceptionFactory::createInternalServerError("An error occurred while creating account");
        }

        return $jobs ?? [];
    }
}
