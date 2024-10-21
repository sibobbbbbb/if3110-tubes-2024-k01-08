<?php

namespace src\repositories;

use DateTime;
use Exception;
use src\dao\JobAttachmentDao;
use src\dao\JobDao;
use src\dao\JobType;
use src\dao\LocationType;
use src\dao\PaginationMetaDao;
use src\dao\UserDao;
use src\database\Database;

class JobRepository extends Repository
{
    // Dependency injection
    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    /**
     * Create a new job posting with attachments
     * @param int $position - The job position
     * @param string $description - The job description
     * @param JobType $jobType - The job type
     * @param LocationType $locationType - The location type
     * @param array $attachmentPaths - Array of file paths for attachments
     * @throws PdoException
     * @return [jobs, attachments] - The new job and attachments
     */
    public function createJobAndAttachments(int $comapanyId, string $position, string $description, JobType $jobType, LocationType $locationType, array $attachmentPaths): array
    {
        // Begin transaction
        try {
            $this->db->beginTransaction();

            // Insert the job
            $newJob = $this->createJob($comapanyId, $position, $description, $jobType, $locationType);

            // Insert the attachments
            $newAttachments = $this->createJobAttachments($newJob->getJobId(), $attachmentPaths);

            // Insert the attachments
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }

        return [$newJob, $newAttachments];
    }

    /**
     * Get job from all company
     * @param ?array isOpens - The job is open or not
     * @param ?array jobTypes - The job types
     * @param ?array locationTypes - The location types
     * @param ?DateTime createdAtFrom - The start date
     * @param ?DateTime createdAtTo - The end date
     * @param ?string search - The search string
     * @param bool isCreatedAtAsc - The order of created at
     * @param ?int page - The page
     * @param ?int limit - The limit
     * @returns [array of JobDao, meta dao] - The jobs
     */
    public function getJobsWithFilter(?array $isOpens, ?array $jobTypes, ?array $locationTypes, ?DateTime $createdAtFrom, ?DateTime $createdAtTo, ?string $search, bool $isCreatedAtAsc, int $page, int $limit): array
    {
        $totalItemsQuery = "SELECT COUNT(*) FROM jobs INNER JOIN users ON jobs.company_id = users.id";
        $query = "SELECT * FROM jobs INNER JOIN users ON jobs.company_id = users.id";

        $params = [];
        $conditions = [];

        if ($isOpens !== null) {
            $conditions[] = "is_open = ANY(:is_opens)";
            $params[":is_opens"] = '{' . implode(',', array_map(function ($isOpen) {
                return $isOpen ? 't' : 'f';
            }, $isOpens)) . '}';
        }

        if ($jobTypes !== null) {
            $conditions[] = "job_type = ANY(:job_types)";
            $params[":job_types"] = '{' . implode(',', array_map(function ($jobType) {
                return $jobType->value;
            }, $jobTypes)) . '}';
        }

        if ($locationTypes !== null) {
            $conditions[] = "location_type = ANY(:location_types)";
            $params[":location_types"] = '{' . implode(',', array_map(function ($locationType) {
                return $locationType->value;
            }, $locationTypes)) . '}';
        }

        if ($createdAtFrom !== null) {
            $conditions[] = "created_at >= :created_at_from";
            $createdAtFrom->setTime(0, 0, 0);
            $params[':created_at_from'] = $createdAtFrom->format('Y-m-d H:i:s');
        }

        if ($createdAtTo !== null) {
            $conditions[] = "created_at <= :created_at_to";
            $createdAtTo->setTime(23, 59, 59);
            $params[':created_at_to'] = $createdAtTo->format('Y-m-d H:i:s');
        }

        if ($search !== null) {
            // position, description, company name
            $conditions[] = "(position ILIKE :search OR description ILIKE :search OR users.name ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
            $totalItemsQuery .= " WHERE " . implode(" AND ", $conditions);
        }

        // Get the total count  
        $totalItems = $this->db->queryOne($totalItemsQuery, $params)[0];

        // Get data
        $query .= " ORDER BY created_at " . ($isCreatedAtAsc ? "ASC" : "DESC");
        $query .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = ($page - 1) * $limit;

        $results = $this->db->queryMany($query, $params);

        // Parse data to dao
        $jobs = [];
        foreach ($results as $result) {
            $newCompany = UserDao::fromRaw($result);
            $newJob = JobDao::fromRaw($result);
            $newJob->setCompany($newCompany);

            $jobs[] = $newJob;
        }

        // Parse to meta dao
        $meta = new PaginationMetaDao($page, $limit, $totalItems);

        return [$jobs, $meta];
    }

    /**
     * Get company's job
     * @param int $companyId - The company id
     * @param ?array isOpens - The job is open or not
     * @param ?array jobTypes - The job types
     * @param ?array locationTypes - The location types
     * @param ?DateTime createdAtFrom - The start date
     * @param ?DateTime createdAtTo - The end date
     * @param ?string search - The search string
     * @param bool isCreatedAtAsc - The order of created at
     * @param ?int page - The page
     * @param ?int limit - The limit
     * @returns [array of JobDao, meta dao] - The jobs
     */
    public function getCompanyJobsWithFilter(int $companyId, ?array $isOpens, ?array $jobTypes, ?array $locationTypes, ?DateTime $createdAtFrom, ?DateTime $createdAtTo, ?string $search, bool $isCreatedAtAsc, int $page, int $limit): array
    {
        $totalItemsQuery = "SELECT COUNT(*) FROM jobs WHERE company_id = :company_id";
        $query = "SELECT * FROM jobs WHERE company_id = :company_id";
        $params = [':company_id' => $companyId];

        if ($isOpens !== null) {
            $commonQuery = " AND is_open = ANY(:is_opens)";

            $query .= $commonQuery;
            $totalItemsQuery .= $commonQuery;

            $params[":is_opens"] = '{' . implode(',', array_map(function ($isOpen) {
                return $isOpen ? 't' : 'f';
            }, $isOpens)) . '}';
        }

        if ($jobTypes !== null) {
            $commonQuery = " AND job_type = ANY(:job_types)";

            $query .= $commonQuery;
            $totalItemsQuery .= $commonQuery;

            $params[":job_types"] = '{' . implode(',', array_map(function ($jobType) {
                return $jobType->value;
            }, $jobTypes)) . '}';
        }

        if ($locationTypes !== null) {
            $commonQuery = " AND location_type = ANY(:location_types)";

            $query .= $commonQuery;
            $totalItemsQuery .= $commonQuery;

            $params[":location_types"] = '{' . implode(',', array_map(function ($locationType) {
                return $locationType->value;
            }, $locationTypes)) . '}';
        }

        if ($createdAtFrom !== null) {
            $query .= " AND created_at >= :created_at_from";
            $totalItemsQuery .= " AND created_at >= :created_at_from";

            // from the date at 00:00:00
            $createdAtFrom->setTime(0, 0, 0);
            $params[':created_at_from'] = $createdAtFrom->format('Y-m-d H:i:s');
        }

        if ($createdAtTo !== null) {
            $query .= " AND created_at <= :created_at_to";
            $totalItemsQuery .= " AND created_at <= :created_at_to";

            // to the date at 23:59:59
            $createdAtTo->setTime(23, 59, 59);
            $params[':created_at_to'] = $createdAtTo->format('Y-m-d H:i:s');
        }

        if ($search !== null) {
            $query .= " AND (position ILIKE :search OR description ILIKE :search)";
            $totalItemsQuery .= " AND (position ILIKE :search OR description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Get the total count  
        $totalItems = $this->db->queryOne($totalItemsQuery, $params)[0];

        // Get data
        $query .= " ORDER BY created_at " . ($isCreatedAtAsc ? "ASC" : "DESC");
        $query .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = ($page - 1) * $limit;

        $results = $this->db->queryMany($query, $params);

        // Parse data to dao
        $jobs = [];
        foreach ($results as $result) {
            $jobs[] = JobDao::fromRaw($result);
        }

        // Parse to meta dao
        $meta = new PaginationMetaDao($page, $limit, $totalItems);

        return [$jobs, $meta];
    }

    /**
     *  Edit a job posting with attachments
     * @param JobDao $job - The job object to be edited
     * @param array $attachmentPaths - Array of file paths for attachments
     * @throws PdoException
     * @return  array of JobAttachmentDao - The new attachments
     */
    public function editJobAndCreateAttachments(JobDao $job, array $attachmentPaths): array
    {
        // Begin transaction
        try {
            $this->db->beginTransaction();

            $this->editJob($job);

            $jobAttachments = $this->createJobAttachments($job->getJobId(), $attachmentPaths);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }

        return $jobAttachments;
    }

    /**
     * Delete a job posting with attachments (cascade delete job attachments)
     */
    public function deleteJobAndAttachments(JobDao $job): void
    {
        $query = "DELETE FROM jobs WHERE job_id = :job_id;";
        $params = [':job_id' => $job->getJobId()];

        $this->db->executeDelete($query, $params);
    }


    /**
     * Create a new job posting
     * @param int $companyId - The company id
     * @param int $position - The job position
     * @param string $description - The job description
     * @param JobType $jobType - The job type
     * @param LcoationType $locationType - The location type
     * @throws PdoException
     * @return JobDao - The new job
     */
    public function createJob(int $companyId, string $position, string $description, JobType $jobType, LocationType $locationType): JobDao
    {
        $query = "INSERT INTO jobs (company_id, position, description, job_type, location_type) VALUES (:company_id, :position, :description, :job_type, :location_type);";
        $params = [
            ':company_id' => $companyId,
            ':position' => $position,
            ':description' => $description,
            ':job_type' => $jobType->value,
            ':location_type' => $locationType->value
        ];

        $newJobId = $this->db->executeInsert($query, $params);
        return new JobDao($newJobId, $companyId, $position, $description, $jobType->value, $locationType->value, true, new DateTime(), new DateTime());
    }

    /**
     * Get a job by id
     */
    public function getJobById(int $jobId): JobDao|null
    {
        $query = "SELECT * FROM jobs WHERE job_id = :job_id;";
        $params = [':job_id' => $jobId];
        $result = $this->db->queryOne($query, $params);

        if ($result == false) return null;

        return JobDao::fromRaw($result);
    }

    /**
     * Get job by id with attachments also (denormliazed into jobDao)
     */
    public function getJobByIdWithAttachments(int $jobId): JobDao|null
    {
        $job = $this->getJobById($jobId);
        if ($job === null) {
            return null;
        }

        $attachments = $this->getJobAttachments($jobId);
        $job->setAttachments($attachments);

        return $job;
    }

    /**
     * Edit a job posting
     * @param JobDao $job - The job object to be edited
     * @throws PdoException
     * @return void
     */
    public function editJob(JobDao $job): void
    {
        $query = "UPDATE jobs SET position = :position, description = :description, job_type = :job_type, location_type = :location_type, is_open = :is_open WHERE job_id = :job_id;";
        $params = [
            ':job_id' => $job->getJobId(),
            ':position' => $job->getPosition(),
            ':description' => $job->getDescription(),
            ':is_open' => $job->getIsOpen() ? "t" : "f",
            ':job_type' => $job->getJobType()->value,
            ':location_type' => $job->getLocationType()->value,
        ];

        $this->db->executeUpdate($query, $params);
        $job->setUpdatedAt(new DateTime());
    }

    /**
     * Delete a job posting
     * @param JobDao $job - The job object to be deleted
     * @throws PdoException
     * @return void
     */
    public function deleteJob(JobDao $job): void
    {
        $query = "DELETE FROM jobs WHERE job_id = :job_id;";
        $params = [':job_id' => $job->getJobId()];
        $this->db->executeDelete($query, $params);
    }


    /**
     * Create job attachments
     * @param jobId - The job id to attach the files to
     * @param string[] $attachments - Array of file paths for attachments
     * @throws Exception
     * @return JobAttachmentDao[] - Array of job attachments
     */
    public function createJobAttachments(int $jobId, array $attachments): array
    {
        $newAttachments = [];
        foreach ($attachments as $path) {
            $query = "INSERT INTO job_attachments (job_id, file_path) VALUES (:job_id, :file_path);";
            $params = [':job_id' => $jobId, ':file_path' => $path];
            $newAttachmentId = $this->db->executeInsert($query, $params);
            $newAttachments[] = new JobAttachmentDao($newAttachmentId, $jobId, $path);
        }

        return $newAttachments;
    }

    /**
     * Get job attachments
     * @param int $jobId - The job id to get attachments for
     * @return JobAttachmentDao[] - Array of job attachments (empty if none)
     */
    public function getJobAttachments(int $jobId): array
    {
        $query = "SELECT * FROM job_attachments WHERE job_id = :job_id;";
        $params = [':job_id' => $jobId];
        $results = $this->db->queryMany($query, $params);

        $attachments = [];
        foreach ($results as $result) {
            $attachments[] = JobAttachmentDao::fromRaw($result);
        }

        return $attachments;
    }

    /**
     * Delete job's job attachments
     * @param JobDao $job - The job object to delete attachments from
     * @throws PdoException
     * @return void
     */
    public function deleteJobsJobAttachments(JobDao $job): void
    {
        $query = "DELETE FROM job_attachments WHERE job_id = :job_id;";
        $params = [':job_id' => $job->getJobId()];
        $this->db->executeDelete($query, $params);
    }

    /**
     * Get job attachment
     */
    public function getJobAttachmentById(int $attachmentId): JobAttachmentDao|null
    {
        $query = "SELECT * FROM job_attachments WHERE attachment_id = :attachment_id;";
        $params = [':attachment_id' => $attachmentId];
        $result = $this->db->queryOne($query, $params);

        if ($result == false) return null;


        return JobAttachmentDao::fromRaw($result);
    }

    /**
     * Delete one job attachment
     * @param JobAttachmentDao $attachment - The attachment to be deleted
     * @throws PdoException
     * @return void
     */
    public function deleteJobAttachment(JobAttachmentDao $attachment): void
    {
        $query = "DELETE FROM job_attachments WHERE attachment_id = :attachment_id;";
        $params = [':attachment_id' => $attachment->getAttachmentId()];
        $this->db->executeDelete($query, $params);
    }
}
