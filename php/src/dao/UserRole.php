<?php

namespace src\dao;

enum UserRole: string
{
    case JOBSEEKER = 'jobseeker';
    case COMPANY = 'company';

    public static function fromString(string $role): UserRole
    {
        return match ($role) {
            'jobseeker' => UserRole::JOBSEEKER,
            'company' => UserRole::COMPANY,
            default => throw new \InvalidArgumentException("Invalid role: $role")
        };
    }
}
