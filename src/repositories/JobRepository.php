<?php

namespace src\repositories;

use DateTime;
use Exception;
use src\dao\JobAttachmentDao;
use src\dao\JobDao;
use src\database\Database;

class JobRepository extends Repository
{
    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // Create a new job and its attachments
    // Modifies the passed job object
    public function createJobAndAttachments(JobDao $job, array $targetPaths): void
    {
        // Begin transaction
        try {
            $this->db->beginTransaction();

            // Insert the job
            $this->createJob($job);
            $newJobId = $job->getJobId();

            // Insert the attachments
            $newAttachments = [];
            foreach ($targetPaths as $path) {
                $newAttachments[] = new JobAttachmentDao(0, $newJobId, $path);
            }
            $this->createJobAttachments($newAttachments);

            // Insert the attachments
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Create job
    public function createJob(JobDao $job): void
    {
        $query = "INSERT INTO jobs (company_id, position, description, job_type, location_type) VALUES (:company_id, :position, :description, :job_type, :location_type);";
        $params = [
            ':company_id' => $job->getCompanyId(),
            ':position' => $job->getPosition(),
            ':description' => $job->getDescription(),
            ':job_type' => $job->getJobType()->value,
            ':location_type' => $job->getLocationType()->value,
        ];

        $newJobId = $this->db->executeInsert($query, $params);
        $job->setJobId($newJobId);
        $job->setIsOpen(true);
        $job->setCreatedAt(new DateTime());
        $job->setUpdatedAt(new DateTime());
    }


    // Create one or more job attachments
    // Modifies the passed job attachments array
    public function createJobAttachments(array $attachments): void
    {
        $query = "INSERT INTO job_attachments (job_id, file_path) VALUES (:job_id, :file_path);";

        foreach ($attachments as $attachment) {
            $params = [
                ':job_id' => $attachment->getJobId(),
                ':file_path' => $attachment->getFilePath()
            ];
            $resultId = $this->db->executeInsert($query, $params);
            $attachment->setAttachmentId($resultId);
        }
    }
}
