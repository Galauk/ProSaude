<?php

require_once __DIR__ . '/public/index.php'; // ou bootstrap adequado

use App\Database\SeederRunner;
use App\Core\Connection; // ajuste conforme sua estrutura

$pdo = Connection::getConnection(); // ou como você pega o PDO

$runner = new SeederRunner($pdo);
$runner->run();

echo "Seeders concluídos!\n";