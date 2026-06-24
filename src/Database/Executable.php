<?php

namespace App\Database;

use PDO;

abstract class Executable
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    protected function invoke(
        object $instance,
        string $method
    ): void
    {
        $reflection = new \ReflectionMethod(
            $instance,
            $method
        );

        if ($reflection->getNumberOfParameters() > 0) {
            $instance->$method($this->pdo);
        } else {
            $instance->$method();
        }
    }
}