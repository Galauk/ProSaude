<?php

class Prontuario_ReceitaMedicaController extends Zend_Controller_Action {

	public function init() {
		$this->_helper->acl->copiarPermissao("zf/prontuario/index");
		//Zend_Layout::getMvcInstance()->setLayout("prontuario");
		$this->view->title = "Medicamento";
	}

	public function indexAction() {
		$usu_codigo = $this->_getParam("usu_codigo",false);
		$tbUsu = new Application_Model_Usuario();

		$this->_helper->layout->setLayout("prontuario");
		//die($this->_getParam("obs", FALSE));
		// no atendimento, é possivel carregar o historico por ajax.
		// Nesse caso, não deve enviar o layout junto
		$this->view->obs = $this->_getParam("obs", FALSE);
		$this->view->io_codigo = $this->_getParam("io_codigo", FALSE);
		$this->view->ate_codigo = $this->_getParam("ate_codigo", FALSE);
		$this->view->usu_codigo = $this->_getParam("usu_codigo", FALSE);
		$this->view->imprimi = $this->_getParam("imprimi", FALSE);

		if($this->view->obs){
			$this->_helper->layout->disableLayout();
			//echo "<pre>".print_r($tbUsu->getDados($usu_codigo)->usu_nome,1);exit;
			$this->view->usu_nome = $tbUsu->getDados($usu_codigo)->usu_nome;
		}else{
			$age = Application_Model_Agendamento::usuEmAberto();
		//	echo "<pre>".print_r("asdas".$age,1);exit;
			$this->view->usu_nome = $age->age_paciente;
		}

	}

	public function formAction(){
		$tbRec = new Application_Model_Receita();
		$this->view->tipo = $this->_getParam("tipo","posto");
   // echo '<pre>'.print_r($_SESSION,1);
     //       die();
		$this->view->dados = $tbRec->temReceita($this->view->tipo);
    $this->view->usu_codigo = $_SESSION[prontuario][age]->usu_codigo ? $_SESSION[prontuario][age]->usu_codigo : $this->_getParam("usu_codigo", FALSE);
//      $age_codigo = $_SESSION['prontuario']['age']->age_codigo;
    $age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
  
   if($age_codigo) {
      $tbPC = new Application_Model_PreConsulta();
     $this->view->pre = $tbPC->temPreConsulta($age_codigo);
    }
            
	}

	public function itensAction(){
		$tipo = $this->_getParam("tipo","posto");
		$obs = $this->_getParam("obs",FALSE);


		//die("ahahah");
		$tbIRec = new Application_Model_ReceitaItens();
		$this->view->itens = $tbIRec->getItens($tipo);
		$this->view->obs = $obs;
	}

	public function itensInternacaoAction(){
		$obs = $this->_getParam("obs",FALSE);
		$io_codigo = $this->_getParam("io_codigo",FALSE);
		$ate_codigo = $this->_getParam("ate_codigo",FALSE);
		$usu_codigo = $this->_getParam("usu_codigo",FALSE);
		$imprimi = $this->_getParam("imprimi",FALSE);
    //die("ate".$imprimi);

    $tbIRec = new Application_Model_ReceitaItens();
		$this->view->itens = $tbIRec->getItensInternacao($io_codigo);
		$this->view->obs = $obs;
		$this->view->io_codigo = $io_codigo;
		$this->view->usu_codigo = $usu_codigo;
		$this->view->ate_codigo = $ate_codigo;
		$this->view->imprimi = $imprimi;
		$this->render("itens");
	}


	public function historicoAction(){
		$ate_codigo = $this->_getParam("id", FALSE);
		if(!$ate_codigo)
			return $this->_redirect ("/prontuario");

		$tipo = $this->_getParam("tipo","posto");

		$tbIRec = new Application_Model_ReceitaItens();
		$this->view->itens = $tbIRec->getHistorico($ate_codigo,$tipo);
		$this->view->tipo = $tipo;
	}

	public function salvarAction() {

		if ($this->_request->isPost()) {
      // die(var_dump($this->_request->getPost()));

			$age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;

      $tbAte = new Application_Model_Atendimento();


      if(!$this->_request->getPost("ate_codigo",FALSE)){
          $ate_codigo = $tbAte->getCodigoAtendimentoPorAgendamento($age_codigo)->ate_codigo;
      }else{
          $ate_codigo = $this->_request->getPost("ate_codigo",FALSE);
      }
      //die("ate aqui");
      $io_codigo = $this->_request->getPost("io_codigo",FALSE);
			$usu_codigo = $this->_request->getPost("usu_codigo",FALSE);
			$obs = $this->_request->getPost("obs",FALSE);
			//$ate_codigo = $this->_request->getPost("ate_codigo",FALSE);

			$dados = array(
				"ate_codigo" => $ate_codigo,
				"rec_tipo" => $this->_request->getPost("rec_tipo", NULL),
				"rec_validade" => $this->_request->getPost("rec_validade", NULL),
        "rec_data" => ($this->_request->getPost("rec_data") != NULL ? $this->_request->getPost('rec_data', NULL) : "NOW()")
			);

			$dadosItens = array(

				"pro_codigo" => $this->_request->getPost("pro_codigo", NULL),
				"irec_quantidade" => $this->_request->getPost("irec_quantidade", NULL),
				"irec_recomendacao" => $this->_request->getPost("irec_recomendacao", NULL),
				"irec_produto" => $this->_request->getPost("irec_produto", NULL),
				"desc_produto" => $this->_request->getPost("desc_produto", NULL)
			);
			try {

				$tbRec = new Application_Model_Receita();
				$tbIRec = new Application_Model_ReceitaItens();
				$dadosItens["rec_codigo"] = $tbRec->salvar($dados,$obs);
				$irec_codigo = $tbIRec->salvar($dadosItens, $dados['rec_tipo']);

				$tipos = array("posto"=>"","controlados"=>"#tabs2-2","externo"=>"#tabs2-3");
				$tab = $tipos[ $dados['rec_tipo'] ];

        if($this->_request->getPost("json", NULL)){
            $this->json($irec_codigo);
        }else{
          if($obs == "S"){
            $this->_redirect("/prontuario/receita-medica/index/obs/S/io_codigo/".$io_codigo."/ate_codigo/".$ate_codigo."/usu_codigo/".$usu_codigo."/".$tab);
          }else{
            $this->_redirect("/prontuario/receita-medica/".$tab);
          }
        }

			} catch (Zend_Validate_Exception $exc) {
        if ($this->_request->getPost("json", NULL)) {
					$this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
					return $this->render("dados", NULL, TRUE);
				}

				$this->view->erro = $exc->getMessage();
				$this->view->dados = (object) $dados;
				$this->view->$dados['rec_tipo'] = (object) $dadosItens;
				$this->render("index");
			}
                        //$this->_redirect("/prontuario/receita-medica");
		} else {
			$this->_redirect("/prontuario/receita-medica");
		}
	}

	public function excluirAction(){
		$id = (int) $this->_getParam("id",0);
		$ate_codigo = $this->_getParam("ate_codigo",FALSE);
		$io_codigo = $this->_getParam("io_codigo",FALSE);


		if(!$id)
			return $this->_redirect ("/prontuario/receita-medica");

		$tbIRec = new Application_Model_ReceitaItens();
		$tbIRec->excluir($id);
		if($io_codigo){
      return $this->_redirect ("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
    }else{
      return $this->_redirect ("/prontuario/receita-medica");
    }
	}

  public function imprimirAction(){
    Zend_Layout::getMvcInstance()->setLayout("simples");
    $tbSec = new Application_Model_Secretaria();
    $this->view->sec = $tbSec->getDadosSec()->toArray();
    $tbUsr = new Application_Model_Usuarios();
    $this->view->usr = $tbUsr->getUsrAtual();
    $selecionados = $this->_getParam("selecionados", FALSE);
    $tipo = $this->_getParam("caminhoTipo","posto");
    $seg = $this->_getParam("seg",FALSE);
    $this->view->seg = $seg;
    $this->view->tipo = $tipo;
    $io_codigo = $this->_getParam("io_codigo", FALSE);
    $usu_codigo = $this->_getParam("usu_codigo", FALSE);
    $tbRec = new Application_Model_Receita();
    $this->view->dados = $tbRec->imprimir($tipo,$io_codigo,$usu_codigo,$selecionados);

    $tbUsr = new Application_Model_Usuarios();
    $this->view->dados_usr = $tbUsr->getUsrAtual();
    $this->view->tipo_impressao = "RECEITUÁRIO";
  }

  public function imprimirAnvisaAction(){
    //$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/prontuario/imprimir-anvisa.js');

    $tbSec = new Application_Model_Secretaria();
    $this->view->sec = $tbSec->getDadosSec()->toArray();
    $tbUsr = new Application_Model_Usuarios();
    $this->view->usr = $tbUsr->getUsrAtual();
    $this->_helper->layout->disableLayout();
    $selecionados = $this->_getParam("selecionados", FALSE);

    $tipo = $this->_getParam("caminhoTipo","posto");

    $this->view->tipo = $tipo;
    $io_codigo = $this->_getParam("io_codigo",FALSE);
    $usu_codigo = $this->_getParam("usu_codigo",FALSE);
    $tbRec = new Application_Model_Receita();
    $this->view->dados = $tbRec->imprimir($tipo,$io_codigo,$usu_codigo,$selecionados);
    $this->view->dados_usr = $tbUsr->getUsrAtual();
  }

  public function aprazamentoAction(){
    $this->view->headLink()->offsetUnset(3);
    $this->view->title = "Aprazamento de Receitas";
  }

  public function aprazarAction(){
    $retorno = ['success'=>false];
    $rec_codigo = $this->_getParam('rec_codigo');
    $usu_codigo = $this->_getParam('usu_codigo');
    if (!$rec_codigo || !$usu_codigo){
      return $this->_redirect("/");
    }

    if($rec_codigo){

      $receitaModel = new Application_Model_Receita();
      $receita = $receitaModel->getReceitaPorCodigo($rec_codigo);

      if(!is_null($receita)){

        $atendimentoModel = new Application_Model_Atendimento();
        $atendimento = $atendimentoModel->getDetalhes($receita['ate_codigo']);

        $agendamentoModel = new Application_Model_Agendamento();
        $agendamento = $agendamentoModel->getAgendamento($atendimento['age_codigo']);

        $usuariosModel = new Application_Model_Usuarios();
        $usuario = $usuariosModel->getUsrAtual();

        $data = date('Y-m-d');
        $hora = date('H:i');

        $novoAgendamento = array(
          "age_data" => $data,
          "age_horario" => $hora,
          "tat_codigo" => $agendamento['tat_codigo'],
          "med_codigo" => $agendamento['med_codigo'],
          "usu_codigo" => $agendamento['usu_codigo'],
          "age_atendido" => 'A',
          "age_paciente" => $agendamento['age_paciente'],
          "uni_codigo" => $agendamento['uni_codigo'],
          "esp_codigo" => $agendamento['esp_codigo'],
          "usr_codigo_cad" => $usuario->usr_codigo,
          "dt_cadastro" => $data.' '.$hora,
          "dt_atualizacao" => "NOW()",
          "age_data_atend" => "NOW()",
          "age_emergencia" => 'N'
        );

        $codigoNovoAgendamento = $agendamentoModel->salvarAgendamento($novoAgendamento);

        $novoAtendimento = $atendimento->toArray();
        $novoAtendimento['age_codigo'] = $codigoNovoAgendamento;
        $novoAtendimento['ate_data'] = $data;
        $novoAtendimento['ate_hora'] = $hora;
        $novoAtendimento['med_codigo'] = $usuario->usr_codigo;
        $novoAtendimento['ate_data_insert'] = "NOW()";

        $novoAtendimento = [
          "ate_data" => $data,
          "ate_hora" => $hora,
          "ate_reclamacao" => $atendimento['ate_reclamacao'],
          "med_codigo" => $usuario->usr_codigo,
          "usu_codigo" => $atendimento['usu_codigo'],
          "age_codigo" => $codigoNovoAgendamento,
          "ate_valor_proc" => "0.00",
          "uni_codigo" => $atendimento['uni_codigo'],
          "ate_data_insert" => "NOW()",
          "ate_simplificado" => 0,
          "co_local_atend" => $atendimento['co_local_atend'],
          "ate_tipo" => is_null($atendimento['ate_tipo']) ? 0 : $atendimento['ate_tipo'],
          "ate_nasf_aval" => is_null($atendimento['ate_nasf_aval']) ? 0 : $atendimento['ate_nasf_aval'],
          "ate_nasf_presc" => is_null($atendimento['ate_nasf_presc']) ? 0 : $atendimento['ate_nasf_presc'],
          "ate_nasf_proc" => is_null($atendimento['ate_nasf_proc']) ? 0 : $atendimento['ate_nasf_proc'],
        ];

        $codigoNovoAtendimento = $atendimentoModel->salvarAtendimento($novoAtendimento);

        $receita = $receita->toArray();

        $recitaItensModel = new Application_Model_ReceitaItens();
        $itens = $recitaItensModel->getItensReceita($rec_codigo)->toArray();
        $intervaloValidade = strtotime($receita['rec_validade']) - strtotime($receita['rec_data']);

        $validade = date('Y-m-d', strtotime($data)+$intervaloValidade);

        $dadosReceita = array(
          "ate_codigo" => $codigoNovoAtendimento,
          "rec_tipo" => $receita['rec_tipo'],
          "rec_validade" => $validade,
          "rec_data" => $data,
        );

        $codigoNovaReceita = $receitaModel->salvar($dadosReceita);

        foreach ($itens as $item) {
          $dadosItem = array(
            "rec_codigo" => $codigoNovaReceita,
            "pro_codigo" => $item['pro_codigo'],
            "irec_quantidade" => $item['irec_quantidade'],
            "irec_recomendacao" => $item['irec_recomendacao'],
            "irec_produto" => $item['irec_produto'],
            "desc_produto" => $item['desc_produto'],
          );
          $recitaItensModel->salvar($dadosItem, $dadosReceita['rec_tipo']);
        }
        $retorno['success'] = true;
        $retorno['data'] = date('d/m/Y', strtotime($data));
        $retorno['rec_codigo'] = $rec_codigo;
        $retorno['validade'] = date('d/m/Y', strtotime($validade));
      }

    }
    die(json_encode($retorno));
  }

  public function imprimirAprazadaAction(){
    $this->_helper->layout->disableLayout();
    $rec_codigo = $this->_getParam('rec_codigo');

    if(!$rec_codigo)
      die('Informe o código da receita');

    $receitaModel = new Application_Model_Receita();
    $receita = $receitaModel->getReceitaPorCodigo($rec_codigo);

    $dados = new stdClass();
    $dados->codigo = $rec_codigo;

    $atendimentoModel = new Application_Model_Atendimento();
    $atendimento = $atendimentoModel->getDetalhes($receita['ate_codigo']);

    $agendamentoModel = new Application_Model_Agendamento();
    $age = $agendamentoModel->getAgendamento($atendimento['age_codigo']);

    $receitaItensModel = new Application_Model_ReceitaItens();
    $dados->itens = $receitaItensModel->getItensReceita($rec_codigo);

    $tbUsu = new Application_Model_Usuario();
    $usu = $tbUsu->getInfo($age->usu_codigo);

    $dados->rua_nome = $usu->rua_nome;
    $dados->rua_bairro = $usu->rua_bairro;
    $dados->dom_numero = $usu->dom_numero;
    $dados->dom_codigo = $usu->dom_codigo;
    $dados->usu_nome = $usu->usu_nome;
    $dados->usu_prontuario = $usu->usu_prontuario;
    $dados->cid_nome = $usu->cid_nome;
    $dados->usu_sexo = $usu->usu_sexo;
    $dados->idade = $usu->usu_datanasc;
    $dados->usu_cartao_sus = $usu->usu_cartao_sus;
    $dados->usu_datanasc = $usu->usu_datanasc;

    $tbUni = new Application_Model_Unidade();
    $uni = $tbUni->buscarCidadeDaUnidade($age->uni_codigo);

    $dados->uni_desc = $uni->uni_desc;
    $dados->nome_cidade = $uni->cid_nome;
    $dados->uni_endereco = $uni->uni_endereco;

    $tbSec = new Application_Model_Secretaria();
    $this->view->sec = $tbSec->getDadosSec()->toArray();

    $tbUsr = new Application_Model_Usuarios();
    $this->view->usr = $tbUsr->getUsrAtual();
    $this->view->dados_usr = $this->view->usr;

    $this->view->dados = $dados;
    $this->view->tipo_impressao = "RECEITUÁRIO";
    $this->render('imprimir');
  }

  private function json($irec_codigo=FALSE) {
		$tbRe = new Application_Model_ReceitaItens();
		$re = $tbRe->getItem($irec_codigo);
    if (!$re)
        return false;

		$this->view->dados = $re->toArray();
		$this->render("dados", NULL, TRUE);
	}
}

