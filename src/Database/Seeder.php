<?php

namespace App\Database;

use PDO;

abstract class Seeder extends Executable
{
    abstract public function run(): void;

    // Método auxiliar para injetar PDO quando necessário
    public function setPdo(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }
}