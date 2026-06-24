<?php

namespace App\Database;

use PDO;
use Exception;

class MigrationRunner extends Executable
{
    public function run(): void
    {
        $this->createMigrationTable();
        $executadas = $this->getExecutedMigrations();

        $files = glob(database_path('migrations/*.php'));

        if (empty($files)) {
            echo "Nenhuma migration encontrada em: " . database_path('migrations') . "\n";
            return;
        }

        echo "Encontradas " . count($files) . " migrations.\n\n";

        foreach ($files as $file) {
            $migrationName = basename($file);

            if (in_array($migrationName, $executadas)) {
                echo "Já executada: {$migrationName}\n";
                continue;
            }

            try {
                echo "Executando: {$migrationName}\n";
                
                $migration = require $file;

                if (!$migration instanceof Migration) {
                    echo "{$migrationName} não é uma Migration válida.\n";
                    continue;
                }

                $this->invoke($migration,'up');

                $this->registerMigration($migrationName);
                echo "Migration executada com sucesso: {$migrationName}\n\n";

            } catch (Exception $e) {
                echo "Erro na migration {$migrationName}: " . $e->getMessage() . "\n\n";
            }
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
            ->query("SELECT migration FROM migrations")
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    private function registerMigration(string $migration): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO migrations (migration)
            VALUES (:migration)
        ");
        $stmt->execute(['migration' => $migration]);
    }
}