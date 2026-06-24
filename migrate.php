<?php

require 'vendor/autoload.php';

use App\Core\Connection;
use App\Core\Environment;
use App\Database\MigrationRunner;

try{
    Environment::load(".env");
    $pdo = Connection::getConnection();

    $migrator = new MigrationRunner(
        $pdo
    );

    $migrator->run();
}catch(\Exception $e){
    echo "Erro ao executar migrations: \n";
    echo $e->getMessage()."\n";
    exit(1);
}