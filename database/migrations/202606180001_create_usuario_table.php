<?php

use App\Database\Migration;

return new class extends Migration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE usuario (
                id UUID PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                login VARCHAR(100) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT NOW()
            )
        ");
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("
            DROP TABLE usuario
        ");
    }
};