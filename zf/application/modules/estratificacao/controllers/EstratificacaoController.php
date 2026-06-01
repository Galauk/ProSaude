<?php
class Estratificacao_EstratificacaoController extends Zend_Controller_Action {

	public function init() {

		$this->_helper->acl->allow(NULL);
		$this->tipoGrupo = new Application_Model_EstratificacaoGrupos();
		$this->tipoPerguntas = new Application_Model_EstratificacaoPerguntas();
		$this->tipoLista = new Application_Model_EstratificacaoLista();

	}

	public function indexAction() {
		$this->view->title = "Dados Estratificação";

		$recebe = $this->_getParam("param",FALSE);
		$this->render('index');
		
	}

	public function grupoAction(){
		$this->render('add-grupo');
	}

	public function indexGrupoAction(){
        $this->_helper->layout->setLayout("simples");
		
		$this->render('index-grupo');


	}
	
	public function apresentarListaGrupoAction(){
		$recebeListar = $this->tipoGrupo->listarDadosBasicosDosGrupos();

		$this->view->recebeListar = $recebeListar;

		$this->render('apresentar-lista-grupo');

	}

	public function desativarGrupoAction(){
		$recebeCodigoGrupo = intval($this->_getParam("recebeCodigoDoGrupo",FALSE));
		
		$resultado = $this->tipoGrupo->desativarGrupo($recebeCodigoGrupo);

		echo json_encode($perguntaAtualizada);

		exit();

		
	}

	public function ativarGrupoAction(){
		$recebeCodigoGrupo = intval($this->_getParam("recebeCodigoDoGrupo",FALSE));
		
		$resultado = $this->tipoGrupo->ativarGrupo($recebeCodigoGrupo);

		echo json_encode($perguntaAtualizada);

		exit();

		
	}
	
	public function editarGrupoAction(){
		$this->view->title = "Editar Grupo";
		
		$recebeCodigoGrupo = intval($this->_getParam("param",FALSE));

		$recebeGrupoPerguntas = $this->tipoGrupo->recuperaGrupoPerguntas($recebeCodigoGrupo);

		$this->view->recebeGrupoPerguntas = $recebeGrupoPerguntas;

		$this->render('editar-grupo');

		
	}

	public function addGrupoAction(){
		$tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
		$usrcodigo = $usr->usr_codigo;
		$tbEst = new Application_Model_EstratificacaoGrupos();
		
		$dados = array(
			"est_gruponome" => $this->_getParam("grupo",FALSE),
			"est_usr" => $usrcodigo,
		);
		$x = $tbEst->salvar($dados);
		
		$this->render('index');
	}

	public function perguntaAction(){
		$this->view->title = "Adiconar Perguntas";

		$tbGrupo = new Application_Model_EstratificacaoGrupos();
		$x = $this->view->grupos = $tbGrupo->listaGrupos();
		$this->render('add-pergunta');
	}
	
	public function addPerguntaAction(){
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		$usrcodigo = $usr->usr_codigo;
		$tbPerg = new Application_Model_EstratificacaoPerguntas();
		$pergunta = $this->_getParam("pergunta",FALSE);
		
 		for($i=0 ;$i<count($pergunta[pergunta]);$i++) {

			$dados = array(
				"est_pergunta" => ($pergunta[pergunta][$i] == '' ? "Pergunta Não Informada" : $pergunta[pergunta][$i]),
				"est_pergvalue" => ($pergunta[valor][$i] == '' ? 2 : $pergunta[valor][$i]),
				"est_pergusr" => $usrcodigo,
				"est_idgrupo" => $this->_getParam("gruposelect",FALSE)
			);

			$tbPerg->salvar($dados);
		}
		$this->render('index');
	}

	public function atualizaNomeGrupoAction(){

		$recebeCodigo = intval($this->_request->getPost("recebeCodigoDoGrupo",FALSE));
		$recebeNomeGrupo = $this->_request->getPost("recebeNomeGrupo",FALSE);

		$perguntaAtualizada = $this->tipoGrupo->atualizaNomeGrupo($recebeCodigo, $recebeNomeGrupo);

		echo json_encode($perguntaAtualizada);

		exit();

	}

	public function atualizaDadosPerguntaAction(){

		$recebeCodigo = intval($this->_request->getPost("recebeCodigo",FALSE));
		$recebeTituloPergunta = ($this->_request->getPost("recebeTituloPergunta",FALSE) == '' ? "Pergunta Não Informada" : $this->_request->getPost("recebeTituloPergunta",FALSE));
		$recebeValorPergunta = (intval($this->_request->getPost("recebeValorPergunta",FALSE)) == '' ? 2 : intval($this->_request->getPost("recebeValorPergunta",FALSE)));
		
		$perguntaAtualizada = $this->tipoPerguntas->atualizaPerguntas($recebeCodigo, $recebeTituloPergunta, $recebeValorPergunta);
		
		echo json_encode($perguntaAtualizada);
		exit();
	}

	public function excluirPerguntaAction(){
		
		$recebeCodigo= intval($this->_request->getPost("recebeCodigo",FALSE));
		
		$resultado = $this->tipoPerguntas->excluirPergunta($recebeCodigo);

		echo json_encode($resultado);
		exit();
	}

	public function fichaAction(){
		$this->view->title = "Cria Ficha";
		
		$tbEsp = new Application_Model_EstratificacaoLista();
		$tbEstGru = new Application_Model_EstratificacaoGrupos();
		$tpMonitoramento = new Application_Model_TbMonitoramento();

		$x = $this->view->especialidade = $tbEsp->pegaEspecialidades();

		$recebeListaDeGrupos = $tbEstGru->listarGrupoFicha();

		$this->view->monitoramento = $tpMonitoramento->getMonitoramento();
		$this->view->recebeListaDeGrupos = $recebeListaDeGrupos;
		$this->render('add-ficha');
	}


	public function addFichaAction(){
		$this->view->title = "Cria Ficha";

		// echo '<pre>';print_r($_POST);die();
		$tipoEstratificacaoLista = new Application_Model_EstratificacaoLista();
		$usrTipo = new Application_Model_Usuarios();
		
		$recebeCodigoGrupos = $this->_request->getPost("codigoGrupos");
		$recebeCodigoEspecialidade = $this->_request->getPost("esp_codigo");
		
		$recebeUsuarioAtual = $usrTipo->getUsrAtual();
		
		$recebeUsrCodigo = $recebeUsuarioAtual->usr_codigo;
		
		$recebeUniCodigo = $recebeUsuarioAtual->uni_codigo;
		
		$dadosBasicosDaFicha = array(
			"est_nomeficha" => $this->_request->getPost("nomeDaFicha", FALSE),

			"est_nivelalto_inicio" => ($this->_request->getPost("est_nivelalto_inicio", FALSE) == '' ? 31 : $this->_request->getPost("est_nivelalto_inicio", FALSE)),
			"est_recomendacao_nivel_alto" => $this->_request->getPost("est_recomendacao_nivel_alto", FALSE),
			"est_monitoramento_alto" => ($this->_request->getPost("est_monitoramento_alto", FALSE) == '' ? 3 : $this->_request->getPost("est_monitoramento_alto", FALSE)),

			"est_nivelmedio_inicio" => ($this->_request->getPost("est_nivelmedio_inicio", FALSE) == '' ? 11 : $this->_request->getPost("est_nivelmedio_inicio", FALSE)),
			"est_nivelmedio_fim" => ($this->_request->getPost("est_nivelmedio_fim", FALSE) == '' ? 30 : $this->_request->getPost("est_nivelmedio_fim", FALSE)),
			"est_recomendacao_nivel_medio" => $this->_request->getPost("est_recomendacao_nivel_medio", FALSE),
			"est_monitoramento_medio" => ($this->_request->getPost("est_monitoramento_medio", FALSE) == '' ? 2 : $this->_request->getPost("est_monitoramento_medio", FALSE)),

			"est_nivelbaixo_inicio" => ($this->_request->getPost("est_nivelbaixo_inicio", FALSE) == '' ? 0 : $this->_request->getPost("est_nivelbaixo_inicio", FALSE)),
			"est_nivelbaixo_fim" => ($this->_request->getPost("est_nivelbaixo_fim", FALSE) == '' ? 10 : $this->_request->getPost("est_nivelbaixo_fim", FALSE)),
			"est_recomendacao_nivel_baixo" => $this->_request->getPost("est_recomendacao_nivel_baixo", FALSE),
			"est_monitoramento_baixo" => ($this->_request->getPost("est_monitoramento_baixo", FALSE) == '' ? 1 : $this->_request->getPost("est_monitoramento_baixo", FALSE)),

			"est_usr" => $recebeUsrCodigo,
			"est_unidade" => $recebeUniCodigo
		);
		$recebeIdListaEstratificacao = $tipoEstratificacaoLista->salvar($dadosBasicosDaFicha);
		// echo '<pre>';print_r($dadosBasicosDaFicha);die();
		
		$this->salvarGruposDaEstratificacao($recebeIdListaEstratificacao, $recebeCodigoGrupos);
		
		// die("saddsad");
		$this->salvarEspecilidadesDaEstratificacao($recebeIdListaEstratificacao, $recebeCodigoEspecialidade);

		$this->render('index');


	}

	public function salvarGruposDaEstratificacao($recebeIdListaEstratificacao, $recebeCodigoGrupos){
		
		$tipoEstratificacaoGrupo = new Application_Model_FichaEstratificacaoRefGrupo();

		$recebeId = $recebeIdListaEstratificacao;
		$recebeGrupos = $recebeCodigoGrupos;

		for ($contador=0; $contador < count($recebeGrupos); $contador++) { 
			
			$salvarGruposAssociadosFicha = array(

				"grupo_ref_codigo" => $recebeGrupos[$contador],
				"ficha_ref_codigo" => $recebeId
			);

			$tipoEstratificacaoGrupo->salvar($salvarGruposAssociadosFicha);
		}
		
	}



	public function salvarEspecilidadesDaEstratificacao($recebeIdListaEstratificacao, $recebeCodigoEspecialidade){
		$tipoEstratificacaoEspecilidade = new Application_Model_fichaEspecialidadesEstratificacao();
		
		$recebeId = $recebeIdListaEstratificacao;
		$recebeEspecilidade = $recebeCodigoEspecialidade;

		for ($contador=0; $contador < count($recebeEspecilidade); $contador++) { 
			
			$salvarEspecilidadesAssociadasFicha = array(

				"ref_especilidade_codigo" => $recebeEspecilidade[$contador],
				"ref_ficha_codigo" => $recebeId
			);

			$tipoEstratificacaoEspecilidade->salvar($salvarEspecilidadesAssociadasFicha);
		}

	}

	function buscarAction(){
		$term = $this->_getParam("term", FALSE);
		$tbEspL = new Application_Model_EstratificacaoLista();
		
		$recebe = $tbEspL->buscaEspec($term);
		$this->view->dados = $recebe;
		return $this->render("dados",NULL,TRUE);
	}

	public function carregaPerguntasDosGruposAction(){
		$tbEstGru = new Application_Model_EstratificacaoGrupos();
		
		$recebeCodigoGrupo = intval($this->_request->getPost("recebeCodigoDosGrupos",FALSE));

		$recebeListaDeGrupos = $tbEstGru->carregaPerguntasDosGrupos($recebeCodigoGrupo);
		
		echo json_encode($recebeListaDeGrupos);

		exit();
	}

	public function carregaPerguntasDosGruposPorFichaAction(){
		$tbEstGru = new Application_Model_EstratificacaoGrupos();

		$recebeCodigoFicha = intval($this->_request->getPost("recebeCodigoFicha",FALSE));

		$recebeListaDeGrupos = $tbEstGru->carregaPerguntasDosGruposPorFicha($recebeCodigoFicha);
		
		echo json_encode($recebeListaDeGrupos);

		exit();
	}

	public function carregaMonitoramentoAction(){
		$recebeCodigoLista = intval($this->_request->getPost("recebeCodigoFicha",FALSE));
		$recebeMonitoramento = $this->tipoLista->carregaMonitoramento($recebeCodigoLista);

		// echo '<pre>';print_r($recebeMonitoramento);die();

		echo json_encode($recebeMonitoramento);

		exit();
	}

}