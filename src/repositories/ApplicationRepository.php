<?php

namespace src\repositories;

use src\dao\{ApplicationDao, ApplicationStatus, JobDao, PaginationMetaDao, UserDao};
use src\database\Database;

class ApplicationRepository extends Repository
{
    // Dependency injection
    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    /**
     * Get user's job application history (paginated)
     * @param int user_id
     * @param int page
     * @return [ApplicationDao, PaginationMetaDao] array
     */
    public function getUserApplications(int $user_id, int $page, int $limit): array
    {
        $queryMeta = "SELECT COUNT(*) FROM applications WHERE user_id = :user_id";
        $params = [
            ':user_id' => $user_id,
        ];

        // Get meta
        $totalItems = $this->db->queryOne($queryMeta, $params)[0];

        // Get data
        $offset = ($page - 1) * $limit;
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        $queryData = "
            SELECT * 
            FROM 
                applications 
                INNER JOIN jobs ON applications.job_id = jobs.job_id
                INNER JOIN users ON jobs.company_id = users.id
            WHERE user_id = :user_id 
            ORDER BY applications.created_at DESC 
            LIMIT :limit 
            OFFSET :offset";

        $result = $this->db->queryMany($queryData, $params);

        // Parse data
        $applications = [];
        foreach ($result as $raw) {
            $company = UserDao::fromRaw($raw);

            $job = JobDao::fromRaw($raw);
            $job->setCompany($company);

            $application = ApplicationDao::fromRaw($raw);
            $application->setJob($job);

            $applications[] = $application;
        }

        // Parse meta
        $meta = new PaginationMetaDao($page, $limit, $totalItems);

        return [$applications, $meta];
    }

    /**
     * Get a company's job applications (paginated)
     * @param int job_id
     * @param int page
     * @param int limit
     * @return [ApplicationDao, PaginationMetaDao] array
     */
    public function getJobApplications(int $job_id, int $page, int $limit): array
    {
        $queryMeta = "SELECT COUNT(*) FROM applications WHERE job_id = :job_id";
        $params = [
            ':job_id' => $job_id,
        ];

        // Get meta
        $totalItems = $this->db->queryOne($queryMeta, $params)[0];

        // Get data
        $offset = ($page - 1) * $limit;
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        $queryData = "
            SELECT * 
            FROM 
                applications 
                INNER JOIN jobs ON applications.job_id = jobs.job_id
                INNER JOIN users ON applications.user_id = users.id 
            WHERE jobs.job_id = :job_id 
            ORDER BY applications.created_at DESC 
            LIMIT :limit 
            OFFSET :offset";
        $result = $this->db->queryMany($queryData, $params);

        // Parse data
        $applications = [];
        foreach ($result as $raw) {
            $application = ApplicationDao::fromRaw($raw);
            $user = UserDao::fromRaw($raw);
            $job = JobDao::fromRaw($raw);
            $application->setUser($user);
            $application->setJob($job);

            $applications[] = $application;
        }

        // Parse meta
        $meta = new PaginationMetaDao($page, $limit, $totalItems);

        return [$applications, $meta];
    }

    /**
     * Get an application by id including the user and job
     * @param int application_id
     * @return ApplicationDao | null
     */
    public function getOneJobApplication(int $application_id): ApplicationDao | null
    {
        $query = "
            SELECT * 
            FROM 
                applications 
                INNER JOIN jobs ON applications.job_id = jobs.job_id
                INNER JOIN users ON applications.user_id = users.id 
            WHERE application_id = :application_id";
        $params = [
            ':application_id' => $application_id,
        ];

        $result = $this->db->queryOne($query, $params);

        if ($result === false) return null;

        $application = ApplicationDao::fromRaw($result);
        $user = UserDao::fromRaw($result);
        $job = JobDao::fromRaw($result);
        $application->setUser($user);
        $application->setJob($job);

        return $application;
    }

    /**
     * Get job application by job id and user id (not candidate key but guarteed unique)
     */
    public function getJobAplicationByUserIdJobId(int $userId, int $jobId): ApplicationDao | null
    {
        $query = "SELECT * FROM applications WHERE user_id = :user_id AND job_id = :job_id";
        $params = [
            ':user_id' => $userId,
            ':job_id' => $jobId,
        ];

        $result = $this->db->queryOne($query, $params);
        if ($result == false) return null;

        $application = ApplicationDao::fromRaw($result);
        return $application;
    }

    /**
     * Update an application's status
     * @param ApplicationDao application
     * @return void
     */
    public function updateApplicationStatus(ApplicationDao $application): void
    {
        $query = "UPDATE applications SET status = :status, status_reason = :status_reason WHERE application_id = :application_id";
        $params = [
            ':application_id' => $application->getApplicationId(),
            ':status' => $application->getStatus()->value,
            ':status_reason' => $application->getStatusReason(),
        ];

        $this->db->executeUpdate($query, $params);

        return;
    }

    /**
     * Apply for a job
     * @param int user_id
     * @param int job_id
     * @param string cv
     * @param string video
     * @return void
     */
    public function createApplication(int $user_id, int $job_id, string $cvPath, string $videoPath): ApplicationDao
    {
        $query = "INSERT INTO applications (user_id, job_id, cv_path, video_path) VALUES (:user_id, :job_id, :cv, :video)";
        $params = [
            ':user_id' => $user_id,
            ':job_id' => $job_id,
            ':cv' => $cvPath,
            ':video' => $videoPath,
        ];

        // echo var_dump($params);

        $newApplicationId = $this->db->executeInsert($query, $params);

        return new ApplicationDao($newApplicationId, $user_id, $job_id, $cvPath,  $videoPath, ApplicationStatus::WAITING, null, new \DateTime());
    }
}
