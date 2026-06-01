<?php

class ProgramasFederais_FichaComplementarController extends Zend_Controller_Action
{

  public function init()
  {

  }

  public function indexAction()
  {
    $this->view->title = "Ficha Complementar - Síndrome Neurológica por Zika/Microcefalia";
    //die("asfdasfd");
    $tbEfc = new Application_Model_EsusFichaComplementar();
    $this->view->dados = $tbEfc->buscarFichas();

  }

  public function formAction()
  {
    $codFicha = $this->_request->getParam("id");
    $this->carregaDadosForm($codFicha);
    $this->view->title = "Ficha Complementar - Síndrome Neurológica por Zika/Microcefalia";
    $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.buscarResponsavel.js');
  }

  public function carregaDadosForm($codFicha)
  {
    if ($codFicha) {
      $dados = (new Application_Model_EsusFichaComplementar())->getDadosPorId($codFicha);
      $this->view->dados = $dados;
      $this->view->dadosUsu = (new Application_Model_Usuario())->buscaDadosEspeciais($dados->usu_codigo);
      $this->view->especialidades = (new Application_Model_Especialidade())->getEspecialidadePorProfissional($dados->usr_codigo, $dados->uni_codigo);
    }
  }

  public function salvarAction()
  {

    Zend_Db_Table::getDefaultAdapter()->beginTransaction();

    try {

      /*$tbUsu = new Application_Model_Usuario();
      $dadosUsu = array(
        "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
        "usu_tem_diabete" => $this->_request->getPost("diabetico", FALSE),
        "usu_esta_gestante" => $this->_request->getPost("gestante", FALSE),
        "usu_tem_hipertensao" => $this->_request->getPost("hipertensao", FALSE),
        "risco_idoso" => $this->_request->getPost("risco_idoso", FALSE),
        "risco_crianca" => $this->_request->getPost("risco_crianca", FALSE),
        "risco_diabetes" => $this->_request->getPost("risco_diabetes", FALSE),
        "risco_gestacao" => $this->_request->getPost("risco_gestacao", FALSE),
        "risco_hipertensao" => $this->_request->getPost("risco_hipertensao", FALSE),
        "risco_psico" => $this->_request->getPost("risco_psico", FALSE)
      );
      $tbUsu->salvar($dadosUsu);*/

      $tbEfc = new Application_Model_EsusFichaComplementar();
      $dadosFicha = array(
        "usr_codigo"=>$this->_request->getPost("usr_codigo", FALSE),
        "esp_codigo"=>$this->_request->getPost("esp_codigo", FALSE),
        "uni_codigo"=>$this->_request->getPost("uni_codigo", FALSE),
        "usr_equipe_codigo"=>$this->_request->getPost("cod_equipe", FALSE),
        "efc_data"=>$this->_request->getPost("efc_data", FALSE),
        "efc_turno"=>$this->_request->getPost("efc_turno", FALSE),
        "usu_codigo"=>$this->_request->getPost("usu_codigo", FALSE),
        "efc_usu_responsavel"=>$this->_request->getPost("efc_usu_responsavel", FALSE),
        "efc_data_olhinho"=>$this->_request->getPost("efc_data_olhinho", FALSE),
        "efc_res_olhinho"=>$this->_request->getPost("efc_res_olhinho", FALSE),
        "efc_data_fundo"=>$this->_request->getPost("efc_data_fundo", FALSE),
        "efc_res_fundo"=>$this->_request->getPost("efc_res_fundo", FALSE),
        "efc_data_orelhinha"=>$this->_request->getPost("efc_data_orelhinha", FALSE),
        "efc_res_orelhinha"=>$this->_request->getPost("efc_res_orelhinha", FALSE),
        "efc_data_transfontanela"=>$this->_request->getPost("efc_data_transfontanela", FALSE),
        "efc_res_transfontanela"=>$this->_request->getPost("efc_res_transfontanela", FALSE),
        "efc_data_tomografia"=>$this->_request->getPost("efc_data_tomografia", FALSE),
        "efc_res_tomografia"=>$this->_request->getPost("efc_res_tomografia", FALSE),
        "efc_data_ressonancia"=>$this->_request->getPost("efc_data_ressonancia", FALSE),
        "efc_res_ressonancia"=>$this->_request->getPost("efc_res_ressonancia", FALSE)

      );
      if($this->_request->getPost("efc_codigo", FALSE)){
        $dadosFicha['efc_codigo'] = $this->_request->getPost("efc_codigo", FALSE);
      }
      $tbUni = new Application_Model_Unidade();
      $cod_ibge = $tbUni->buscarCidadeDaUnidade($dadosFicha['uni_codigo'])->toArray();
      $dadosFicha['efc_cod_igbe_mun'] = $cod_ibge[0]['uni_codigo_ibge'];

      $tbEfc->salvar($dadosFicha);

      $this->view->dialog = array("Confirmação", "Dados salvo com sucesso!", 300, 140);


      $valida = $this->_getParam("valida");
      Zend_Db_Table::getDefaultAdapter()->commit();

      if (!$valida)
      return $this->_redirect("/programasfederais/ficha-complementar/index");
      else
      return $this->_redirect("/programasfederais/ficha-complementar/inconsistencias");

    } catch (Exception $exc) {
      Zend_Db_Table::getDefaultAdapter()->rollBack();
      $this->carregaDadosForm();
      $this->view->erro = $exc->getMessage();
      return $this->render("form");
    }
  }


  public function buscarAction()
  {
    $this->view->title = "Ficha Complementar - Síndrome Neurológica por Zika/Microcefalia";

    if ($this->_request->getPost("busca") != "") {
      $term = $this->_request->getPost("busca");
    } else {
      $term = $this->_request->getPost("busca2");
    }
    $tipoBusca = $this->_request->getPost("tipo_busca");
    $tbEfc = new Application_Model_EsusFichaComplementar();
    $this->view->dados = $tbEfc->buscarFichas($term, $tipoBusca);
    return $this->render("index");
  }

  public function excluirAction()
  {

    $this->view->title = "Ficha Complementar - Síndrome Neurológica por Zika/Microcefalia";

    $efcCod = $this->_request->getParam("id");

    $tbEfc = new Application_Model_EsusFichaComplementar();


    try {

      $tbEfc->excluir($efcCod);
      $this->view->dados = $tbEfc->buscarFichas();
      $this->view->dialog = array("Confirmação", "Dados excluído com sucesso!", 300, 140);

      return $this->render("index");
    } catch (Exception $ex) {
      $ex->getMessage();
      $this->view->dados = $tbEfc->buscarFichas();
      $this->view->erro = $ex->getMessage();

      return $this->render("index");
    }
  }

  public function inconsistenciasAction()
  {
    $this->view->title = 'E-SUS Inconsistências Odontologia';
    $uuid = $this->_request->getPost("uuid");
    if ($uuid) {
      $tbEsusOdo = new Application_Model_EsusOdonto();
      $this->view->dados = $tbEsusOdo->getDadosPorUuid($uuid);
    }
  }

  public function editaInconsistenciaAction()
  {
    $id = $this->_request->getParam("id");
    $tbEsusOdo = new Application_Model_EsusOdonto();
    $this->view->dados = $tbEsusOdo->getDadosPorId($id);
    $selected = $tbEsusOdo->getDadosPorId($id)->co_local_atend;
    $tbLocal = new Application_Model_TbLocalAtend();
    $this->view->selectLocais = $tbLocal->selectTag($selected);
  }

  public function salvarEditaInconsistenciasAction()
  {
    $dados = $_POST;
    $id = $dados["eo_codigo"];
    $dtNasc = $dados["eo_dtnascimento"];
    $cnsProf = $dados["eo_profissional_cns"];
    $cnsPac = $dados["eo_num_cartao_sus"];
    // Dados do atendimento
    $tbEsusOdo = new Application_Model_EsusOdonto();
    $this->view->dados = $tbEsusOdo->getDadosPorId($id);
    $selected = $tbEsusOdo->getDadosPorId($id)->co_local_atend;
    $tbLocal = new Application_Model_TbLocalAtend();
    $this->view->selectLocais = $tbLocal->selectTag($selected);
    // Funções de validação
    $tbFun = new Application_Model_Funcoes();
    if ($tbFun->ValidaData($dtNasc) == 1) {
      if ($tbFun->validaCnsGeral($cnsProf) == 1) {
        if ($tbFun->validaCnsGeral($cnsPac) == 1) {
          try {
            $dados["uuid"] = null;
            $tbEsusOdo->salvar($dados);
            $this->view->dialog = array("Confirmação", "Dados salvo com sucesso!", 300, 140);
            $this->_redirect("programasfederais/ficha-odontologica/inconsistencias");
          } catch (Exception $exc) {
            $this->view->erro = $exc->getMessage();
          }
        } else {
          $this->view->erro = "Erro! CNS paciente inválido!";
        }
      } else {
        $this->view->erro = "Erro! CNS profissional inválido!";
      }
    } else {
      $this->view->erro = "Erro! Data de nascimento inválida!";
    }
    return $this->render("ficha-odontologica/edita-inconsistencia", NULL, TRUE);
  }


}
