<?php

class ProgramasFederais_GrupoAtividadeColetivaController extends Zend_Controller_Action {
	public function init() {
		$this->view->title = "Grupo Atividade Coletiva";
		$this->_helper->acl->allow(NULL);
	}

	public function indexAction() {
		
		$tbGrupoAtiv = new Application_Model_GrupoAtividadeColetiva();
		$this->view->dados = $tbGrupoAtiv->getGrupos();
	}

	public function buscarAction(){
		$descricao = $_POST['busca'];
		$tbGrupoAtiv = new Application_Model_GrupoAtividadeColetiva();
		$this->view->dados = $tbGrupoAtiv->getGruposPorNome($descricao);
		return $this->render("index");
	}

	public function listarGruposAtivosAction(){
		$tbGrupoAtiv = new Application_Model_GrupoAtividadeColetiva();
		$this->view->dados = $tbGrupoAtiv->getGrupos(true)->toArray();
		return $this->render("dados", NULL, TRUE);
	}

	public function listarParticipantesPorGrupoAction(){
		$gac_codigo = $this->_request->getParam("gac_codigo");
		$tbPart = new Application_Model_GrupoAtividadeColetiva();
		$this->view->dados = $tbPart->getParticipantesPorGrupo($gac_codigo)->toArray();
		return $this->render("dados", NULL, TRUE);
	}

	public function formAction(){
		$codGrupo = $this->_request->getParam("id");
		if($codGrupo){
			$tbGac = new Application_Model_GrupoAtividadeColetiva();
			$this->view->data = $tbGac->getGrupo($codGrupo);
		}
		$this->render("form");
	}

	public function salvarAction(){
		$gac_descricao = $this->_request->getPost("gac_descricao", FALSE);
		if(empty($gac_descricao)){
			echo "<script> 
					alert('Campo descrição é obrigatório');
					sleep(2000);
					  location.reload(true);
				  </script>";
			$this->render("form");
			return;
		}

		$dados = array(
			"gac_codigo" => $this->_request->getPost("gac_codigo", NULL),
			"gac_descricao" => strtoupper($gac_descricao),
			"gac_status" => ($this->_request->getPost("gac_status", NULL) == "on" ? 1 : 0)
		);
		$tbGac = new Application_Model_GrupoAtividadeColetiva();
		$gac_codigo = $tbGac->salvar($dados);

		/* Participantes */
		$participantes = $this->_request->getPost("usus_part", NULL);
		if(count($participantes) > 0){
			foreach ($participantes as $key => $part){
				$peso = ($participantes[$key]['gap_peso'] == "" ?  0.000 : $participantes[$key]['gap_peso']);
				$peso = (strpos($peso, ",") != false ?  str_replace(",",".",$participantes[$key]['gap_peso']) : $peso);
				$altura = ($participantes[$key]['gap_altura'] == "" ? 0 : $participantes[$key]['gap_altura']);
				$participantes[$key]['gac_codigo'] = $gac_codigo;
				$participantes[$key]['gap_peso'] = $peso;
				$participantes[$key]['gap_altura'] = $altura;
			}
			$tbGap = new Application_Model_GrupoAtividadeParticipante();
			$tbGap->salvar($participantes);
		}
		$this->_redirect("programasfederais/grupo-atividade-coletiva");
	}

	public function desativarAction(){
		$gac_codigo = $this->_getParam("id", NULL);
		$tbGac = new Application_Model_GrupoAtividadeColetiva();
		$tbGac->desativar($gac_codigo);
	}

	public function ativarAction(){
		$gac_codigo = $this->_getParam("id", NULL);
		$tbGac = new Application_Model_GrupoAtividadeColetiva();
		$tbGac->ativar($gac_codigo);
		$this->_redirect("programasfederais/grupo-atividade-coletiva");
	}
}
