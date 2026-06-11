<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

\App\Core\Config::load(__DIR__ . '/../.env');

$router = new App\Routing\Router();

require_once __DIR__ . '/../config/routes.php';

$router->dispatch();
