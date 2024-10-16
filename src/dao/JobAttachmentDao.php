<?php

namespace src\dao;

class JobAttachment
{
    private int $attachment_id;
    private int $job_id;
    private string $file_path;

    public function __construct(int $attachment_id, int $job_id, string $file_path)
    {
        $this->attachment_id = $attachment_id;
        $this->job_id = $job_id;
        $this->file_path = $file_path;
    }

    public function getAttachmentId(): int
    {
        return $this->attachment_id;
    }
    public function getJobId(): int
    {
        return $this->job_id;
    }
    public function getFilePath(): string
    {
        return $this->file_path;
    }
}
