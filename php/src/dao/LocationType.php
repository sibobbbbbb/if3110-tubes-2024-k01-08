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

    public static function fromString(string $location): LocationType
    {
        return match ($location) {
            'on-site' => LocationType::ON_SITE,
            'hybrid' => LocationType::HYBRID,
            'remote' => LocationType::REMOTE,
            default => throw new \InvalidArgumentException("Invalid location: $location")
        };
    }

    public static function renderText(LocationType $location): string
    {
        return match ($location) {
            LocationType::ON_SITE => 'On-site',
            LocationType::HYBRID => 'Hybrid',
            LocationType::REMOTE => 'Remote',
            default => throw new \InvalidArgumentException("Invalid location: $location")
        };
    }
}
