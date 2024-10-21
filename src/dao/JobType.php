<?php

namespace src\dao;

enum JobType: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case INTERNSHIP = 'internship';

    public static function getValues(): array
    {
        return [
            self::FULL_TIME->value,
            self::PART_TIME->value,
            self::INTERNSHIP->value,
        ];
    }

    public static function fromString(string $jobType): JobType
    {
        return match ($jobType) {
            'full-time' => JobType::FULL_TIME,
            'part-time' => JobType::PART_TIME,
            'internship' => JobType::INTERNSHIP,
            default => throw new \InvalidArgumentException("Invalid job type: $jobType")
        };
    }

    public static function renderText(JobType $jobType): string
    {
        return match ($jobType) {
            JobType::FULL_TIME => 'Full-time',
            JobType::PART_TIME => 'Part-time',
            JobType::INTERNSHIP => 'Internship',
            default => throw new \InvalidArgumentException("Invalid job type: $jobType")
        };
    }
}
