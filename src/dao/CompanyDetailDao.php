<?php

namespace src\dao;

class CompanyDetail
{
    private int $user_id;
    private string $location;
    private string $about;

    public function __construct(int $user_id, string $location, string $about)
    {
        $this->user_id = $user_id;
        $this->location = $location;
        $this->about = $about;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getAbout(): string
    {
        return $this->about;
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
