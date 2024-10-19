<?php

namespace src\repositories;

use Exception;
use PDO;
use src\dao\{UserDao, CompanyDetailDao};
use \src\database\Database;
use src\exceptions\HttpExceptionFactory;

/**
 * Repository for user model
 * Handles all database operations related to user
 */
class UserRepository extends Repository
{
    public function __construct(Database $db)
    {
        parent::__construct($db);
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

    // Find company detail of user id
    public function findCompanyDetailByUserId(int $userId): CompanyDetailDao | null
    {
        $query = "
            SELECT
                u.id as user_id,
                u.name,
                cd.location,
                cd.about
            FROM
                users u
                INNER JOIN company_details cd ON u.id = cd.user_id
            WHERE
                u.id = :userId
        ";
        $params = [':userId' => $userId];

        // Query the database
        $result = $this->db->queryOne($query, $params);

        // If not found
        if ($result == false) {
            return null;
        }

        $company = CompanyDetailDao::fromRaw($result);

        return $company;
    }

    // Update company detail
    public function updateCompanyDetail(CompanyDetailDao $updatedCompany): void
    {
        // Update user name
        $query1 = "UPDATE users SET name = :name WHERE id = :userId";
        $params1 = [
            ':userId' => $updatedCompany->getUserId(),
            ':name' =>  $updatedCompany->getName()
        ];

        // Update company details
        $query2 = "UPDATE company_details SET location = :location, about = :about WHERE user_id = :userId";
        $params2 = [
            ':userId' => $updatedCompany->getUserId(),
            ':location' => $updatedCompany->getLocation(),
            ':about' => $updatedCompany->getAbout()
        ];

        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Execute queries
            $this->db->executeUpdate($query1, $params1);
            $this->db->executeUpdate($query2, $params2);

            // Commit transaction
            $this->db->commit();
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            throw $e;
        }
    }

    // Insert Into User and company_details
    public function insertintouserjobseeker(string $name, string $email, string $password): void
    {
        try {

            $this->db->beginTransaction();

            $id = $this->generateUniqueId();

            // Insert into users table
            $query_insert_user = "INSERT INTO users  VALUES (:id, :name, :email, :password, 'jobseeker')";
            $params_user = [
                ':id' => $id,
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
            ];

            $result_user = $this->db->executeInsert($query_insert_user, $params_user);

            $this->db->commit();
            return;
        } catch (Exception $e) {
            // Rollback 
            $this->db->rollBack();

            throw HttpExceptionFactory::createBadRequest($e->getMessage());

            return;
        }
    }

    // Insert Into User and company_details
    public function insertintouserandcompany(string $name, string $email, string $password, string $location, string $about): void
    {
        try {

            $this->db->beginTransaction();

            $id = $this->generateUniqueId();

            // Insert into users table
            $query_insert_user = "INSERT INTO users  VALUES (:id, :name, :email, :password, 'company')";
            $params_user = [
                ':id' => $id,
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
            ];

            $result_user = $this->db->executeInsert($query_insert_user, $params_user);
            // var_dump('User Insert Result: ', $result_user);


            // Insert into company_details table
            $query_insert_company = "INSERT INTO company_details VALUES (:user_id, :location, :about)";
            $params_company = [
                ':user_id' => $id,
                ':location' => $location,
                ':about' => $about,
            ];

            $result_company = $this->db->executeInsert($query_insert_company, $params_company);
            // var_dump('Company Insert Result: ', $result_company);


            // Commit 
            $this->db->commit();
            return;
        } catch (Exception $e) {
            // Rollback 
            $this->db->rollBack();

            throw HttpExceptionFactory::createBadRequest($e->getMessage());

            return;
        }
    }

    private function generateUniqueId(): int
    {
        $query = "SELECT MAX(id) as max_id FROM users";
        $id = $this->db->queryOne($query);

        if ($id === false || !isset($id['max_id'])) {
            return 1;
        }

        return $id['max_id'] + 1;
    }
}
