<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EstratificacaoRisco extends Elotech_Db_Table_Abstract {

	protected $_name = 'estratificacao_risco';
	protected $_primary = 'er_codigo';
	protected $_sequence = 'seq_er_codigo';


	public function getValoresPorGrupo($grupo){
		$sql = $this->select(FALSE)
                        ->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("er"=>"estratificacao_risco"),array("er_codigo","er_desc","er_cor"))
                        ->where("er.er_grupo=?",$grupo);
            return $this->fetchAll($sql);
	}
}