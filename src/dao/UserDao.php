<?php

namespace src\dao;

class UserDao
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private UserRole $role;

    public function __construct(int $id, string $name, string $email, string $password, UserRole $role)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getRole(): UserRole
    {
        return $this->role;
    }
    public function getHashedPassword(): string
    {
        return $this->password;
    }
}
