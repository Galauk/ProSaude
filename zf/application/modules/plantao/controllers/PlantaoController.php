<?php

class Plantao_PlantaoController extends Zend_Controller_Action {

    public function selecionarDataAction(){
        $this->_helper->layout->disableLayout();
        // Passa por get código do usuário e data inicial
        $usr_codigo = $this->_getParam("prof", FALSE);
        $this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));
        // Se não tiver usuário ele reinderiza a página
        if(!$usr_codigo)
            return $this->_helper->viewRenderer->setNoRender(true);
        $tbPla = new Application_Model_Plantao();
        $tbUsr = new Application_Model_Usuarios();
        // Efetua calculo de data final de acordo com o número de dias implicito na configuração AGENDA_MOSTRAR_N_OPCOES
        $this->view->data_final = $tbPla->calculaDataFinal($this->view->data_inicial);
        // Array de datas com a quantidade de vagas disponível encaminhado para a view
        $this->view->vagas = $tbPla->getVagas($this->view->data_inicial, $this->view->data_final, $usr_codigo);
        // Pega nome do profissional e encaminha para view
        $this->view->nomeProf = $tbUsr->getNomeProfissional($usr_codigo);
    }

    public function selecionarHorarioAction(){
        $this->_helper->layout->disableLayout();
        // Passa por get código do convênio item e data inicial
        $usr_codigo = $this->_getParam("prof", FALSE);
        $this->view->data_inicial = $this->_getParam("ds", date("Y-m-d"));
        $tbPla = new Application_Model_Plantao();
        $tbFun = new Application_Model_Funcoes();
        $distribuicao = [
            "07:00-19:00",
            "07:00-13:00",
            "13:00-19:00",
            "19:00-00:00",
            "19:00-22:00",
            "19:00-07:00",
            "00:00-07:00"
        ];
        $this->view->distribuicao = $distribuicao;
    }

    public function salvarAction(){
        $tbPla = new Application_Model_Plantao();
        $tbUsr = new Application_Model_Usuarios();

        $session = new Zend_Session_Namespace();
        $session->dados =  $this->_request->getPost();

        $dados = array(
            "escpla_data" => $this->_request->getPost("escpla_data", FALSE),
            "med_codigo" => $this->_request->getPost("usr_codigo_medico", FALSE),
            "escpla_hora_inicio" => $this->_request->getPost("hora_inicio", FALSE),
            "escpla_hora_fim" => $this->_request->getPost("hora_fim", FALSE),
            "uni_codigo" => $this->_request->getPost("codigo_convenio", FALSE),
            "valor_plantao" => number_format($this->_request->getPost("valor_plantao", FALSE), 2, ".", ""),
            "dt_cadastro"=>"NOW()"
        );

        $tem_agendamento = $tbPla->verificaSeTemAgendamento($this->_request->getPost("usr_codigo_medico", FALSE),$this->_request->getPost("escpla_data", FALSE),$this->_request->getPost("hora_inicio", FALSE),$this->_request->getPost("hora_fim", FALSE),$this->_request->getPost("usu_codigo", FALSE));

        if($tem_agendamento->quantidade > 0){
            $this->view->dados = array("success"=>FALSE, "titulo"=>"Aviso", "mensagem"=>"Este médico já possui agendamento para esta data e horário!");
        }else{
             $this->salvaOuNao($dados);
        }
        return $this->render("dados", NULL, TRUE);
    }

    public function salvaOuNao(array $dados){
        $tbPla = new Application_Model_Plantao();

        try {
            $escpla_codigo = $tbPla->salvar($dados);
            $this->view->dados = array("success"=>TRUE,"escpla_codigo"=>$escpla_codigo);
            //colocar mais informações na sessao
        } catch (Zend_Validate_Exception $exc) { // Exceção de validação
            $this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
        }
    }

}
