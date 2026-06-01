<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Log extends Elotech_Db_Table_Abstract {

	protected $_name = 'log'; 
	protected $_primary = 'log_cod';
	protected $_dependentTables = array();


	public function relAcessoPorUsuario($usr_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE) {

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("log" => "log"), array("log_data","usr_codigo"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=log.usr_codigo",array("usr_nome","usr_codigo"))
				
				->group(array("usr_nome","usr.usr_codigo","log_data","log.usr_codigo"))
				->order("usr_nome");


		if ($usr_codigo){
			$where->where("usr.usr_codigo = ?", $usr_codigo);
		}

		if ($data_inicial){
			$where->where("log.log_data::date>= ?", $data_inicial);
		}

		if ($data_final){
			$where->where("log.log_data::date<= ?", $data_final);
		}
        return $this->fetchAll($where);
	}     
}