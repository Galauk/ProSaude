<?php

namespace App\Database;

use PDO;

abstract class Executable
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}