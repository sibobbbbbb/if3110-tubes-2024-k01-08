<?php

namespace src\dao;

use DateTime;

class ApplicationDao
{
    private int $application_id;
    private int $user_id;
    private int $job_id;
    private string $cv_path;
    private ?string $video_path;
    private ApplicationStatus $status;
    private ?string $status_reason;
    private DateTime $created_at;

    public function __construct(int $application_id, int $user_id, int $job_id, string $cv_path, ?string $video_path, ApplicationStatus $status, ?string $status_reason, DateTime $created_at)
    {
        $this->application_id = $application_id;
        $this->user_id = $user_id;
        $this->job_id = $job_id;
        $this->cv_path = $cv_path;
        $this->video_path = $video_path;
        $this->status = $status;
        $this->status_reason = $status_reason;
        $this->created_at = $created_at;
    }

    public function getApplicationId(): int
    {
        return $this->application_id;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function getJobId(): int
    {
        return $this->job_id;
    }
    public function getCvPath(): string
    {
        return $this->cv_path;
    }
    public function getVideoPath(): ?string
    {
        return $this->video_path;
    }
    public function getStatus(): ApplicationStatus
    {
        return $this->status;
    }
    public function getStatusReason(): ?string
    {
        return $this->status_reason;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCvPath(string $cv_path): void
    {
        $this->cv_path = $cv_path;
    }
    public function setVideoPath(?string $video_path): void
    {
        $this->video_path = $video_path;
    }
    public function setStatus(ApplicationStatus $status): void
    {
        $this->status = $status;
    }
    public function setStatusReason(?string $status_reason): void
    {
        $this->status_reason = $status_reason;
    }
}
