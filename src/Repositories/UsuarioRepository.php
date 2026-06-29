<?php
namespace App\Repositories;

use PDO;
use App\Models\Usuario;

use App\Enums\PerfilUsuario;
use DateTime;
use Exception;
use PDOException;

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

        if (!$data) {
            return null;
        }

        return $this->fromData($data);
    }

    public function buscarPorLogin(string $login){
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        return $this->fromData($data);
    }

    public function salvar(Usuario $usuario): void
    {
        $stmt = $this->db->prepare("INSERT INTO usuario (nome, login, email, senha, perfil, ativo) VALUES (:nome, :login, :email, :senha, :perfil, :ativo)");
        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'login' => $usuario->getLogin(),
            'senha' => $usuario->getSenhaHash(),
            'perfil' => $usuario->getPerfil(),
            'ativo' => $usuario->isAtivo(),
        ]);
    }

    public function atualizar(Usuario $usuario){
        try{
            $stmt = $this->db->prepare("
                UPDATE usuario
                SET
                    nome = :nome,
                    login = :login,
                    email = :email,
                    senha = :senha,
                    perfil = :perfil,
                    ativo = :ativo
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $usuario->getId(),
                'nome' => $usuario->getNome(),
                'email' => $usuario->getEmail(),
                'login' => $usuario->getLogin(),
                'senha' => $usuario->getSenhaHash(),
                'perfil' => $usuario->getPerfil(),
                'ativo' => $usuario->isAtivo(),
            ]);
        }catch(Exception $e){
            throw $e;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function listar(): array
    {
        $stmt = $this->db->query("SELECT * FROM usuario");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $usuarios = [];
        while ($data = $stmt->fetch()) {
            
            $usuarios[] = $this->fromData($data);
        }
        return $usuarios;
    }

    private function fromData(array $data): Usuario
    {
        $usuario = new Usuario();

        $usuario->setId($data['id']);
        $usuario->setNome($data['nome']);
        $usuario->setLogin($data['login']);

        $usuario->setEmail(
            $data['email'] ?? null
        );

        $usuario->setSenhaHash(
            $data['senha']
        );

        $usuario->setPerfil(
            PerfilUsuario::from(
                $data['perfil']
            )
        );

        $usuario->setAtivo(
            (bool) $data['ativo']
        );

        if (!empty($data['ultimo_login'])) {
            $usuario->setUltimoLogin(
                new DateTime($data['ultimo_login'])
            );
        }

        if (!empty($data['created_at'])) {
            $usuario->setCreatedAt(
                new DateTime($data['created_at'])
            );
        }

        if (!empty($data['updated_at'])) {
            $usuario->setUpdatedAt(
                new DateTime($data['updated_at'])
            );
        }

        return $usuario;
    }
}