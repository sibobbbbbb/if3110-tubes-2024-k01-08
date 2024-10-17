<?php

namespace src\repositories;

use PDO;
use src\dao\UserDao;
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

    // Find a user by email
    public function findUserByEmail(string $email): UserDao | null
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $params = [':email' => $email];

        // Query the database
        $result = $this->db->queryOne($query, $params);

        // If not found
        if ($result == false) return null;

        $user = UserDao::fromRaw($result);

        return $user;
    }

    // Find a user by id
    public function findUserById(int $id): UserDao | null
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $params = [':id' => $id];

        // Query the database
        $result = $this->db->queryOne($query, $params);

        // If not found
        if ($result == false) {
            return null;
        }

        $user = UserDao::fromRaw($result);

        return $user;
    }
}
