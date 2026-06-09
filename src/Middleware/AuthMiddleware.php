<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!isset($_SESSION['usuario'])) {

            header('Location: /login');

            exit;
        }
    }
}