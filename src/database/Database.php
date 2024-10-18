<?php

namespace src\database;

use src\core\{Config, ENV_KEY};
use PDO;
use PDOException;
use src\exceptions\HttpExceptionFactory;

// Class for database connection
class Database
{
    // Dependency injection
    private Config $config;

    // Db connection
    private $connection = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Connect to the database
     */
    public function connect()
    {
        $dbHost = $this->config->get(ENV_KEY::POSTGRES_HOST);
        $dbPort = $this->config->get(ENV_KEY::POSTGRES_PORT);
        $dbName = $this->config->get(ENV_KEY::POSTGRES_DB);
        $dbUser = $this->config->get(ENV_KEY::POSTGRES_USER);
        $dbPass = $this->config->get(ENV_KEY::POSTGRES_PASSWORD);

        // $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;";
        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName};user={$dbUser};password={$dbPass}";
        try {
            $this->connection = new PDO($dsn);
            // enable exceptionss
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Internal server error
            throw HttpExceptionFactory::create(500, 'Cannot connect to the database');
        }
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * Query one row from the database
     */
    public function queryOne(string $sql, array $params = []): array | false
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Query many rows the database
     */
    public function queryMany(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a query (insert, update, delete)
     * Return the number of rows affected
     */
    public function executeUpdate(string $sql, array $params = []): int
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Execute a query (insert)
     * Returns the last inserted id
     */
    public function executeInsert(string $sql, array $params = []): int
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $this->connection->lastInsertId();
    }

    /**
     * Get the connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begins a transaction
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rolls back a transaction
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }
}
