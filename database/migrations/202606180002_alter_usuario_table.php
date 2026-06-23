<?php

use App\Database\Migration;

return new class extends Migration
{
    public function up(\PDO $pdo): void
    {
        $pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN email VARCHAR(255) UNIQUE
        ");

        $pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN perfil VARCHAR(50)
            NOT NULL
            DEFAULT 'USUARIO'
        ");

        $pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN ativo BOOLEAN
            NOT NULL
            DEFAULT TRUE
        ");

        $pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN ultimo_login TIMESTAMP
        ");

        $pdo->exec("
            ALTER TABLE usuario
            ADD COLUMN updated_at TIMESTAMP
            NOT NULL
            DEFAULT NOW()
        ");
    }

    public function down(\PDO $pdo): void
    {
        $pdo->exec("
            ALTER TABLE usuario
            DROP COLUMN email,
            DROP COLUMN perfil,
            DROP COLUMN ativo,
            DROP COLUMN ultimo_login,
            DROP COLUMN updated_at
        ");
    }
};