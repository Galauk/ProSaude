<?php

class Elotech_View_Helper_DadosSecretaria extends Zend_View_Helper_Abstract {
	
	static protected $sec = FALSE;

	function dadosSecretaria() {
		if(!self::$sec)
			$this->carregarSec ();
		
		return self::$sec;
    }
	
	protected function carregarSec(){
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$obj = new stdClass();
		$obj->cidade = $sec->nome_cidade;
		$obj->endereco = $sec->endereco_secretaria.", ".$sec->numero_end_secretaria." ".$sec->nome_cidade."/".$sec->sec_sigla;
		$obj->cnes = $sec->cnes_secretaria;
		$obj->cnpj = $sec->cnpj_secretaria;
		
		self::$sec = $obj;
	}

} 