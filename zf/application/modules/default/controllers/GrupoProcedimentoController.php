<?php

class GrupoProcedimentoController extends Zend_Controller_Action {

	public function init() {
            $this->view->title = "Grupo de Procedimentos";
	}

	public function indexAction() {
		$gp_codigo = $this->_request->getPost("gp_codigo", FALSE);
		//die(var_dump($gp_codigo));
		$tbGP = new Application_Model_GrupoProcedimento();
		$this->view->g_select = $tbGP->getGrupo();

		if($gp_codigo){
			$grupos = $tbGP->getGrupo($gp_codigo);
		} else {
			$grupos = $tbGP->getGrupo();
		}

		foreach($grupos as $grupo){
			$procs[$grupo->gp_codigo] = $tbGP->getGrupoProcedimento($grupo->gp_codigo);
		}

		$this->view->grupos = $grupos;
		$this->view->procs = $procs;
	}

	public function formAction() {
		$tbGP = new Application_Model_GrupoProcedimento();
		$id = $this->_getParam("id", FALSE);
		//die(var_dump($id));
		if($id != FALSE){
			//die(var_dump($tbGP->getGrupo($id)));
			$this->view->dados = $tbGP->getGrupo($id);
		}
	}

	public function salvarAction () {
		$post = $_POST;
		$dados = array(
            "gp_descricao" => $this->_request->getPost("gp_descricao", FALSE),
            "gp_obs" => $this->_request->getPost("gp_obs", FALSE)
        );
        if($this->_request->getPost("gp_codigo", FALSE) != FALSE){
            $dados["gp_codigo"] = $this->_request->getPost("gp_codigo", FALSE);
        }
        $tbGP = new Application_Model_GrupoProcedimento();
        try {
            $gp_codigo = $tbGP->salvar($dados);
        } catch (Exception $exc) {
            die("Erro".$exc->getMessage());
            return $exc->getMessage();
        }

        foreach ($post["procedimento"] as $val) {
            $dados = "";
            $dados = array(
                "gp_codigo" => $gp_codigo,	
                "proc_codigo" => $val
            );
            if($this->_request->getPost("co_gp_codigo", FALSE) != FALSE){
                $dado = $this->_request->getPost("co_gp_codigo", FALSE);
                $dados["co_gp_codigo"] = $dado[$val];
            };
            $tbRGP = new Application_Model_RlGrupoProcedimento();
            try{
                $tbRGP->salvar($dados);
            } catch (Exception $exc) {
                die($exc->getMessage());
                return $exc->getMessage();
            }
        }
        $this->_redirect("grupo-procedimento/index");
	}

	public function deletarAction() {
		$tbGP = new Application_Model_GrupoProcedimento();
		$tbRGP = new Application_Model_RlGrupoProcedimento();
		$id = $this->_getParam("id", FALSE);

		$procs = $tbGP->getGrupoProcedimento($id);
		foreach ($procs as $key => $proc) {
			$tbRGP->excluir($proc->co_gp_codigo);
		}	
		$tbGP->excluir($id);
		$this->_redirect("grupo-procedimento/index");
	}

	public function procedimentosGrupoAjaxAction() {
        $tbGP = new Application_Model_GrupoProcedimento();
        $gp_codigo = $this->_request->getPost("gp_codigo",FALSE);
        $this->view->dados = $tbGP->getGrupoProcedimento($gp_codigo)->toArray();
        return $this->render("dados", NULL, TRUE);
    }
	
	public function buscarAction(){

        $this->view->title = "Grupo de Procedimentos";
        $gp_codigo = $this->_request->getPost("gp_codigo");
        $tbGP = new Application_Model_GrupoProcedimento();
        $this->view->g_select = $tbGP->getGrupo();
        $grupos = $tbGP->getGrupo($gp_codigo);
        foreach($grupos as $grupo){
			$procs[$grupo->gp_codigo] = $tbGP->getGrupoProcedimento($grupo->gp_codigo);
		}
		$this->view->grupos = $grupos;
		$this->view->procs = $procs;
        return $this->render("index");
    }

}