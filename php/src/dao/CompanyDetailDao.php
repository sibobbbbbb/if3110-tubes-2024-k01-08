<?php

namespace src\dao;

class CompanyDetailDao
{
    private int $user_id;
    private string $location;
    private string $about;

    // Denormalized from User (only for GET)
    private string $name;

    public function __construct(int $user_id, string $name, string $location, string $about)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->location = $location;
        $this->about = $about;
    }

    public static function fromRaw(array $raw): CompanyDetailDao
    {
        return new CompanyDetailDao(
            $raw['user_id'],
            $raw['name'],
            $raw['location'],
            $raw['about']
        );
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getAbout(): string
    {
        return $this->about;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }
    public function setAbout(string $about): void
    {
        $this->about = $about;
    }
}
