<?php

require 'vendor/autoload.php';

use App\Core\Database;
use App\Core\Environment;
use App\Database\Migrator;

Environment::load(".env");
$pdo = Database::getConnection();

$migrator = new Migrator(
    $pdo
);

$migrator->migrate();