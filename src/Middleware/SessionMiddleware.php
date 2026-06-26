<?php 
namespace App\Middleware;

class SessionMiddleware
{
    public function handle(): void
    {
        /*
        // Iniciar sessão se não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Configurar timeout da sessão (15 minutos)
        $timeout = 15 * 60;
        
        if (
            isset($_SESSION['LAST_ACTIVITY']) &&
            time() - $_SESSION['LAST_ACTIVITY'] > $timeout
        ) {
            // Sessão expirou, destruir e redirecionar
            session_destroy();
            header('Location: /login?erro=sessao_expirada');
            exit;
        }
        
        // Atualizar timestamp da última atividade
        $_SESSION['LAST_ACTIVITY'] = time();
        */
    }
}