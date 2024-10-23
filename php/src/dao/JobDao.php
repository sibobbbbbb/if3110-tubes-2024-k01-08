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

    // Denormalized data (only for GET)
    // array of JobAttachmentDao
    private array $attachments;

    // UserDao (companyId)
    private UserDao $company;

    // Company detail
    private CompanyDetailDao $companyDetail;

    public function __construct(int $job_id, int $company_id, string $position, string $description, string $job_type, string $location_type, bool $is_open, DateTime $created_at, DateTime $updated_at)
    {
        $this->job_id = $job_id;
        $this->company_id = $company_id;
        $this->position = $position;
        $this->description = $description;
        $this->job_type = JobType::fromString($job_type);
        $this->location_type = LocationType::fromString($location_type);
        $this->is_open = $is_open;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->attachments = [];
    }

    public static function fromRaw(array $raw): JobDao
    {
        return new JobDao($raw['job_id'], $raw['company_id'], $raw['position'], $raw['description'], $raw['job_type'], $raw['location_type'], $raw['is_open'], new DateTime($raw['created_at']), new DateTime($raw['updated_at']));
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
    public function getAttachments(): array
    {
        return $this->attachments;
    }
    public function getCompany(): UserDao
    {
        return $this->company;
    }
    public function getCompanyDetail(): CompanyDetailDao
    {
        return $this->companyDetail;
    }

    public function setJobId(int $job_id): void
    {
        $this->job_id = $job_id;
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
    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }
    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }
    public function setCompany(UserDao $company): void
    {
        $this->company = $company;
    }
    public function setCompanyDetail(CompanyDetailDao $companyDetail): void
    {
        $this->companyDetail = $companyDetail;
    }
}
