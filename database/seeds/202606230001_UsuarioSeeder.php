<?php

use App\Database\Seeder;

return new class($this->pdo) extends Seeder
{
    public function run(): void
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM usuario
            WHERE login = :login
        ");

        $stmt->execute([
            'login' => 'admin'
        ]);

        if ($stmt->fetchColumn() > 0) {
            return;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO usuario
            (
                id,
                nome,
                login,
                email,
                senha,
                perfil,
                ativo
            )
            VALUES
            (
                uuid_generate_v4(),
                :nome,
                :login,
                :email,
                :senha,
                :perfil,
                :ativo
            )
        ");

        $stmt->execute([
            'nome'   => 'Administrador',
            'login'  => 'admin',
            'email'  => 'admin@prosaude.local',
            'senha'  => password_hash(
                'admin123',
                PASSWORD_BCRYPT
            ),
            'perfil' => 'ADMIN',
            'ativo'  => true
        ]);
    }
};