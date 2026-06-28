<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Models\Usuario;

class UsuarioService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function listarUsuarios()
    {
        return $this->usuarioRepository->listar();
    }

    public function buscarUsuarioPorCodigo(string $codigo)
    {
        return $this->usuarioRepository->buscarPorCodigo($codigo);
    }

    public function salvar(Usuario $usuario)
    {
        if (empty($usuario->getNome()) || empty($usuario->getEmail())) {
            throw new \Exception('Campos obrigatórios: nome e email');
        }
        $this->usuarioRepository->salvar($usuario);
        return $usuario;
    }
    public function atualizar(Usuario $usuario)
    {
        if (empty($usuario->getNome()) || empty($usuario->getEmail())) {
            throw new \Exception('Campos obrigatórios: nome e email');
        }
        $this->usuarioRepository->atualizar($usuario);
        return $usuario;
    }
}