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

    public static function fromString(string $role): JobType
    {
        return match ($role) {
            'full-time' => JobType::FULL_TIME,
            'part-time' => JobType::PART_TIME,
            'internship' => JobType::INTERNSHIP,
            default => throw new \InvalidArgumentException("Invalid role: $role")
        };
    }
}
