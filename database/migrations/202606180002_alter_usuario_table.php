<?php

use App\Database\Migration;

return new class($this->pdo) extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN email VARCHAR(255) UNIQUE
        ");

        $this->pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN perfil VARCHAR(50)
            NOT NULL
            DEFAULT 'USUARIO'
        ");

        $this->pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN ativo BOOLEAN
            NOT NULL
            DEFAULT TRUE
        ");

        $this->pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN ultimo_login TIMESTAMP
        ");

        $this->pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN updated_at TIMESTAMP
            NOT NULL
            DEFAULT NOW()
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("
            ALTER TABLE usuario
            DROP COLUMN email,
            DROP COLUMN perfil,
            DROP COLUMN ativo,
            DROP COLUMN ultimo_login,
            DROP COLUMN updated_at
        ");
    }
};