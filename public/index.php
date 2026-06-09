<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$router = new App\Routing\Router();

require_once __DIR__ . '/../config/routes.php';

$router->dispatch();
