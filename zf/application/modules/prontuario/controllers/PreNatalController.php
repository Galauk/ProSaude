<?php

class Prontuario_PreNatalController extends Zend_Controller_Action
{

  public function init()
  {
    $this->_helper->acl->copiarPermissao("zf/prontuario/index");
    Zend_Layout::getMvcInstance()->setLayout("prontuario");
    //Fazer função para analisar se é a primeira consulta ou não, alterando o título
  }

  public function indexAction()
  {
    $age = Application_Model_Agendamento::usuEmAberto();
    $this->view->usu_codigo = $age->usu_codigo;
    $this->view->esp_codigo = $age->esp_codigo;
    $tbAte = new Application_Model_Atendimento();
    $ate_codigo = $tbAte->temAtendimento($age->ate_codigo)->ate_codigo;
    $this->view->ate_codigo = $ate_codigo;
    $tbGru = new Application_Model_GrupoExame();
    $this->view->grupos = $tbGru->getGrupos(TRUE);

    $tbUsu = new Application_Model_Usuario();
    $usuario = $tbUsu->listaDadosUsuario($age->usu_codigo);
    $this->view->usuario = $usuario;
    $tbUsd = new Application_Model_UsuarioDados();
    $this->view->dadosUsuario = $tbUsd->buscaDadosUsuario($this->view->usu_codigo);

    $tbPc = new Application_Model_PreConsulta();
    $this->view->dados = $tbPc->getUltima();

    $tbApn = new Application_Model_AtendimentoPrenatal();
    $tbCid = new Application_Model_Cid();
    $tbCiap = new Application_Model_RlCdsAtendIndividualCiap();
    $this->view->numeroGestacao = $tbApn->checaNumeroGestacao($this->view->usu_codigo);
    $this->view->ultimaConsulta = $tbApn->checaUltimaConsulta($this->view->usu_codigo);
    $this->view->atendimento_pre_natal = $tbApn->getDados($ate_codigo);
    $dum = $tbApn->getDum($age->usu_codigo);
    $this->view->ultima_dum = $dum;
    $consultas = $tbApn->buscaConsultas($this->view->usu_codigo, $this->view->numeroGestacao)->toArray();

    foreach ($consultas as $key => $consulta) {
      $consultas[$key]['cids'] = $tbCid->getDadosPorAtendimento($consulta['ate_codigo'])->toArray();
	 // die("asdfasdf");
      $consultas[$key]['ciaps'] = $tbCiap->getCiapAtendimento($consulta['ate_codigo'])->toArray();
    }

    $this->view->consultas = $consultas;
    if ($dum) {
      $dpp = $this->calculaDPP($dum);
      $this->view->dpp = $dpp;
    }
   // die("asdfasdf");

    $tbReq = new Application_Model_RequisicaoExame();
    $this->view->itens = $tbReq->getItens(FALSE, $ate_codigo);
  }

  public function salvarAction()
  {
    if ($this->_request->isPost()) {
      $json = $this->_request->getPost("json", FALSE);
      $age = Application_Model_Agendamento::usuEmAberto();

      $tbAte = new Application_Model_Atendimento();
      $ate_codigo = $tbAte->temAtendimento($age->ate_codigo)->ate_codigo;

      $tbUsr = new Application_Model_Usuarios();
      $usuario['usu_codigo'] = $_SESSION['prontuario']['age']->usu_codigo;

      $dadosUsuario = [
        "usu_codigo" => $_SESSION['prontuario']['age']->usu_codigo,
        "cirurgias" => $this->_request->getPost("cirurgias", FALSE),
        "internacoes" => $this->_request->getPost("internacoes", FALSE),
        "observacoes" => $this->_request->getPost("observacoes", NULL),
        "gestas_previas" => $this->_request->getPost("gestas_previas", NULL),
        "cesareas" => $this->_request->getPost("cesareas", NULL),
        "rn2500" => $this->_request->getPost("rn2500", NULL),
        "abortos" => $this->_request->getPost("abortos", NULL),
        "nascidos_vivos" => $this->_request->getPost("nascidos_vivos", NULL),
        "rn4500" => $this->_request->getPost("rn4500", NULL),
        "partos" => $this->_request->getPost("partos", NULL),
        "vivem" => $this->_request->getPost("vivem", NULL),
        "mortos_1sem" => $this->_request->getPost("mortos_1sem", NULL),
        "partos_vaginais" => $this->_request->getPost("partos_vaginais", NULL),
        "nascidos_mortos" => $this->_request->getPost("nascidos_mortos", NULL),
        "mortos_d1sem" => $this->_request->getPost("mortos_d1sem", NULL),
        "partos_domiciliares" => $this->_request->getPost("partos_domiciliares", NULL),
        "desfecho" => $this->_request->getPost("desfecho", NULL),
        "vacinacao_em_dia" => $this->_request->getPost("vacinacao_em_dia", NULL)
      ];
      if ($this->_request->getPost("usd_codigo", FALSE)) {
        $dadosUsuario['usd_codigo'] = $this->_request->getPost("usd_codigo");
      }

      //Faz a análise para definir qual o número da gestação e valida se está gravida ou não
      if ($this->_request->getPost("tipo_consulta") == 2) {
        $numeroGestacao = $this->_request->getPost("numero_gestacao");
        $usuario['usu_esta_gestante'] = "f";
        $usuario['risco_gestacao'] = '';
      } else {
        $usuario['usu_esta_gestante'] = "t";
        $tbApn = new Application_Model_AtendimentoPrenatal();
        $ultimaConsulta = $tbApn->checaUltimaConsulta($_SESSION['prontuario']['age']->usu_codigo);
        if ($ultimaConsulta == 1) {
          $numeroGestacao = $this->_request->getPost("numero_gestacao");
        } else {
          $numeroGestacao = ($this->_request->getPost("numero_gestacao") + 1);
        }
      }

      $dadosPreNatal = [
        "ate_codigo" => $ate_codigo,
        "tipo_gravidez" => $this->_request->getPost("tipo_gravidez", FALSE),
        "gravidez_planejada" => $this->_request->getPost("gravidez_planejada", FALSE),
        "edema" => $this->_request->getPost("edema", NULL),
        "altura_uterina" => $this->_request->getPost("altura_uterina", NULL),
        "batimento_cardiaco" => $this->_request->getPost("batimento_cardiaco", NULL),
        "movimentacao_fetal" => $this->_request->getPost("movimentacao_fetal", NULL),
        "dum" => $this->_request->getPost("dum", NULL),
        "tipo_consulta" => $this->_request->getPost("tipo_consulta", NULL),
        "numero_gestacao" => $numeroGestacao,
        "data_ultimo_parto" => $this->_request->getPost("data_ultimo_parto", NULL),
        "data_provavel_parto" => $this->_request->getPost("dpp", NULL)
      ];
      $atp_codigo = $this->_request->getPost("atp_codigo", FALSE);
      if ($atp_codigo) {
        $dadosPreNatal['atp_codigo'] = $atp_codigo;
      }

      try {
        $tbUsu = new Application_Model_Usuario();
        $tbUsu->salvar($usuario);

        $tbUsd = new Application_Model_UsuarioDados();
        $usuarioDados = $tbUsd->salvar($dadosUsuario);

        $tbApn = new Application_Model_AtendimentoPrenatal();
        $AtendimentoPreNatal = $tbApn->salvar($dadosPreNatal);
        $this->view->dados = array("success" => TRUE, "mensagem" => "Dados cadastrados com sucesso!");
        $this->render("dados", NULL, TRUE);
      } catch (Zend_Validate_Exception $exc) {
        if ($json) {
          $this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
          $this->render("dados", NULL, TRUE);
        } else {
          $this->view->erro = $exc->getMessage();
          $this->view->dados = (object)$dados;
          $this->render("index");
        }
      }
    } else {
      $this->_redirect("/prontuario/pre-natal");
    }
  }

  public function editarAction()
  {
    $id = $this->_getParam("id", FALSE);
    if (!$id)
      return $this->_redirect("/prontuario/pre-natal");

    $tbApn = new Application_Model_AtendimentoPrenatal();
    $atendimentoPreNatal = $tbApn->find($id);
  }

  public function calculaDPP($dum)
  {
    $data = new DateTime($dum);
    $data->add(new DateInterval('P7D'));
    $data->sub(new DateInterval('P3M'));
    $data->add(new DateInterval('P12M'));
    return $data->format('Y-m-d');
  }

  public function calculaDppAction() {
    //error_reporting(E_ALL);
    $dum = $this->_request->getParam('dum');
    $data = new DateTime($dum);

    $data->add(new DateInterval('P7D'));
    $data->sub(new DateInterval('P3M'));
    $data->add(new DateInterval('P12M'));
    $this->view->dados = array("success" => TRUE, "data" => $data->format('Y-m-d'));
    return $this->render("dados", NULL, TRUE);
  }

  public function calculaIdadeGestacionalAction() {
    //error_reporting(E_ALL);

    $dateStart = new DateTime($this->_request->getParam('dum'));
    $dateNow = new DateTime(date('Y-m-d'));

    $dateDiff = $dateStart->diff($dateNow);
    $semanas = ($dateDiff->days/7);
    $dataf = number_format($semanas, 1, '.', '');

    $this->view->dados = array("success" => TRUE, "data" => $dataf);
    return $this->render("dados", NULL, TRUE);
  }
}
