<?php

class Elotech_View_Helper_UsrLogado extends Zend_View_Helper_Abstract {

    function usrLogado($usr_codigo) {
		if(!empty ($usr_codigo)){
			$tbUsr = new Application_Model_Usuarios();
			return $tbUsr->estaLogado($usr_codigo);				
		}
		
    }

} 