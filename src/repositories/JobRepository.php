<?php

namespace src\repositories;

use DateTime;
use Exception;
use src\dao\JobAttachmentDao;
use src\dao\JobDao;
use src\dao\JobType;
use src\dao\LocationType;
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

            $jobAttachments =  $this->createJobAttachments($job->getJobId(), $attachmentPaths);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }

        return $jobAttachments;
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
    public function getJobById(int $jobId): JobDao | null
    {
        $query = "SELECT * FROM jobs WHERE job_id = :job_id;";
        $params = [':job_id' => $jobId];
        $result = $this->db->queryOne($query, $params);

        if ($result === null) {
            return null;
        }

        return JobDao::fromRaw($result);
    }

    /**
     * Get job by id with attachments also (denormliazed into jobDao)
     */
    public function getJobByIdWithAttachments(int $jobId): JobDao | null
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
    public function getJobAttachmentById(int $attachmentId): JobAttachmentDao | null
    {
        $query = "SELECT * FROM job_attachments WHERE attachment_id = :attachment_id;";
        $params = [':attachment_id' => $attachmentId];
        $result = $this->db->queryOne($query, $params);

        if ($result === null) {
            return null;
        }

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
