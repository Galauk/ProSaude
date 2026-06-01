<?php

class ProgramasFederais_FichaOdontologicaController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        $this->view->title = "Ficha Odontológica";
        $tbAte = new Application_Model_Atendimento();
        $this->view->dados = $tbAte->getAtendimentosOdontologicos();
    }

    public function formAction() {
        $this->view->title = "Ficha Odontológica";
        $this->carregaDadosForm();
    }
    
    public function visualizarOdontoAction(){
        $this->view->title = "Ficha Odontológica";
        $this->carregaDadosForm();
        $atendimento = new Application_Model_Atendimento();
        $tbProc = new Application_Model_OdontoProcedimentosRealizados();
        $ateCod = $this->_request->getParam("id");
        $recebeAtendOdonto = $atendimento->atendimentoOdonto($ateCod); 
        // echo "<pre>";print_r($recebeAtendOdonto);die();
        $this->view->dadosAtendOdonto = $atendimento->atendimentoOdonto($ateCod);
        // $this->view->dados = $tbAte->getAtendimentosOdontologicos();
        $this->view->atendimentoConsulta = $atendimento->recuperaAtendimentoConsultaOdontologia($ateCod);
        $this->view->atendimentoVigilanciaEncaminhamento = $atendimento->recuperaVigilanciaConduta($ateCod);
        $this->view->procedimenosRealizados = $tbProc->procedimentosOdontoEditar($ateCod);

    }

    public function editarFichaOdontoAction(){
        $this->view->title = "Ficha Odontológica";
        $this->carregaDadosForm();
        $atendimento = new Application_Model_Atendimento();
        $tbProc = new Application_Model_OdontoProcedimentosRealizados();
        $ateCod = $this->_request->getParam("id");
        $recebeAtendOdonto = $atendimento->atendimentoOdonto($ateCod); 
        $this->view->dadosAtendOdonto = $atendimento->atendimentoOdonto($ateCod);
        $this->view->atendimentoConsulta = $atendimento->recuperaAtendimentoConsultaOdontologia($ateCod);
        $this->view->atendimentoVigilanciaEncaminhamento = $atendimento->recuperaVigilanciaConduta($ateCod);
        $this->view->procedimenosRealizados = $tbProc->procedimentosOdontoEditar($ateCod);
    }

    public function carregaDadosForm(){
        $tbTipVig = new Application_Model_TbCdsTipoVigSaudeBucal();
        $tbTipCond = new Application_Model_TbCdsTipoEncamOdonto();
        $tbTipAte = new Application_Model_TipoAtendimento();
        $tbTipCons = new Application_Model_TipoConsulta();
        $tbUsr = new Application_Model_Usuarios();
        $tbLocal = new Application_Model_TbLocalAtend();
        $tbProcedimento = new Application_Model_Procedimento();
        $this->view->selectLocais = $tbLocal->selectTagLocalOdontologia();
        $this->view->vigilancia = $tbTipVig->getDados();
        $this->view->conduta = $tbTipCond->getDados();
        $this->view->encaminhamentos = $tbTipCond->getEncaminhamentos();
        $this->view->tipoAtendFicha = $tbTipAte->getTiposDeAtendimentoFicha();
        $this->view->demandaEspontanea = $tbTipAte->getDemandaEspontanea();
        $this->view->tipoAtend = $tbTipAte->getLocalAtendimentoOdonto();
        $this->view->tipoCons = $tbTipCons->getDados();
        $this->view->uniCodigo = $tbUsr->getUsrAtual()->uni_codigo;
        $this->view->procedimento_odonto_ab_sia = $tbProcedimento->recuperaProcedimentosABOdonto();
    }

    public function salvarAction() {
        $this->view->title = "Ficha Odontológica";
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            $post = $_POST;
            $ageCod = $this->salvarAgendamento($post);
            $ateCod = $this->salvarAtendimento($post,$ageCod);
            // die("alouuu");
            $this->salvarGestante($post);
            $this->salvarCondutas($post,$ateCod);
            $this->salvarVigilanciaBucal($post,$ateCod);
            $odoTratCod = $this->salvarTratamento($post,$ateCod);
            $odoPconCod = $this->salvarProcedimentoControle($odoTratCod,$ateCod);
            $odoPrealCod = $this->salvarProcedimentosRealizados($post,$odoPconCod);
            Zend_Db_Table::getDefaultAdapter()->commit();
            $this->carregaDadosForm();
            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
            return $this->render("ficha-odontologica/form",NULL,TRUE);
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->carregaDadosForm();
            $this->view->erro = $exc->getMessage();
            return $this->render("form");
        }
    }

    public function salvarAgendamento($post=FALSE) {
        $dados = array(
            "coni_codigo" => 0,
            "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "age_data" => $this->_request->getPost("data_atendimento", FALSE),
            "age_horario" => $this->_request->getPost("dt_hora_inicial", FALSE),
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "age_atendido" => "A",
            "dt_cadastro" => "NOW()",
            "tat_codigo" => $this->_request->getPost("tipo_atend", FALSE),
            "tp_cod" => $this->_request->getPost("tipo_cons", FALSE)
        );
        // echo "<pre>";print_r($dados);die();
        $tbAge = new Application_Model_Agendamento();
        try {
            return $tbAge->salvar($dados);
        } catch (Exception $exc) {
            die("aaaaaaaa".$exc->getMessage());
            return $exc->getMessage();
        }
    }

    public function salvarAtendimento($post = FALSE, $ageCod = FALSE) {
        $dados = array(
            "ate_data" => $this->_request->getPost("data_atendimento", FALSE),
            "med_codigo" => $this->_request->getPost("usr_codigo", NULL),
            "usu_codigo" => $this->_request->getPost("usu_codigo", NULL),
            "age_codigo" => $ageCod,
            "uni_codigo" => $this->_request->getPost("uni_codigo"),
            "turno" => $this->_request->getPost("turno"),
            "ate_atendido" => "S",
            "co_local_atend" => $this->_request->getPost("co_local_atend", FALSE),
            "usu_dtnascimento" => $this->_request->getPost("id_data"),
            "usu_possui_necessidade_especial" => $this->_request->getPost("usu_possui_necessidade_especial"),
            "fornecimento_odonto" => $this->_request->getPost("fornecimentoOdonto"),
            "usu_sexo" => $this->_request->getPost("usu_sexo"),
            "procedimento_odonto_ab_sia" => $this->_request->getPost("procedimento_odonto_ab_sia")
        );
        // echo "<pre>";print_r($dados);die();
        $tbAte = new Application_Model_Atendimento();
        try{
            return $tbAte->salvarAtendimento($dados);
        } catch (Exception $exc) {
            die($exc->getMessage());
            return $exc->getMessage();
        }

    }

    public function salvarGestante($post = FALSE) {
        //Dados que podem ser atualizados 
        $tbUsu = new Application_Model_Usuario();
        
        $dadosUsu = array(
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "usu_esta_gestante" => $this->_request->getPost("usu_esta_gestante", FALSE)
        );
        // echo "<pre>";print_r($dadosUsu);die();
        $tbUsu->salvarEstaGestante($dadosUsu);
    }
    
    public function salvarCondutas($post=FALSE,$ateCod=FALSE){
        $tbTipEnc = new Application_Model_RlCdsAtendOdontoTipoEncam();
        foreach ($post["conduta"] as $val) {
            $dados = "";
            $dados = array(
                "ate_codigo" => $ateCod,
                "tp_cds_encam_odonto" => $val 
            ); 
            try{
                $tbTipEnc->salvar($dados);
            } catch (Exception $exc) {
                die($exc->getMessage());
                return $exc->getMessage();
            }
        }
    }
    
    public function salvarVigilanciaBucal($post=FALSE,$ateCod=FALSE){
        foreach ($post["vigilancia"] as $val) {
            $dados = "";
            $dados = array(
                "ate_codigo" => $ateCod,
                "tp_cds_vig_saude_bucal" => $val 
            ); 
            $tbRlVig = new Application_Model_RlCdsAtendOdontTipVigBuc();
            try{
                $tbRlVig->salvar($dados);
            } catch (Exception $exc) {
                die($exc->getMessage());
                return $exc->getMessage();
            }
        }
    }

    public function salvarTratamento($post=FALSE,$ateCod=FALSE) {
        $dados = array(
            "odo_trat_dtinicial" => $this->_request->getPost("dt_atend_inicial")." ".$this->_request->getPost("dt_hora_inicial"),
            "odo_trat_dtfinal" => "NOW()",
            "odo_trat_status" => 'F',
            "ate_codigo_origem" => $ateCod
        );
        $tbOdoTrat = new Application_Model_OdontoTratamento();
        try{
            return $tbOdoTrat->salvar($dados); 
        } catch (Exception $exc) {
            die($exc->getMessage());
            return $exc->getMessage();
        }
    }
    
    public function salvarProcedimentoControle($odoTratCod=FALSE,$ateCod=FALSE){
        $dados = array(
            "odo_trat_codigo" => $odoTratCod,
            "ate_codigo" => $ateCod
        );
        $tbProcCont = new Application_Model_OdontoProcedimentosControle();
        try{
            return $tbProcCont->salvar($dados);
        } catch (Exception $exc) {
            die($exc->getMessage());
            return $exc->getMessage();
        }
    }

    public function salvarProcedimentosRealizados($post,$odoPconCod) {
        foreach ($post["procedimento"] as $val) {
            $dados = "";
            $dados = array(
                "odo_pcon_codigo" => $odoPconCod,
                "proc_codigo" => $val,
                "odo_preal_dtcadastro" => "NOW()"
            );
            $tbProcRea = new Application_Model_OdontoProcedimentosRealizados();
            try{
                $tbProcRea->salvar($dados);
            } catch (Exception $exc) {
                die($exc->getMessage());
                return $exc->getMessage();
            }
        }
    }
    
    public function buscarAction(){
        $this->view->title = "Ficha Odontológica";
        $term = $this->_request->getPost("busca");
        $tipoBusca = $this->_request->getPost("tipo_busca");
        $tbAte =  new Application_Model_Atendimento();
        $this->view->dados = $tbAte->getAtendimentosOdontologicosProfissionais($term,$tipoBusca);
        return $this->render("index");
    }
    
    public function excluirAction(){
        $this->view->title = "Ficha Odontológica";
        $ateCod = $this->_request->getParam("id");
        $tbAge = new Application_Model_Agendamento();
        $tbCond = new Application_Model_RlCdsAtendOdontoTipoEncam();
        $tbVig = new Application_Model_RlCdsAtendOdontTipVigBuc();
        $tbProc = new Application_Model_OdontoProcedimentosRealizados();
        $tbProcCont = new Application_Model_OdontoProcedimentosControle();
        $tbTrat = new Application_Model_OdontoTratamento();
        $tbAte = new Application_Model_Atendimento();
        $dadosAte = $tbAte->getDadosAtendimentosOdontologico($ateCod);
        try{
            $tbCond->excluirPorAtendimento($ateCod);
            $tbVig->excluirPorAtendimento($ateCod);
            $tbProc->excluirPorProcedimentoControle($dadosAte->odo_pcon_codigo);
            $tbProcCont->excluirPorAtendimento($ateCod);
            $tbTrat->excluirPorAtendimento($ateCod);
            $tbAte->excluir($ateCod);
            $this->view->dados = $tbAte->getAtendimentosOdontologicos();
            $this->view->dialog = array("Confirmação","Dados excluído com sucesso!",300,140);
            return $this->render("ficha-odontologica/index",NULL,TRUE);
        } catch (Exception $ex) {
            $ex->getMessage();
            $this->view->dados = $tbAte->getAtendimentosOdontologicos();
            $this->view->erro = $exc->getMessage();
            return $this->render("index");
        }
    }

    public function inconsistenciasAction() {
        $this->view->title = 'E-SUS Inconsistências Odontologia';
        $uuid = $this->_request->getPost("uuid");
        if ($uuid) {
            $tbEsusOdo = new Application_Model_EsusOdonto();
            $this->view->dados = $tbEsusOdo->getDadosPorUuid($uuid);
        }
    }

    public function editaInconsistenciaAction(){
        $id = $this->_request->getParam("id");
        $tbEsusOdo = new Application_Model_EsusOdonto();
        $this->view->dados = $tbEsusOdo->getDadosPorId($id);
        $selected = $tbEsusOdo->getDadosPorId($id)->co_local_atend;
        $tbLocal = new Application_Model_TbLocalAtend();
        $this->view->selectLocais = $tbLocal->selectTag($selected);
    }

    public function salvarEditaInconsistenciasAction(){
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
        if($tbFun->ValidaData($dtNasc)==1){
            if($tbFun->validaCnsGeral($cnsProf)==1){
                if($tbFun->validaCnsGeral($cnsPac)==1){
                    try{
                        $dados["uuid"] = null;
                        $tbEsusOdo->salvar($dados);
                        $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
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
        return $this->render("ficha-odontologica/edita-inconsistencia",NULL,TRUE);
    }


}
