<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\UsuarioController;

use App\Middleware\AuthMiddleware;
use App\Middleware\SessionMiddleware;

if(!isset($router)) {
    die('Acesso negado');
}

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

$router->get(
    '/',
    [HomeController::class, 'index']
);


$router->get(
    '/sobre',
    [HomeController::class, 'sobre']
);

$router->get(
    '/login',
    [AuthController::class, 'login']
);

$router->post(
    '/autenticar',
    [AuthController::class, 'autenticar']
);

/*
|--------------------------------------------------------------------------
| Rotas Protegidas
|--------------------------------------------------------------------------
*/
$router->get(
    '/prosaude',
    [DashboardController::class, 'index'],
    [AuthMiddleware::class,SessionMiddleware::class]
);

$router->get(
    '/prosaude/dashboard',
    [DashboardController::class, 'index'],
    [AuthMiddleware::class,SessionMiddleware::class]
);

$router->get(
    '/prosaude/usuarios',
    [UsuarioController::class, 'listar'],
    [AuthMiddleware::class,SessionMiddleware::class]
);

$router->get(
    '/prosaude/usuarios/criar',
    [UsuarioController::class, 'criar'],
    [AuthMiddleware::class,SessionMiddleware::class]
);

$router->post(
    '/prosaude/usuarios',
    [UsuarioController::class, 'salvar'],
    [AuthMiddleware::class,SessionMiddleware::class]
);

$router->get(
    '/prosaude/logout',
    [AuthController::class, 'logout'],
    [AuthMiddleware::class]
);

$router->get(
    '/logout',
    [AuthController::class, 'logout'],
    [AuthMiddleware::class]
);