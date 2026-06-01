<?php

class TbComposicaoFamiliarController extends Zend_Controller_Action {
	
	public function init(){
		$this->_helper->acl->allow(NULL);
	}

	public function verificaSeUsuarioJaEResponsavelOuMembroDeOutraFamiliaAction(){
		$this->_helper->layout->disableLayout();
		$codigoDoPaciente = $this->_getParam("codigoDoUsuario", null);
		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();
		
		$usuarioResponsavel = $tbComposicaoFamiliar->verificaSeUsuarioJaEResponsavel($codigoDoPaciente);
		$usuarioMembro = $tbComposicaoFamiliar->verificaSeUsuarioJaEstaCadastradoEmOutraFamilia($codigoDoPaciente);

		if ($usuarioResponsavel == false  && $usuarioMembro == false) {
			echo json_encode(false);
		}
		exit();
		return $this->render("dados", NULL, TRUE);
	}

	// public function buscarProntuarioFamiliarAction(){
	// 	$prontuarioFamiliar = $this->_getParam("term", null);
	// 	$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

 //        $this->view->dados = $tbComposicaoFamiliar->buscar($prontuarioFamiliar);
 //        return $this->render("dados", NULL, TRUE);
 //    }

    public function resumoGeralFamiliarAction(){
		$prontuarioFamiliar = $this->_getParam("prontuarioFamiliar", null);

		$tbComposicaoFamiliar = new Application_Model_TbComposicaoFamiliar();

		$recebeBusca = $tbComposicaoFamiliar->buscar($prontuarioFamiliar);
		
		echo json_encode($recebeBusca);
		exit();
    }

}