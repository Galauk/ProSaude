<?php

use App\Database\Migration;

return new class($this->pdo) extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE usuario (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                nome VARCHAR(255) NOT NULL,
                login VARCHAR(100) UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT NOW()
            )
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("
            DROP TABLE usuario
        ");
    }
};