<?php

namespace src\dao;

enum ApplicationStatus: string
{
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case WAITING = 'waiting';

    public static function fromString(string $status): ApplicationStatus
    {
        return match ($status) {
            'accepted' => self::ACCEPTED,
            'rejected' => self::REJECTED,
            'waiting' => self::WAITING,
        };
    }
}
