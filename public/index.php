<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';


$router = new App\Routing\Router();

require_once __DIR__ . '/../config/routes.php';

$router->dispatch();
/*
$rota = $_GET['rota'] ?? 'login';

switch($rota){
    case 'login': (new AuthController())->login(); break;
    case 'autenticar': (new AuthController())->autenticar(); break;
    case 'dashboard': (new DashboardController())->index(); break;
    case 'logout': (new AuthController())->logout(); break;
}
    */