<?php

class Elotech_View_Helper_GetConfig extends Zend_View_Helper_Abstract {

    /**
     * Verifica uma configuração no banco
     * @param $chave string
     * @return bool 
     */
    function getConfig($chave) {
		$tbConf = new Application_Model_Configuracao();
		return $tbConf->getConfig($chave);		  
    }

} 