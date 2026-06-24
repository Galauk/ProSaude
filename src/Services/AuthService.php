<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;

class AuthService
{
    public function __construct(
        private UsuarioRepository $usuarioRepository
    ) {}

    public function autenticar(string $login,string $senha): bool {

        $usuario = $this->usuarioRepository->buscarPorLogin($login);
 
        if (!$usuario || !$usuario->verificarSenha($senha)) {
            return false;
        }

        $_SESSION['usuario_id']
            = $usuario->getId();

        $_SESSION['usuario_nome']
            = $usuario->getNome();

        $_SESSION['usuario_perfil']
            = $usuario->getPerfil();

        return true;
    }
}