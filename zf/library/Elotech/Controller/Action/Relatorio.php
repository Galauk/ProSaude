<?php

class Elotech_Controller_Action_Relatorio extends Zend_Controller_Action {
	
	public function relatorio($where){
		$tbRel = new Application_Model_Relatorio();

        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
		
		$params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
		
		$this->view->relatorio = $tbRel->relatorioGenerico($where);
		$this->_helper->layout->setLayout("relatorio");
		return $this->render("relatorio", NULL, TRUE);
	}
	
}