<?php

class Elotech_View_Helper_PodeReabrirAtendimento extends Zend_View_Helper_Abstract {

    /**
     * Verifica se o atendimento ainda pode editado (após concluido)
     * @param Zend_Db_Table_Row_Abstract $ate atendimento ($ate->buscar())
     * @return bool 
     */
    function podeReabrirAtendimento($ate) {
       // die($ate->ate_datafinal."--".$ate->ate_horafinal);
        if(!$ate->ate_hora){           
                // este atendimento ainda não foi finalizado
                return FALSE;
        }
                
        list($y,$m,$d) = explode("-",$ate->ate_data);
        list($h,$i) = explode(":",$ate->ate_hora);
        $tempoFinal = mktime($h, $i, 0, $m, $d, $y);
        
		$tbConf = new Application_Model_Configuracao();
		$tempoMax = $tbConf->getConfig("PRONTUARIO_ATENDIMENTO_TEMPOPARAREABRIR");		
        $tempoMax *= 60; // segundos
        return (bool) ($tempoFinal > (time()-$tempoMax));        
    }

} 