<?php

/**
 * Classe para gerar os relatórios
 * Obs.: Não é uma extenção Zend_db_Table...
 */
class Application_Model_Relatorio {
	public function relatorioGenerico($where, $grupo=FALSE,$total=FALSE){
		/* @var $db Zend_Db_Table_Abstract */
		$db = Zend_Db_Table::getDefaultAdapter(); 
		
		/* @var $linhas Zend_Db_Table_Rowset_Abstract */
		$linhas = $db->fetchAll($where);
		$table = array();
		$last = "";
		foreach($linhas as $tr){			
			if($grupo){
				if($tr[$grupo] != $last){
					$last = $tr[$grupo];
					$table []= $last;
				}
				unset($tr[$grupo]);
			}
			$table []= $tr;
		}
		//$table.= "as";
		return $table;
	}
        
}
