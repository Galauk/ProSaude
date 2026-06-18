<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        $this->render('auth/login');
    }
    public function autenticar()
    {
        $login = $_POST['login'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        // Iniciar sessão uma única vez
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Se já está logado, redirecionar para dashboard
        if (isset($_SESSION['usuario'])) {
            $this->redirect('/prosaude/dashboard');
            exit;
        }
        
        // Validar entrada
        if (empty($login) || empty($senha)) {
            $this->render(
                'auth/login', 
                [
                    'title' => 'ProSaúde',
                    'erro' => 'Login e senha são obrigatórios'
                ], 
                'public'
            );
            return;
        }
        
        // TODO: Implementar validação no banco de dados
        // $usuario = Usuario::where('login', $login)->first();
        // if ($usuario && password_verify($senha, $usuario->senha)) {
        
        // Validar credenciais
        if ($this->validarCredenciais($login, $senha)) {
            // Regenerar ID de sessão para evitar session fixation
            session_regenerate_id(true);
            
            // Armazenar dados do usuário com chave padronizada
            $_SESSION['usuario'] = $login;
            $_SESSION['LAST_ACTIVITY'] = time();
            
            $this->redirect('/prosaude/dashboard');
            exit;
        } else {
            $this->render(
                'auth/login', 
                [
                    'title' => 'ProSaúde',
                    'erro' => 'Login ou senha inválidos'
                ], 
                'public'
            );
            return;
        }
    }
    
    private function validarCredenciais($email, $senha): bool
    {
        // TODO: Implementar validação real no banco de dados
        // Por enquanto, aceita qualquer email não vazio com senha > 3 caracteres
        return !empty($email) && strlen($senha) >= 3;
    }
    
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        
        $this->redirect('/login');
        exit;
    }
}