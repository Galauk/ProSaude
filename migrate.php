<?php

require 'vendor/autoload.php';

use App\Core\Connection;
use App\Core\Environment;
use App\Database\MigrationRunner;

Environment::load(".env");
$pdo = Connection::getConnection();

$migrator = new MigrationRunner(
    $pdo
);

$migrator->migrate();