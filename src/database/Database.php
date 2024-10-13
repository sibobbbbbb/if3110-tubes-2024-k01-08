<?php


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

        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName};";
        try {
            $this->connection = new PDO($dsn, $dbUser, $dbPass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Internal server error
            echo 'Connection failed: ' . $e->getMessage();
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
     * Query the database
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
