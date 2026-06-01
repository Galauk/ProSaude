<?php

class Elotech_View_Helper_Odonto extends Zend_View_Helper_Abstract {

	public function odonto() {

		return $this;
	}

	public function procedimento($situacao){
		$tbOd = new Application_Model_Odonto();
		$procedimentos = $tbOd->getSituacao();
		return $procedimentos[$situacao];
	}
}
