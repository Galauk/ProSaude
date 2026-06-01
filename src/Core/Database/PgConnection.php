<?php

declare(strict_types=1);

namespace App\Core\Database;

use RuntimeException;

final class PgConnection
{
    private string $connectionString;
    private ?resource $resource = null;

    public function __construct(string $connectionString)
    {
        $this->connectionString = $connectionString;
    }

    public function connect(): resource
    {
        if ($this->resource === null) {
            $connection = pg_connect($this->connectionString);
            if ($connection === false) {
                throw new RuntimeException('Falha ao conectar ao PostgreSQL.');
            }
            $this->resource = $connection;
        }

        return $this->resource;
    }

    public function disconnect(): void
    {
        if ($this->resource !== null) {
            pg_close($this->resource);
            $this->resource = null;
        }
    }
}
