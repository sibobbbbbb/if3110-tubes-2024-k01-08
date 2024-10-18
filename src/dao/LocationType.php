<?php

namespace src\dao;

enum LocationType: string
{
    case ON_SITE = 'on-site';
    case HYBRID = 'hybrid';
    case REMOTE = 'remote';

    public static function getValues(): array
    {
        return [
            self::ON_SITE->value,
            self::HYBRID->value,
            self::REMOTE->value,
        ];
    }

    public static function fromString($location): LocationType
    {
        return match ($location) {
            'on-site' => LocationType::ON_SITE,
            'hybrid' => LocationType::HYBRID,
            'remote' => LocationType::REMOTE,
            default => throw new \InvalidArgumentException("Invalid location: $location")
        };
    }
}
