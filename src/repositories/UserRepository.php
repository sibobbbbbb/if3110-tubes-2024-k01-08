<?php

namespace src\repositories;

use \src\database\Database;

/**
 * Repository for user model
 * Handles all database operations related to user
 */
class UserRepository extends Repository
{
    // Dependency injection
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Find user by id
     */
    // public function findById(int $id): User
    // {
    //     $query = "SELECT * FROM users WHERE id = :id";
    //     $arrResult = $this->db->query($query, [':id' => $id]);
    //     if (count($arrResult) === 0) {
    //         throw new NotFoundException("User not found");
    //     }
    // }
}
