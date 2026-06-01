<?php

class Elotech_View_Helper_Moeda extends Zend_View_Helper_Abstract {

    function moeda($valor) {
                
                $temPonto = null;
        
		if(!empty ($valor)){
                    $temPonto = strpos($valor, '.');
                    if(!$temPonto){
                        return $valor*100;
                    } 
		}
		return $valor; 
    }

} 