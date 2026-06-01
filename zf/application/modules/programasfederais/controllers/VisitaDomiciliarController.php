<?php

class ProgramasFederais_VisitaDomiciliarController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "E-SUS Inconsistências";
    }
    
    public function indexAction() {
    
    }
	
	public function inconsistenciasAction() {
		$this->view->title = 'E-SUS Inconsistências Visita Domiciliar';
        $uuid = $this->_request->getPost("uuid");
        if ($uuid) {
            $tbEsusVd = new Application_Model_EsusVisitaDomiciliar();
            $this->view->dados = $tbEsusVd->getDadosPorUuid($uuid);
        }
    }

    public function editaInconsistenciaAction(){
        $this->view->title = 'E-SUS Inconsistências Visita Domiciliar';
        $id = $this->_request->getParam("id");
        $tbEsusVd = new Application_Model_EsusVisitaDomiciliar();
        $this->view->dados = $tbEsusVd->getDadosPorId($id);
    }

    public function salvarEditaInconsistenciasAction(){
        // Atualiza as inconsistências em que o código do agendamento for X
        $dados = $_POST;
        $dados["uuid_ficha"] = null;
        // Dados do post
        $ine = $dados["esv_ine"];
        $dtNasc = $dados["esv_usu_datanasc"];
        $cnsProf = $dados["esv_profissional_cns"];
        $cnsPac = $dados["esv_usu_cns"];
        $esvCodigo = $this->_request->getPost("esv_codigo");
        // Dados do atendimento
        $tbEsusVd = new Application_Model_EsusVisitaDomiciliar();
        $this->view->dados = $tbEsusVd->getDadosPorId($esvCodigo);
        // Funções de validação
        $tbFun = new Application_Model_Funcoes();
        if($tbFun->ValidaData($dtNasc)==1){
            if($tbFun->validaCnsGeral($cnsProf)==1){
                if($tbFun->validaCnsGeral($cnsPac)==1){
                    if ($tbFun->validaIne($ine)==1) {
                        try{
                            $tbEsusVd = new Application_Model_EsusVisitaDomiciliar();
                            $tbEsusVd->salvar($dados);
                            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
                            $this->_redirect("programasfederais/visita-domiciliar/inconsistencias");
                        }catch(Exception $exc){
                            $this->view->erro = $exc->getMessage();
                        }
                    } else {
                        $this->view->erro = "Erro! INE inválido!";
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
        return $this->render("visita-domiciliar/edita-inconsistencia",NULL,TRUE);
    }
    
}

?>