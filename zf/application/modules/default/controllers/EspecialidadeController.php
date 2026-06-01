<?php

class EspecialidadeController extends Zend_Controller_Action {

	public function init(){
		$this->_helper->acl->allow(NULL,array("buscar"));
	}

	public function indexAction() {
		// action body
	}

	/**
	 * Retorna as especilidades em JSON
	 * O retorno é usado pelo plugin de busca
	 */
	public function buscarAction(){
		$tbEsp = new Application_Model_Especialidade();
		
		$term = $this->_getParam("term",FALSE);
		$this->view->dados = $tbEsp->buscar($term);
		return $this->render("dados", NULL, TRUE);
	}
        
    public function listaEspecialidadePorProfissionalAction(){
        $tbEsp = new Application_Model_Especialidade();
        $usrCodigo = $this->_request->getPost("usrCodigo",FALSE);
        $uniCodigo = $this->_request->getPost("uniCodigo",FALSE);

        $this->view->dados = $tbEsp->getEspecialidadePorProfissionalUnidade($usrCodigo, $uniCodigo)->toArray();
        return $this->render("dados", NULL, TRUE);
    }

    public function listaEspecialidadePorProfissionalGeralAction(){
        $tbEsp = new Application_Model_Especialidade();
        $usrCodigo = $this->_request->getPost("usrCodigo",FALSE);
        $uniCodigo = $this->_request->getPost("uniCodigo",FALSE);

        $this->view->dados = $tbEsp->getEspecialidadePorProfissionalGeral($usrCodigo)->toArray();
        return $this->render("dados", NULL, TRUE);
    }

    public function carregaEquipesAction(){
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
        $uni_codigo = $this->_request->getPost("uni_codigo", FALSE);
        if ($usr_codigo){
            $this->view->dados = $tbUsr->usuariosEquipes($usr_codigo, $uni_codigo)->toArray();
        }

        return $this->render("dados", NULL, TRUE);
    }

    public function carregaMicroareaAction(){
        $tbMa = new Application_Model_MicroArea();
        $cod_seq_equipe = $this->_request->getPost("co_seq_equipe", NULL);
        $uni_codigo = $this->_request->getPost("uni_codigo", NULL);

        if ($cod_seq_equipe != 'null' or $cod_seq_equipe != 'undefined') {
            $this->view->dados = $tbMa->getMicroAreasAtivas($cod_seq_equipe)->toArray();
        } else {
            if($uni_codigo != 'null') {
                $this->view->dados = $tbMa->getMicroAreasAtivasPorUnidade($uni_codigo)->toArray();
            }
        }
        return $this->render("dados", NULL, TRUE);
    }

    public function listaEspecialidadePorProfissionalUnidadeAction(){
        $tbEsp = new Application_Model_Especialidade();
        $usrCodigo = $this->_request->getPost("usr_codigo",FALSE);
        $uniCodigo = $this->_request->getPost("uniCodigo",FALSE);

        $this->view->dados = $tbEsp->getEspecialidadePorProfissionalUnidade($usrCodigo, $uniCodigo)->toArray();
        return $this->render("dados", NULL, TRUE);
    }


}