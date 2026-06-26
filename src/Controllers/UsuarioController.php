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

    public function listar($msg = null,$alert = 0)
    {
        $usuarios = $this->service->listarUsuarios();
        $data = ['title' => 'Lista de Usuários','usuarios' => $usuarios];
        if($msg != null && $alert == 0){ $data['msg'] = $msg;}
        if($msg != null && $alert == 1){ $data['alert'] = $msg;}
        $this->render('usuario/listar',$data,'app');
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
        $usuario->definirSenha($_POST['senha'] ?? '');
        $usuario->setPerfil(
            PerfilUsuario::from($_POST['perfil'])
        );

        try {
            $usuario = $this->service->salvar($usuario);
            $this->listar("Usuario cadastrado com sucesso!");
        } catch (\Exception $e) {
            $this->listar("Falha ao cadastrar o usuario!",1);
        }

        exit;
    }

}