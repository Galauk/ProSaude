<?php

	function corta($str, $max=10, $preencherCom="0"){
		$lado = ($preencherCom === "0" ? STR_PAD_LEFT : STR_PAD_RIGHT);
		
		if( strlen($str) > $max )
			$str = substr($str, 0, $max);
			
		return str_pad($str, $max, $preencherCom, $lado);
	}

?>