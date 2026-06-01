<?php

class Elotech_View_Helper_ArrayToRadio extends Zend_View_Helper_Abstract {

    function arrayToRadio($nome,$arr,$padrao=FALSE) {
		$out = "";
		foreach($arr as $valor => $texto){
			if($valor == $padrao)
				$out .= sprintf("<input name=\"%s\" type=\"radio\" value=\"%s\" checked=\"checked\" />%s ", $nome, $valor, $texto);
			else
				$out .= sprintf("<input name=\"%s\" type=\"radio\" value=\"%s\" />%s ", $nome, $valor, $texto);
		}
		
		return $out;
    }

} 