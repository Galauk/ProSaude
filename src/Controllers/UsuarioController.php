<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use App\Models\Usuario;

use App\Enums\PerfilUsuario;

class UsuarioController extends BaseController
{
    public function __construct(private UsuarioService $service)
    {
    }

    public function criar()
    {
        $this->render(
            'usuario/criar',
            [
                'title' => 'Criar Usuário',
                'perfilUsuario' => PerfilUsuario::cases()
            ],
            'app'
        );
    }

    public function listar()
    {
        $usuarios = $this->service->listarUsuarios();
        $this->render(
            'usuario/listar',
            [
                'title' => 'Lista de Usuários',
                'usuarios' => $usuarios
            ],
            'app'
        );
    }

    public function visualizar(string $codigo){
        $usuario = $this->service->buscarUsuarioPorCodigo($codigo);
        $this->render(
            'usuario/visualizar',
            [
                'title' => 'Visualizar Usuário',
                'usuario' => $usuario
            ],
            'app'
        );
    }

    public function salvar()
    {
        $usuario = new Usuario();

        $usuario->setNome($_POST['nome'] ?? '');
        $usuario->setEmail($_POST['email'] ?? '');
        $usuario->setLogin($_POST['login'] ?? '');
        $usuario->setSenha($_POST['senha'] ?? '');
        $usuario->setPerfil(
            PerfilUsuario::from($_POST['perfil'])
        );

        try {
            $usuario = $this->service->salvar($usuario);
            header('Location: /prosaude/usuarios?sucesso=usuario_criado');
        } catch (\Exception $e) {
            header('Location: /prosaude/usuarios?erro=' . urlencode($e->getMessage()));
        }

        exit;
    }

}