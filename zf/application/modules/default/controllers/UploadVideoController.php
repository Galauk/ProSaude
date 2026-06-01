<?php

class UploadVideoController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Upload de Videos";
	}

	public function indexAction() {
		// action body
	}

	public function salvarAction(){            
           $dados = array("upv_titulo"=>$this->_request->getPost("upv_titulo", NULL),
                           "upv_descricao"=>$this->_request->getPost("upv_descricao", NULL)
           );

            $tbUpv = new Application_Model_UploadVideo();
            $tbUpv->salvar($dados);           
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->addValidator('Extension', false, 'ogv');         
           
            $adapter->setDestination('C:\Desenvolvimento\Elotech\WebSocialSaude\zf\public\videos');          
            if (!$adapter->receive()){
                $messages = $adapter->getMessages();
                echo implode("\n", $messages);
            }

            $this->render("index");
            
            
            
        }
        
}

