<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {

            try {

                $driver = $_ENV['DB_CONNECTION'] ?? 'pgsql';

                $host = $_ENV['DB_HOST'] ?? '127.0.0.1';

                $port = $_ENV['DB_PORT'] ?? '5432';

                $database = $_ENV['DB_DATABASE'] ?? '';

                $user = $_ENV['DB_USERNAME'] ?? '';

                $password = $_ENV['DB_PASSWORD'] ?? '';

                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s',
                    $driver,
                    $host,
                    $port,
                    $database
                );

                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

            } catch (PDOException $e) {

                error_log($e->getMessage());
                echo $e->getMessage();

                die('Erro interno de conexão.');
            }
        }

        return self::$instance;
    }
}