<?php

class ProgramasFederais_InconsistenciasCnesController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Inconsistências";
    }
    
    public function indexAction() {
        $tbConi = new Application_Model_ConvenioItens();
        $profissionais = $tbConi->getItensDesatualizados();
        $dados = array();
       
        foreach($profissionais as $profissional){
            $esp_uni = trim($profissional[uni_desc]." -- ".$profissional[esp_nome]);
            if (count($dados[$profissional[usr_codigo]]) > 0) {
                $dados[$profissional[usr_codigo]][esp_codigo][$profissional[esp_codigo]] =  $esp_uni;
            }else{
                
                $dados[$profissional[usr_codigo]] = array ("usr_codigo"=>$profissional[usr_codigo],
                                                           "usr_nome" => $profissional[usr_nome],
                                                           "esp_codigo" => array($profissional[esp_codigo] => $esp_uni),
                                                           "esp_atual" => $this->getEspecialidadesCorretas($profissional[usr_codigo]));
            }
        }
       // echo "<pre>".print_r($dados,1);die();
        $this->view->dados = $dados;
    }
    
    public function getEspecialidadesCorretas($usr_codigo){
        $tbMes = new Application_Model_MedicoEspecialidade();
        $especialidades = $tbMes->getEspecialidadePorMedico($usr_codigo)->toArray();
        $esp_atual = array();
        foreach($especialidades as $especialidade){
            $esp_atual = array($especialidade["esp_codigo"] => $especialidade["uni_desc"]." -- ".$especialidade["esp_nome"]);
        }
        return $esp_atual;
        
    }

   
}

?>