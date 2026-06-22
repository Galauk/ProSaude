<?php

require 'vendor/autoload.php';

use App\Core\Connection;
use App\Core\Environment;
use App\Database\SeedRunner;

Environment::load(".env");
$pdo = Connection::getConnection();

$seeder = new SeedRunner(
    $pdo
);

$seeder->migrate();