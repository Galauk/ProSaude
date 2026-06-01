<?php

class Elotech_View_Helper_Imc extends Zend_View_Helper_Abstract {

    function imc($peso, $altura) {
		$altura = (float) $altura;
		$altura *= $altura;
		
		if($peso && $altura)
			return number_format($peso/$altura,1);
		
		return "--";
		
    }

} 