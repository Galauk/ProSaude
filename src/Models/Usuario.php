<?php
namespace App\Models;
class Usuario
{
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $nomeMae;
    public $id_unidade;
    public $dataNasc;
    public $sexo;
    public Endereco $endereco;
    public $ocupacao;

    public $prontuario;
    public $observacao;
    /** @var Documento[] */
    private array $documentos = [];

    public function __construct($id, $nome, $email, $senha) 
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
    }
}