<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_FichaRaas extends Elotech_Db_Table_Abstract {

	protected $_name = 'raas';
	protected $_primary = 'raas_id';
	


    public function salvar($dados) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($dados);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($dados);
    }
    public function updateQntAcoes($prontuario,$qnt){
        $quantidade = intval($qnt);
        $qntacoes = $this->getDefaultAdapter()->query(
            "SELECT ras_total_acoes from raas
            WHERE ras_prontuario = '$prontuario'
            "
        )->fetchAll();

        $qntac = intval($qntacoes[ras_total_acoes]);
        //echo "<pre>";print_r($qntac);die();

        $quantidade = $qntac + $quantidade;
        //echo "<pre>";print_r($quantidade);die();

        $sql = $this->getDefaultAdapter()->query(
            "UPDATE raas
             SET ras_total_acoes = $quantidade
             WHERE ras_prontuario = '$prontuario'
            "
        )->fetchAll();
        return $sql;
    }


    //prontuario
    public function updateProntuarioRaas($dados,$tbRaasResultado){
        $sql = $this->getDefaultAdapter()->query(
            "UPDATE raas
             SET ras_prontuario = $dados
             WHERE raas_id = $tbRaasResultado
            "
        )->fetchAll();
        return $sql;
    }

    //adiciona data final e motivo de saida
    public function updateFinalizaRaas($x, $y, $z){
        $dt = date('Ymd');
        $data2 = date('Y-m-d', strtotime($dt));
        $motivo = intval($x);
        $sql = $this->getDefaultAdapter()->query(
            "UPDATE raas
             SET ras_motivosaida = '$motivo' , ras_data_obito_alta = '$y', ras_val_fin = '$data2'
             WHERE ras_prontuario = '$z' 
            "

        )->fetchAll();
        return $sql;
    }


    public function buscaListaRaas($term){
        $tbConf = new Application_Model_Configuracao();
        $recebeTermo = $term;
        if($term){
            $sql = $this->getDefaultAdapter()->query(
                " SELECT raas.ras_val_ini, raas.ras_prontuario, raas.ras_paciente, raas.ras_motivosaida FROM raas
                WHERE raas.ras_paciente ilike '%recebeTermo%'
                "
            )->fetchAll();
        }

        $out = array();

        foreach ($sql as $usu) {
            $out [] = array(
                    "id" => $usu[ras_prontuario],
                    "label" => $usu[ras_paciente],
                    "data" => $usu
            );
        }
        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => $usu
            );
        }
        return $out;

    }

    public function listaFichaRaas($busca=FALSE, $ras_prontuario=FALSE){
        //die("visitas");
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("raas"=>"raas"), array("ras_val_ini","ras_prontuario","ras_paciente","ras_motivosaida"));
                    //->join(array("acoes"=>"acoes_raas"), "raas.id_formulario_raas=acoes.id_formulario_raas", array("id_acao"));
        // Usado somente para busca
        
        $sql->where("raas.ras_paciente ilike '%".$busca."%'");
        //die($sql);
       // $sql->order(array("data_primeiroatendimento DESC"));
        return $this->fetchAll($sql);
    }

    public function pegaCns($salvaprontuario){
        $sql = $this->getDefaultAdapter()->query(
                " SELECT raas.ras_cns_paciente FROM raas
                WHERE raas.ras_prontuario = '$salvaprontuario'
                "
        )->fetchAll();
        return $sql;
    }
    public function pegaIbgeMun($bai){

        $b = intval($bai);
        $sql = $this->getDefaultAdapter()->query(
            "SELECT cidade.cid_codigo_ibge from cidade
            WHERE cidade.cid_codigo = $b
            "
        )->fetchAll();
        return $sql;
    }

    public function excluirFicha($value){
        //echo "<pre>";print_r($value);die();
        $sql = $this->getDefaultAdapter()->query(
            "DELETE from raas
            WHERE ras_prontuario = '$value'
            "
        )->fetchAll();
        $sql2 = $this->getDefaultAdapter()->query(
            "DELETE from raas_acoes
            where ras_prontuario = '$value'
            "
        )->fetchAll();
        return $sql;
    }

    public function pegaFichas($prontuario){
        //echo "<pre>";var_dump($prontuario);die();
        $sql = $this->getDefaultAdapter()->query(
                "SELECT * from raas
                WHERE ras_prontuario = '$prontuario[ras_prontuario]'
                "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function somaCnesCns($value){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT ras_cnes, ras_cns_paciente from raas
            WHERE ras_prontuario = '$value'
            "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function pegaMotivo($value){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT ras_motivosaida from raas
            WHERE ras_prontuario = '$value'
            "
        )->fetchAll();
        return $sql;
    }



}