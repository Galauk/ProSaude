<?php
namespace App\Repositories;

use PDO;
use App\Models\Usuario;

use App\Enums\PerfilUsuario;

class UsuarioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function buscarPorCodigo(string $codigo): ?Usuario
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE id = :codigo");
        $stmt->execute(['codigo' => $codigo]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Usuario($data['id'], $data['nome'], $data['email']);
        }

        return null;
    }

    public function salvar(Usuario $usuario): void
    {
        $stmt = $this->db->prepare("INSERT INTO usuario (nome, login, email, senha, perfil, ativo) VALUES (:nome, :login, :email, :senha, :perfil, :ativo)");
        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'login' => $usuario->getLogin(),
            'senha' => $usuario->getSenha(),
            'perfil' => $usuario->getPerfil()->value,
            'ativo' => $usuario->isAtivo(),
        ]);
    }

    public function listar(): array
    {
        $stmt = $this->db->query("SELECT * FROM usuario");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $usuarios = [];
        while ($data = $stmt->fetch()) {
            $usuario = new Usuario();
            $usuario->setId($data['id']);
            $usuario->setNome($data['nome']);
            $usuario->setEmail($data['email']);
            $usuario->setLogin($data['login']);
            $usuario->setPerfil(PerfilUsuario::from($data['perfil']));
            $usuario->setAtivo((bool)$data['ativo']);
            $usuarios[] = $usuario;
        }
        return $usuarios;
    }
}