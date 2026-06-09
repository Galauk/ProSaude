<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router->get('/', [AuthController::class, 'login']);

$router->post(
    '/login',
    [AuthController::class, 'autenticar']
);

$router->get(
    '/dashboard',
    [DashboardController::class, 'index']
);