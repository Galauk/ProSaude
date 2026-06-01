<?php

class ProgramasFederais_AtendimentoIndividualController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "E-SUS Inconsistências Atendimento Individual";
    }
    
    public function indexAction() {
    
    }
    
    public function inconsistenciasAction() {
        $uuid = $this->_request->getPost("uuid");
        if ($uuid) {
            $tbEsusAi = new Application_Model_EsusAtendimentoIndividual();
            $this->view->dados = $tbEsusAi->getDadosPorUuid($uuid);
        }
    }
    
    public function editaInconsistenciaAction(){
        $id = $this->_request->getParam("id");
        $tbEsusAi = new Application_Model_EsusAtendimentoIndividual();
        $this->view->dados = $tbEsusAi->getDadosPorId($id);
        $selected = $tbEsusAi->getDadosPorId($id)->co_local_atend;
        $tbLocal = new Application_Model_TbLocalAtend();
        $this->view->selectLocais = $tbLocal->selectTag($selected);
    }
    
    public function salvarEditaInconsistenciasAction(){
        $dados = $_POST;
        $id = $dados["eai_codigo"];
        $dtNasc = $dados["eai_dtnascimento"];
        $cnsProf = $dados["eai_profissional_cns"];
        $cnsPac = $dados["eai_num_cartao_sus"];
        // Dados do atendimento
        $tbEsusAi = new Application_Model_EsusAtendimentoIndividual();
        $this->view->dados = $tbEsusAi->getDadosPorId($dados["eai_codigo"]);
        $selected = $tbEsusAi->getDadosPorId($id)->co_local_atend;
        
        $tbLocal = new Application_Model_TbLocalAtend();
        $this->view->selectLocais = $tbLocal->selectTag($selected);
        // Funções de validação
        $tbFun = new Application_Model_Funcoes();
        if($tbFun->ValidaData($dtNasc)==1){
            if($tbFun->validaCnsGeral($cnsProf)==1){
                if($tbFun->validaCnsGeral($cnsPac)==1){
                    try{
                        $dados["uuid_ficha"] = null;
                        $tbEsusAi->salvar($dados);
                        $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
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
        return $this->render("atendimento-individual/edita-inconsistencia",NULL,TRUE);
    }
    
}

?>