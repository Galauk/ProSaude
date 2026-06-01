<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RlGrupoProcedimento extends Elotech_Db_Table_Abstract {

	protected $_name = 'rl_grupo_procedimento';
	protected $_primary = 'co_gp_codigo';
	protected $_sequence = 'co_gp_codigo_seq';

	public function salvar(array $data) {
		try {
            $co_gp_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o agendamento: ".$exc->getMessage());
        }
        return $co_gp_codigo;
    }

    public function excluir($co_gp_codigo=FALSE) {
        $item = $this->fetchRow("co_gp_codigo=$co_gp_codigo");
        if ($item) {
                $item->delete();
        }
    }

}