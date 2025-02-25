<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\database\postgres;

use PgSql\Connection;

class Database
{
    public function __construct(
        private string $host = "localhost",
        private int $port = 5432,
        private string $dbname = "postgres",
        private string $user = "postgres",
        private string $password = "postgres",
    ) {}

    private ?Connection $connection = null;

    public function connect(): Connection
    {
        if (! $this->connection) {
            $connectionString = 
                sprintf(
                    "host=%s port=%d dbname=%s user=%s password=%s",
                    $this->host,
                    $this->port,
                    $this->dbname,
                    $this->user,
                    $this->password,
                );
            $connection = pg_connect($connectionString);
            if ($connection) $this->connection = $connection;
        }
        return $this->connection;
    }

    public function disconnect(): void
    {
        if ($this->connection) pg_close($this->connection);
    }
}
