<?php

namespace App\Database;

use App\Database\Migration;

class MigrationRunner extends Migration
{
    public function run(): void
    {
        $this->createMigrationTable();

        $executadas = $this->getExecutedMigrations();

        foreach (
            glob(
                database_path('migrations/*.php')
            )
            as $file
        ) {

            $migrationName = basename($file);

            if (
                in_array(
                    $migrationName,
                    $executadas
                )
            ) {
                continue;
            }

            $migration = require $file;

            $migration->up();

            $this->registerMigration(
                $migrationName
            );

            echo "Migration executada: {$migrationName}\n";
        }
    }

    private function createMigrationTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) UNIQUE,
                executed_at TIMESTAMP DEFAULT NOW()
            )
        ");
    }

    private function getExecutedMigrations(): array
    {
        return $this->pdo
            ->query(
                "SELECT migration FROM migrations"
            )
            ->fetchAll(
                PDO::FETCH_COLUMN
            );
    }

    private function registerMigration(
        string $migration
    ): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO migrations
            (
                migration
            )
            VALUES
            (
                :migration
            )
        ");

        $stmt->execute([
            'migration' => $migration
        ]);
    }
}