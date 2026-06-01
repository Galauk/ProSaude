<?php

class GuicheController extends Zend_Controller_Action {

	public function init() {
		$this->view->title = "Guichê de Atendimento";
        $this->_helper->acl->allow(NULL,array('index'));
	}

	public function indexAction() {
        $this->_helper->layout->setLayout("sem-aba");
        $tbCha = new Application_Model_Chamada();
            $proximo = $tbCha->buscarProximo();
        //    die("asdfasdf");
        $chamados = $tbCha->buscarChamados()->toArray();
        //echo "<pre>".print_r($proximo,1); die();
        $this->view->chamados = $chamados;
        $this->view->proximo = $proximo;
	}
    
    public function retornaVideoAction() {
	    $tbUpv= new Application_Model_UploadVideo();
        $videos = $tbUpv->retornaVideo();
        // $video = array_rand($videos);
        // echo $video[0];die("aca");
        // echo $videos; die();
        //  echo "<pre>".print_r($videos,1); die();
        $this->view->dados = $videos;
        return $this->render("dados", NULL, TRUE);
	}

    public function carregaDadosIniciaisAction(){
        $tbUsr = new Application_Model_Usuarios();
        $this->view->dados = $tbUsr->getUsrAtual();
        return $this->render("dados", NULL, TRUE);
    }
    
    public function getLastIndexAction() {
        $_SESSION[modulo] = "WebSocialSaude/"; $_SESSION[root] = $_SERVER[DOCUMENT_ROOT] . "/"; $_SESSION[linkroot] = "http://" . $_SERVER[HTTP_HOST] . "/"; $_SESSION[comum] = "WebSocialComum/"; $_SESSION[modulo] = "WebSocialSaude/"; require_once $_SESSION[root].$_SESSION[modulo]."sessao_controller.php";
        $sessao = new TempoSessao();
        $sessao->primeiraPagina();

        $uni_codigo = $this->_getParam("uni_codigo");

        // Verifica se houve chamada
        $tbCha = new Application_Model_Chamada();
        $index = $tbCha->getIndex($uni_codigo)->toArray();
        $this->view->dados = $index;
        return $this->render("dados", NULL, TRUE);
    }
    
    public function buscarChamadasAction() {
	    $tbCha = new Application_Model_Chamada();
        $uni_codigo = $this->_getParam("uni_codigo");
        $chamadas = $tbCha->buscarChamadas($uni_codigo)->toArray();
        //echo "<pre>".print_r($chamadas,1); die();
        $this->view->dados = $chamadas;
        return $this->render("dados", NULL, TRUE);
    }
    
    public function buscarProximoAction() {
	    $tbCha = new Application_Model_Chamada();
        $proximo = $tbCha->buscarProximo()->toArray();
        //echo "<pre>".print_r($chamadas,1); die();
        $this->view->dados = $proximo;
        return $this->render("dados", NULL, TRUE);
	}
        
    public function buscarPacientesAction() {
	    $tbCha = new Application_Model_Chamada();            
        $vai = $tbCha->buscarChamados()->toArray();
        // echo "<pre>".print_r($vai,1); die();
        $this->view->dados = $vai;
        return $this->render("dados", NULL, TRUE);
	}
        
    public function alteraStatusAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	    $age_codigo = $this->_getParam("age_codigo");
        $tbCha = new Application_Model_Chamada();
        $tbCha->encerrarChamada($age_codigo);
        $this->render("index");
	}
  
    public function chamarAction(){
        // $age_codigo = $this->_getParam("age_codigo");
        // $tbCha = new Application_Model_Chamada();
        // $tbCha->encerrarChamada($age_codigo,"C");

        // $bC = $tbCha->buscarChamados();
        // $bP= $tbCha->buscarProximo();
        
        // $dados[] = $bC->toArray();
        // $dados[] = $bP->toArray();
        
        // $this->view->dados = $dados;
        // return $this->render("dados", NULL, TRUE); 
        $age_codigo = $this->_getParam("age_codigo");
        
        $tbUsr = new Application_Model_Usuarios();

        $tbCha = new Application_Model_Chamada();
        $dataCha = array(
            "age_codigo" => $age_codigo,
            "usr_codigo" => $tbUsr->getUsrAtual()->usr_codigo,
            "cha_status" => "C"
        );

        // print_r($dataCha); die;
        $idCha = $tbCha->salvar($dataCha);

        // echo $age_codigo;
        $tbCha = new Application_Model_Chamada();
        $ret = $tbCha->encerrarChamada($age_codigo, "C");

        $this->view->dados = $ret;
        return $this->render("dados", NULL, TRUE);
    }

    public function lerAction() {
        $usu_nome = $this->_getParam("usu_nome","maria");
	    $tbCha = new Application_Model_Chamada();
        $chamadas = $tbCha->ler($usu_nome);
        //echo "<pre>".print_r($chamadas,1); die();
        $this->view->dados = $chamadas;
        return $this->render("dados", NULL, TRUE);        
	}
}