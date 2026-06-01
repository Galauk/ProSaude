<?php

class ProgramasFederais_FichaProcedimentoController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "E-SUS Inconsistências";
    }
    
    public function indexAction() {
    
    }

    public function inconsistenciasAction() {
        $this->view->title = 'E-SUS Inconsistências Ficha de Procedimentos';
        $uuid = $this->_request->getPost("uuid");
        if ($uuid) {
            $tbEsusFp = new Application_Model_EsusFichaProcedimento();
            $this->view->dados = $tbEsusFp->getDadosPorUuid($uuid);
        }
    }

    public function editaInconsistenciaAction(){
        $this->view->title = 'E-SUS Inconsistências Ficha de Procedimentos';
        $id = $this->_request->getParam("id");
        $tbEsusFp = new Application_Model_EsusFichaProcedimento();
        $this->view->dados = $tbEsusFp->getDadosPorId($id);
        $selected = $tbEsusFp->getDadosPorId($id)->co_local_atend;
        $tbLocal = new Application_Model_TbLocalAtend();
        $this->view->selectLocais = $tbLocal->selectTag($selected);
    }

    public function salvarEditaInconsistenciasAction(){
        // Atualiza as inconsistências em que o código do agendamento for X
        $dados = $_POST;
        $dados["uuid_ficha"] = null;
        unset($dados["age_codigo"]);
        $dtNasc = $dados["efp_dtnascimento"];
        $cnsProf = $dados["efp_profissional_cns"];
        $cnsPac = $dados["efp_num_cartao_sus"];
        $ageCodigo = $this->_request->getPost("age_codigo");
        // Dados do atendimento
        $tbEsusFp = new Application_Model_EsusFichaProcedimento();
        $this->view->dados = $tbEsusFp->getDadosPorId($ageCodigo);
        $selected = $tbEsusFp->getDadosPorId($ageCodigo)->co_local_atend;
        $tbLocal = new Application_Model_TbLocalAtend();
        $this->view->selectLocais = $tbLocal->selectTag($selected);
        // Funções de validação
        $tbFun = new Application_Model_Funcoes();
        if($tbFun->ValidaData($dtNasc)==1){
            if($tbFun->validaCnsGeral($cnsProf)==1){
                if($tbFun->validaCnsGeral($cnsPac)==1){
                    try{
                        $tbEsusFp = new Application_Model_EsusFichaProcedimento();
                        $tbEsusFp->atualizaDadosFicha($dados,$ageCodigo);
                        $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
                        $this->_redirect("programasfederais/ficha-procedimento/inconsistencias");
                    }catch(Exception $exc){
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
        return $this->render("ficha-procedimento/edita-inconsistencia",NULL,TRUE);
    }
    
}

?>