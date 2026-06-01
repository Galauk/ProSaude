<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Acoes extends Elotech_Db_Table_Abstract {

	protected $_name = 'raas_acoes';
	protected $_primary = 'ras_acoes_id';
	


    public function salvar($acoesdados) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($acoesdados);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($acoesdados);
    }




    public function pegaUsrCns($usr){
		$sql = $this->getDefaultAdapter()->query(
                " SELECT usuarios.cnes_cod_cns FROM usuarios
                WHERE usuarios.usr_codigo = '$usr'
                "
        )->fetchAll();

        return $sql;
    }

    public function pegaUsrCbo($salvaespecialidade){
    	$sql = $this->getDefaultAdapter()->query(
                " SELECT especialidade.cod_cbo FROM especialidade
                WHERE especialidade.esp_codigo = '$salvaespecialidade'
                "
        )->fetchAll();

        return $sql;
    }

    public function pegaUniCnes($unidad){
    	$sql = $this->getDefaultAdapter()->query(
                " SELECT unidade.uni_cnes FROM unidade
                WHERE unidade.uni_codigo = '$unidad'
                "
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }


    public function listaAcoesRaas($ras_prontuario){
        $pront = intval($ras_prontuario);

         //echo "<pre>"; var_dump($pront); die();

        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("raas_acoes"), array("ras_acoes_id","ras_dataexe","ras_qnt","ras_prontuario","ras_obs"))
                    ->joinLeft(array("procedimento"), "raas_acoes.ras_acao=procedimento.proc_codigo_sus", array("proc_nome"));
        // Usado somente para busca
        
        $sql->where("raas_acoes.ras_prontuario = '$pront'");
        //die($sql);
       // $sql->order(array("data_primeiroatendimento DESC"));
        return $this->fetchAll($sql);
    }

    public function recuperaAcao($idraas){
        $value = intval($idraas);
        $sql = $this->getDefaultAdapter()->query(
            "SELECT * FROM  raas_acoes
            WHERE raas_acoes.ras_acoes_id = $value
            "
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }
    public function deletaAcao($idraas){
        $value = intval($idraas);
        $sql = $this->getDefaultAdapter()->query(
            "DELETE FROM  raas_acoes
            WHERE raas_acoes.ras_acoes_id = $value
            "
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function pegaAcao($ras_acao)    {
        $sql = $this->getDefaultAdapter()->query(
            "SELECT procedimento.proc_nome, procedimento.proc_codigo, procedimento.proc_codigo_sus FROM procedimento
            WHERE procedimento.proc_codigo_sus = '$ras_acao'
            "
        )->fetchAll();

        return $sql;
    }

    public function contaProntuariosAcoes($dataacao){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT distinct ras_prontuario from raas_acoes where to_char(ras_dataexe , 'YYYY-MM') = '$dataacao'
            "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function contaAcoes($prontuario, $dataacao){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT count(*) from raas_acoes
            WHERE ras_prontuario = '$prontuario' and to_char(ras_dataexe , 'YYYY-MM') = '$dataacao'
            "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function pegaTodasAcoes($prontuario,$dataacao){
        
        //echo "<pre>";var_dump($value);die();
        $sql = $this->getDefaultAdapter()->query(
            "SELECT * from raas_acoes
            WHERE ras_prontuario = '$prontuario' and to_char(ras_dataexe , 'YYYY-MM') = '$dataacao'
            "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        //die("teste");
        return $sql;
    }

    public function somaProcQnt($datames){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT ras_qnt, ras_acao from raas_acoes
            WHERE to_char(ras_dataexe , 'YYYY-MM') = '$datames'
            "
        )->fetchAll();
        //echo "<pre>";print_r($sql);die();
        return $sql;
    }


}