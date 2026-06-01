<?php

class Elotech_View_Helper_Radio extends Zend_View_Helper_Abstract {

    /**
     * Verifica se o atendimento ainda pode editado (após concluido)
     * @see /application/configs/config.ini: prontuario.atendimento.tempoParaReabrir
     * @param Zend_Db_Table_Row_Abstract $ate atendimento ($ate->buscar())
     * @return bool 
     */
    function radio($nome, $valor, $opcoes=array(), $atributos=array(), $divisao=" ") {
		$attr = "";
		foreach($atributos as $chave => $value){
			$attr .= " $chave=\"$value\"";
		}
		
		$out = array();
		foreach($opcoes as $chave => $value){
			if($chave == $valor)
				$checked = "checked=\"checked\"";
			else
				$checked = "";
			
			$out []= "<input type=\"radio\" name=\"$nome\" value=\"$chave\"{$attr}{$checked}> $value";
		}
		
		return implode($divisao,$out);
		
    }

} 