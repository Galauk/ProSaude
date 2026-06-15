<?php
namespace App\Models;

class Paciente
{
    public $id;
    public $nome;
    public $email;
    public $dataNasc;
    public $sexo;
    public $nomeMae;
    public $id_unidade;
    public Endereco $endereco;
    public $ocupacao;

    public $prontuario;
    public $observacao;
    /** @var Documento[] */
    private array $documentos = [];

    public function __construct($id, $nome, $email, $dataNasc, $sexo, $nomeMae, $id_unidade, Endereco $endereco, $ocupacao)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->dataNasc = $dataNasc;
        $this->sexo = $sexo;
        $this->nomeMae = $nomeMae;
        $this->id_unidade = $id_unidade;
        $this->endereco = $endereco;
        $this->ocupacao = $ocupacao;
    }

    public function adicionarDocumento(Documento $documento): void
    {
        $this->documentos[] = $documento;
    }

    public function getDocumentos(): array
    {
        return $this->documentos;
    }
}