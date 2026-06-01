<?php

class Elotech_View_Helper_Data extends Zend_View_Helper_Abstract {

    function data($data, $para='dd/MM/YYYY', $empty=TRUE) {
		if(empty ($data)){
			if($empty === FALSE)
				return "";
			else
				return $empty;
		}
		
		$date = new Zend_Date($data);
		return $date->toString($para, 'America/Sao_Paulo'); 
    }

} 