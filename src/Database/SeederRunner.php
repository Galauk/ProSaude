<?php

namespace App\Database;

use App/Database/Seeder;

class SeederRunner extends Seeder
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function run(): void
    {
        foreach (
            glob(
                database_path('seeds/*.php')
            )
            as $file
        ) {

            $seeder = require $file;

            $seeder->run();

            echo "Seeder executado: "
                . basename($file)
                . PHP_EOL;
        }
    }
}