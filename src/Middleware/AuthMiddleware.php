<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        // Iniciar sessão se não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Se o usuário não está logado, redirecionar para login
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
    }
}