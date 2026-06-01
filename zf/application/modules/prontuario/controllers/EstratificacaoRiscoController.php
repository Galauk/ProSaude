<?php

class Prontuario_EstratificacaoRiscoController extends Zend_Controller_Action {

    public function init() {
		$this->_p = new Zend_Session_Namespace("prontuario");
        $this->_helper->acl->copiarPermissao("zf/prontuario/index");
        $this->view->title = "Estratificação";    
        
		$this->tipoReferenciaResposta = new Application_Model_TbReferenciaRespostaFicha();
        
    }

    public function indexAction(){
        $this->_helper->layout->setLayout("prontuario");

        $tipoEstratificacao = new Application_Model_fichaEspecialidadesEstratificacao();
        $tipoUsr = new Application_Model_Usuarios();

        $recebeDadosUsuario = $tipoUsr->getUsrAtual();

        if(isset($this->_p->age)){
			$this->view->age = $this->_p->age;
        }
        
        // echo '<pre>';print_r($recebeDadosUsuario);die();

        $recebeEspecialidadeUsuario = $recebeDadosUsuario->esp_codigo;

        $recuperFichaEspecialidade = $tipoEstratificacao->ficharPorEspecializade($recebeEspecialidadeUsuario);

        $this->view->recuperFichaEspecialidade = $recuperFichaEspecialidade;
    
    }

    public function salvarAction(){
        
        $tpEstUsu = new Application_Model_EstratificacaoUsu();

        $tpUsr = new Application_Model_Usuarios();
        
        $recebeCodigoUsr = $tpUsr->getUsrAtual()->usr_codigo;
        $recebeCodigoUni = $tpUsr->getUsrAtual()->uni_codigo;
        
        $dadosEstratificação = array(
            
            "est_listaid" => $this->_request->getPost("recebeCodigoFicha", FALSE),
            "est_score" => $this->_request->getPost("recuperaSomaTotal", FALSE),
            "est_usu_codigo" => $this->_request->getPost("recebeUsuCodigo", FALSE),
            "est_usr_codigo" => $recebeCodigoUsr,
            "est_uni_codigo" => $recebeCodigoUni,
            
        );

        $recebeIdDaEstratificacao = $tpEstUsu->salvar($dadosEstratificação);

        echo json_encode($recebeIdDaEstratificacao);

        exit;
        

    }

    public function imprimirAction(){
        $this->_helper->layout->setLayout("modelo-print");
        
        $codigoFichaUsuario = intval($this->_getParam("recebeCodigoFicha",FALSE));
        
        $recebeDados = $this->tipoReferenciaResposta->imprimirFicha($codigoFichaUsuario);

        $this->view->recebeDados = $recebeDados;
        
    }
    
    public function salvarRelacionamentoRespostaAction(){
        
        $recebeIdFichaUsuario = intval($this->_request->getPost("recebeIdFichaUsuario",FALSE));

        $matrizResposta = $this->_request->getPost("matrizResposta",FALSE);

        for ($contador = 0; $contador < count($matrizResposta); $contador++) { 
            
            $dadosEstratificação = array(
            
                "ref_res_ficha_usu" => $recebeIdFichaUsuario,
                "ref_res_resposta" => $matrizResposta[$contador][1],
                "ref_res_id_pergunta" => $matrizResposta[$contador][0],
                "ref_grupo_pergunta" => $matrizResposta[$contador][2],
                
            );

            $this->tipoReferenciaResposta->salvar($dadosEstratificação);

            
        }
        
        echo json_encode($recebeIdFichaUsuario);

        exit();
        
        
    }


}

