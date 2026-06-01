<?php

class Elotech_View_Helper_InputCheckbox extends Zend_View_Helper_Abstract {

	function inputCheckbox($nome, $valor, $padrao=FALSE) {
		$out = "";

		if ($valor == $padrao)
			$out .= sprintf("<input name=\"%s\" type=\"checkbox\" value=\"%s\" checked=\"checked\" /> ", $nome, $valor);
		else
			$out .= sprintf("<input name=\"%s\" type=\"checkbox\" value=\"%s\" /> ", $nome, $valor);


		return $out;
	}

}

