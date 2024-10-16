<?php

namespace src\core;

// type hinting for environment variables keys
enum ENV_KEY: string
{
    case POSTGRES_HOST = 'POSTGRES_HOST';
    case POSTGRES_PORT = 'POSTGRES_PORT';
    case POSTGRES_DB = 'POSTGRES_DB';
    case POSTGRES_USER = 'POSTGRES_USER';
    case POSTGRES_PASSWORD = 'POSTGRES_PASSWORD';
}

// Config class to store environment variables
class Config
{
    // associative array to store configuration
    private $config = [];

    public function __construct()
    {
        foreach ($_ENV as $key => $value) {
            // echo $key, $value;
            $this->config[$key] = $value;
        }
    }

    public function get(ENV_KEY $key): string | null
    {
        return $this->config[$key->value] ?? null;
    }
}
