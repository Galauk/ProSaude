<?php

namespace App\Controllers;
use App\Services\AuthService;

class AuthController extends BaseController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login()
    {
        $this->render('auth/login');
    }
    
    public function autenticar(): void
    {
        $ok = $this->authService->autenticar($_POST['login'],$_POST['senha']);

        if(!$ok){
            $this->redirect('/login');
        }else{
            $this->redirect('/dashboard');
        }
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