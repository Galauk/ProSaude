<?php

class Elotech_View_Helper_DadosUsu extends Zend_View_Helper_Abstract {
	
	static protected $usu = FALSE;

	function dadosUsu($usu_codigo) {
		if(!self::$usu)
			$this->carregarUsu($usu_codigo);
		
		return self::$usu;
    }
	
	protected function carregarUsu($usu_codigo){
		$tbUsu = new Application_Model_Usuario();
		
		self::$usu = $tbUsu->getDadosToPrint($usu_codigo);
	}

} 