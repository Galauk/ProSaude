<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EditarRaas extends Elotech_Db_Table_Abstract {

	protected $_name = 'raas';
	protected $_primary = 'raas_id';
	


    public function salvar($dados) {

        try {
            return parent::salvar($dados);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o atendimento: ".$exc->getMessage());
        }
    }

/*    public function message()
    {
        $this->_redirect("atendimento/atendimento-simplificado/form-ficha-raas?alert=sucesseditar");
    }*/

        // echo "<pre>"; print_r($sql);die();
    public function recuperaFichaRaas($dados){
        // $converte = intval($dados);
        // var_dump($converte);die();
        $sql = $this->getDefaultAdapter()->query(
            "SELECT * FROM  raas
            WHERE raas.ras_prontuario = '$dados'
            "
        )->fetchAll();

        // echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function recuperaUnidadeEsf($dados)
    {
        $sql = $this->getDefaultAdapter()->query(
            "SELECT uni_desc from unidade
            where uni_cnes = $dados

            "
        )->fetchAll();

        return $sql;
    }


    public function recuperaCids($dados)
    {
        $sql = $this->getDefaultAdapter()->query(
            "SELECT ras_cidp, ras_cids1, ras_cids2, ras_cids3, ras_cidca from raas
            where ras_prontuario = '$dados'
            "
        )->fetchAll();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }


}