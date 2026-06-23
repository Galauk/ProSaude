<?php

namespace App\Database;

use PDO;

class SeederRunner extends Seeder
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function run(): void
    {
        foreach (glob(database_path('seeds/*.php')) as $file) {
            $seeder = require $file;

            // Correção principal: injetar o PDO
            if (method_exists($seeder, 'setPdo')) {
                $seeder->setPdo($this->pdo);
            }

            $seeder->run();

            echo "Seeder executado: " . basename($file) . PHP_EOL;
        }
    }
}