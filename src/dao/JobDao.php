<?php

namespace src\dao;

use DateTime;

class JobDao
{
    private int $job_id;
    private int $company_id;
    private string $position;
    private string $description;
    private JobType $job_type;
    private LocationType $location_type;
    private bool $is_open;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(int $job_id, int $company_id, string $position, string $description, JobType $job_type, LocationType $location_type, bool $is_open, DateTime $created_at, DateTime $updated_at)
    {
        $this->job_id = $job_id;
        $this->company_id = $company_id;
        $this->position = $position;
        $this->description = $description;
        $this->job_type = $job_type;
        $this->location_type = $location_type;
        $this->is_open = $is_open;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getJobId(): int
    {
        return $this->job_id;
    }
    public function getCompanyId(): int
    {
        return $this->company_id;
    }
    public function getPosition(): string
    {
        return $this->position;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getJobType(): JobType
    {
        return $this->job_type;
    }
    public function getLocationType(): LocationType
    {
        return $this->location_type;
    }
    public function getIsOpen(): bool
    {
        return $this->is_open;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function setJobType(JobType $job_type): void
    {
        $this->job_type = $job_type;
    }
    public function setLocationType(LocationType $location_type): void
    {
        $this->location_type = $location_type;
    }
    public function setIsOpen(bool $is_open): void
    {
        $this->is_open = $is_open;
    }
}
