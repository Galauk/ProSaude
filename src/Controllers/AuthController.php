<?php

namespace App\Controllers;

class AuthController 
{
    public function login()
    {
        require __DIR__ . '../Views/auth/login.php';
    }
}