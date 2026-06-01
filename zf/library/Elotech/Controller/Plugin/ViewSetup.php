<?php

class Elotech_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract {

	/**
	 * @var Zend_View
	 */
	protected $_view;
	
	protected $modulo;
	protected $controller;
	protected $action;

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->init();

		$this->_view = $viewRenderer->view;

		$this->modulo = $request->getModuleName();
        $this->controller = $request->getControllerName();
		$this->action = $request->getActionName();

		$this->adicionaMeta();
		$this->adicionaIcon();
		$this->adicionaJS();
		$this->adicionaCSS();
		$this->selecionaLayout();
	}
	
	private function selecionaLayout(){
		// não há regra padrão para escolha do layout
	}
	
	private function adicionaMeta(){	
		$view = $this->_view;		
		
		$view->doctype('HTML5');
        $view->headMeta()->setName('Content-Type','text/html; charset=UTF-8');
        $view->headMeta()->setHttpEquiv('Content-Type', 'text/html; charset=UTF-8');  
        header('Content-Type: text/html; charset=UTF-8');
	}
	
	private function adicionaIcon(){
		$this->_view->headLink(array(
			'rel' => 'icon',
			'href' => $this->_view->baseUrl().'/public/images/icons/mini_logo_elotech.png'), 'PREPEND');	
	}
	
	private function adicionaJS(){		
		$view = $this->_view;		
		
		// Adiciona o jquery em todas as páginas
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery-1.6.2.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery-ui-1.8.16.custom.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/geral.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.maskedinput-1.3.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.validate.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.form.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.shortcuts.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/easyTooltip.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/plupload.full.js');		
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.plupload.queue.js');		
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.metadata.js');
                $view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.cookie.js');
                $view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.elevateZoom-2.5.5.min.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.printElement.js');
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.fullscreen.min.js');
		
		// Chamar em todas as páginas?
		// Prontuario_PreConsultaController::indexAction()
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.price_format.1.6.min.js');
		
		// Prontuario_PreConsultaController::indexAction()
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.tinymce.js');
		
		// Prontuario_ReceitaMedicaController::indexAction()
		$view->headScript()->appendFile($view->baseUrl() . '/public/js/jquery.buscar.js');
		
		
		// Adiciona automaticamente um arquivo JS do modulo/controller em uso
        if (file_exists(APPLICATION_PATH . "/../public/js/{$this->modulo}.js"))
            $view->headScript()->appendFile($view->baseUrl("/public/js/{$this->modulo}.js"));

        if (file_exists(APPLICATION_PATH . "/../public/js/{$this->modulo}/{$this->controller}.js"))
            $view->headScript()->appendFile($view->baseUrl("/public/js/{$this->modulo}/{$this->controller}.js"));

        if (file_exists(APPLICATION_PATH . "/../public/js/{$this->modulo}/{$this->controller}/{$this->action}.js"))
            $view->headScript()->appendFile($view->baseUrl("/public/js/{$this->modulo}/{$this->controller}/{$this->action}.js"));
	}
	
	private function adicionaCSS(){		
		$view = $this->_view;
		
		$view->headLink()->appendStylesheet($view->baseUrl().'/public/css/redmond/jquery-ui-1.8.16.custom.css');		
		$view->headLink()->appendStylesheet($view->baseUrl().'/public/css/geral.css','all');
		
        // Adiciona automaticamente um arquivo CSS do modulo/controller em uso
        if (file_exists(APPLICATION_PATH . "/../public/css/{$this->modulo}.css"))
            $view->headLink()->appendStylesheet($view->baseUrl("/public/css/{$this->modulo}.css"),"all");
			
        if (file_exists(APPLICATION_PATH . "/../public/css/{$this->modulo}/{$this->controller}.css"))
            $view->headLink()->appendStylesheet($view->baseUrl("/public/css/{$this->modulo}/{$this->controller}.css"),"all");
			
        if (file_exists(APPLICATION_PATH . "/../public/css/{$this->modulo}/{$this->controller}/{$this->action}.css"))
            $view->headLink()->appendStylesheet($view->baseUrl("/public/css/{$this->modulo}/{$this->controller}/{$this->action}.css"),"all");
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!$request->isDispatched()) {
			return;
		}
		$view = $this->_view;

		if (count($view->headTitle()->getValue()) == 0) {			
			$tbConf = new Application_Model_Configuracao();
			
			$titulo = 'Software de Gestão Pública || ProSaúde ';
			$titulo .= $tbConf->getConfig("VERSAO_SAUDE");
			$versao = $tbConf->getIniConfig("versao");
			if($versao){
				$titulo .= ".".$versao->biuld;
			}
			
			if(APPLICATION_ENV == "development"){
				$titulo .= " || Versão de Desenvolvimento";
			}
			
			
			$view->headTitle($view->title);
			$view->headTitle()->setSeparator(' | ');
			$view->headTitle($titulo);
		}
	}

}

