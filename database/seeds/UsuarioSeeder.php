<?php

use App\Database\Seeder;
use PDO;

class UsuarioSeeder extends Seeder
{
    public function run(PDO $pdo): void
    {
        $stmt = $pdo->prepare("
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

        $stmt = $pdo->prepare("
            INSERT INTO usuario
            (
                nome,
                login,
                email,
                senha,
                perfil,
                ativo
            )
            VALUES
            (
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
            'email'  => 'admin@localhost',
            'senha'  => password_hash('admin123', PASSWORD_BCRYPT),
            'perfil' => 'ADMIN',
            'ativo'  => true
        ]);
    }
}