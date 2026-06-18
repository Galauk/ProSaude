<?php
namespace App\Database;

use  App\Core\Database;
use PDO;

class Migrator
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function migrate(): void
    {
        $this->createMigrationTable();

        $executadas = $this->getExecuted();

        foreach (
            glob(
                __DIR__ .
                '/../../database/migrations/*.php'
            )
            as $file
        ) {

            $name = basename($file);

            if (
                in_array(
                    $name,
                    $executadas
                )
            ) {
                continue;
            }

            $migration = require $file;

            $migration->up(
                $this->pdo
            );

            $this->register(
                $name
            );

            echo "Executada: {$name}\n";
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

    private function getExecuted(): array
    {
        return $this->pdo
            ->query(
                "SELECT migration FROM migrations"
            )
            ->fetchAll(
                PDO::FETCH_COLUMN
            );
    }

    private function register(
        string $migration
    ): void
    {
        $stmt =
            $this->pdo->prepare("
                INSERT INTO migrations
                (migration)
                VALUES (:migration)
            ");

        $stmt->execute([
            'migration' => $migration
        ]);
    }
}