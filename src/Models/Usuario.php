<?php
namespace App\Models;

use DateTime;
use App\Enums\PerfilUsuario;

class Usuario
{
    private ?string $id = null;

    private string $nome = '';

    private string $login = '';

    private ?string $email = null;

    private string $senhaHash = '';

    private PerfilUsuario $perfil = PerfilUsuario::USUARIO;

    private bool $ativo = true;

    private ?DateTime $ultimoLogin = null;

    private ?DateTime $createdAt = null;

    private ?DateTime $updatedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getPerfil(): string
    {
        return $this->perfil->value;
    }
    public function setPerfil(PerfilUsuario $perfil): void
    {
        $this->perfil = $perfil;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }
    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }

    public function definirSenha(string $senha): void
    {
        $this->senhaHash = password_hash(
            $senha,
            PASSWORD_BCRYPT
        );
    }    

    public function setSenhaHash(string $hash): void
    {
        $this->senhaHash = $hash;
    }

    public function getSenhaHash(): string
    {
        return $this->senhaHash;
    }

    public function verificarSenha(string $senha): bool
    {
        return password_verify(
            $senha,
            $this->senhaHash
        );
    }

    public function setUltimoLogin(DateTime $ultimoLogin){
        $this->ultimoLogin = $ultimoLogin;
    }

    public function setCreatedAt(DateTime $createdAt){
        $this->createdAt = $createdAt;
    }
    
    public function setUpdatedAt(DateTime $updatedAt){
        $this->updatedAt = $updatedAt;
    }

}