<?php

namespace App\Controllers;

use App\Services\UsuarioService;
use App\Core\View;

class UsuarioController
{
    public function __construct(private UsuarioService $service)
    {
    }

    public function listar()
    {
        $usuarios = $this->service->listarUsuarios();
        View::render(
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
        View::render(
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
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($id) || empty($nome) || empty($email)) {
            header('Location: /prosaude/usuarios?erro=campos_obrigatorios');
            exit;
        }

        try {
            $usuario = $this->service->criarUsuario($id, $nome, $email);
            header('Location: /prosaude/usuarios?sucesso=usuario_criado');
        } catch (\Exception $e) {
            header('Location: /prosaude/usuarios?erro=' . urlencode($e->getMessage()));
        }

        exit;
    }

}