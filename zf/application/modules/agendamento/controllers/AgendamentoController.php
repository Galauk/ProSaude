<?php

class Agendamento_AgendamentoController extends Zend_Controller_Action {
    public function historicoAction(){
        $this->_helper->layout->disableLayout();
        $usu_codigo = $this->_getParam("usu", FALSE);
        if(!$usu_codigo){
            return $this->_redirect ("/agendamento/index");
        }
        $tbAge = new Application_Model_Agendamento();
        $this->view->itens = $tbAge->getHistoricoPorUsuario($usu_codigo);
    }
    
    public function selecionarDataAction(){
        $this->_helper->layout->disableLayout();
        // Passa por get código do usuário, código do convênio item e data inicial
        $usr_codigo = $this->_getParam("prof", FALSE);
        $coni_codigo = $this->_getParam("coni_codigo", FALSE);
        $this->view->data_inicial = $this->_getParam("de", date("Y-m-d"));
        // Se não tiver usuário ele reinderiza a página
        if(!$usr_codigo){
            return $this->_helper->viewRenderer->setNoRender(true);
        }

        $tbAge = new Application_Model_Agendamento();
        $tbConI = new Application_Model_ConvenioItens();
        //$coni_codigo = $tbConI->getItemPorUsuarios($usr_codigo,$conv_codigo);
        // Efetua calculo de data final de acordo com o número de dias implicito na configuração AGENDA_MOSTRAR_N_OPCOES
        $this->view->data_final = $tbAge->calculaDataFinal($this->view->data_inicial);
        $this->view->coni_codigo = $coni_codigo;
        // Array de datas com a quantidade de vagas disponível encaminhado para a view
        $this->view->vagas = $tbAge->getVagas($coni_codigo, $this->view->data_inicial, $this->view->data_final);
		//die("AHAHHA");
		//die("asdfasf");
        // Pega nome do profissional e encaminha para view
        $this->view->nomeProf = $tbConI->getNomeProfissional($coni_codigo);
    }
    
    public function selecionarHorarioAction(){
        $this->_helper->layout->disableLayout();
        // Passa por get código do convênio, código do convênio item e data inicial
        //$usr_codigo = $this->_getParam("prof", FALSE);
        $conv_codigo = $this->_getParam("conv_codigo", FALSE);
        $coni_codigo = $this->_getParam("coni_codigo", FALSE);
        $this->view->data_inicial = $this->_getParam("ds", date("Y-m-d"));
        // Se código do convênio item não existir redireciona a página
        if(!$coni_codigo){
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        
        $tbAge = new Application_Model_Agendamento();
        $tbConI = new Application_Model_ConvenioItens();
        $tbConH = new Application_Model_ConvenioHorarios();
        $tbConvAge = new Application_Model_ConvenioDiasSemanaAgendamento();
        $tbFun = new Application_Model_Funcoes();
        //$coni_codigo = $tbConI->getItemPorUsuarios($usr_codigo,$conv_codigo);
        $tbGrap = new Application_Model_GradePeriodo();
        // Criar array de horários com hora inicial e final, se for exceção pega da grade periodo, se não pega da tabela de convenio_horários
        $atendeQueDia = $tbFun->diaSemana($this->view->data_inicial);
        $condiAgeCod = $tbConvAge->getDadosDia($coni_codigo,$atendeQueDia)->condi_age_codigo;
        $condiAgeEnc = $tbConvAge->getDadosDia($coni_codigo, $atendeQueDia)->condi_age_encaixe;
        $horarios = $tbGrap->getHorariosDia($coni_codigo, $this->view->data_inicial, $condiAgeCod);
        // Calcula quantidade de intervalo dos horários e joga em um array
        $quantidades = $tbFun->calculaQuantidadePorIntervalo($horarios,$coni_codigo,$this->view->data_inicial,$condiAgeCod);
        // Realiza um arry de distribuição de horários pela quantidade, horarios e etc ..
        $distribuicao = $tbFun->distribuicao($quantidades,$horarios,$coni_codigo,$this->view->data_inicial,$condiAgeCod);
        $this->view->distribuicao = $distribuicao;
        // Verifica se tem encaixe na tabela de convênio itens
        $this->view->encaixe = $condiAgeEnc;
    }

    public function confereHorarioAction(){
        $this->_helper->layout->disableLayout();
        // Passa por get código do convênio, código do convênio item e data inicial
        //$usr_codigo = $this->_getParam("prof", FALSE);
        $conv_codigo = $this->_getParam("conv_codigo", FALSE);
        $coni_codigo = $this->_getParam("coni_codigo", FALSE);
        // Transforma o horário e quebra em duas casas
        $horarioGet = explode("-",$this->_getParam("horario", FALSE));
        $horario = $horarioGet[0].":".$horarioGet[1];
        
        $tbConvHorDia = new Application_Model_ConvenioHorariosDias();
        if ($tbConvHorDia->confereSeHorarioExiste($horario) >= 1) {
            return true;
        } else {
            return false;
        } 
    }
    
    public function buscarEspecialidadePorConiAction(){
        $this->_helper->layout->disableLayout();
        
        $coni_codigo = $this->_getParam("coni_codigo", FALSE);
        
        $tbConI = new Application_Model_ConvenioItens();
        $this->view->dados = $tbConI->getEspecialidadeConvenioItens($coni_codigo)->toArray();
       
        return $this->render("dados", NULL, TRUE);       
    }
    
    public function salvarAction(){
        $tbAge = new Application_Model_Agendamento();
        $tbEsp = new Application_Model_Especialidade();
        $tbUsr = new Application_Model_Usuarios();
        $tbConv = new Application_Model_Convenio();
        //echo "<pre>".  print_r($_REQUEST,1);die();
        
        $session = new Zend_Session_Namespace();
        $session->dados =  $this->_request->getPost();
        $horario_de_encaixe = $this->_request->getPost("horario_de_encaixe", FALSE);
        if($horario_de_encaixe == "S"){
            $uni_codigo = $tbUsr->getUsrAtual();
            if($uni_codigo->uni_codigo != $this->_request->getPost("codigo_convenio", FALSE)){
                $unidade = $tbConv->getUnidadePorConvenio($this->_request->getPost("codigo_convenio", FALSE));
                $this->view->dados = array("success"=>FALSE, "titulo"=>"Aviso", "mensagem"=>"As vagas de encaixe são de uso exclusivo da ".$unidade->uni_desc);
                return $this->render("dados", NULL, TRUE);
                $encaixe = 'S';
            }
            
            if($this->_request->getPost("age_data", FALSE) != date("Y-m-d")){
                $this->view->dados = array("success"=>FALSE, "titulo"=>"Aviso", "mensagem"=>"As vagas de encaixe só podem ser utilizadas no dia da consulta!");
                return $this->render("dados", NULL, TRUE);
            }
        }
        
        $dados = array(
            "age_encaixe" => $horario_de_encaixe,
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "est_codigo" =>  $this->_request->getPost("est_codigo", FALSE),
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "age_horario" => $this->_request->getPost("age_horario", FALSE),
            "age_data" => $this->_request->getPost("age_data", FALSE),
            "coni_codigo" => $this->_request->getPost("coni_codigo", FALSE),
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "coni_codigo" => $this->_request->getPost("coni_codigo", FALSE),
            "uni_codigo" => $this->_request->getPost("codigo_convenio", FALSE),
            "med_codigo" => $this->_request->getPost("usr_codigo_medico", FALSE),
            "esp_codigo" => $tbEsp->getEspecialidadePorConvenio($this->_request->getPost("esp_codigo", FALSE))->esp_codigo,
            "age_atendido"=>"N",
            "dt_cadastro"=>"NOW()",
            "tat_codigo" => $this->_request->getPost("tat_codigo", FALSE),
            "tp_cod" => ($this->_request->getPost("tp_cod") != "" ? $this->_request->getPost("tp_cod") : "99"),
            "age_observacao" => ($this->_request->getPost("agee_observacao", FALSE)?$this->_request->getPost("agee_observacao", FALSE):"")
        );
        //die(var_dump($dados));
                
        $tem_agendamento = $tbAge->verificaSeTemAgendamento($this->_request->getPost("coni_codigo", FALSE),$this->_request->getPost("age_data", FALSE),$this->_request->getPost("usu_codigo", FALSE));
        $espConfig = $this->_request->getPost("esp_codigo_config");
        //echo "<pre>".print_r($tbEsp->getEspecialidade($espConfig)->toArray());die();
        // die($tbEsp->getEspecialidade($espConfig));
        if($tbEsp->getEspecialidade($espConfig)->esp_mais_agendamento){
            $this->salvaOuNao($dados);
        } else {
            if($tem_agendamento->quantidade > 0){
                $this->view->dados = array("success"=>FALSE, "titulo"=>"Aviso", "mensagem"=>"Este paciente já possui agendamento para esta data");
            } else {
                $this->salvaOuNao($dados);
            }
        }
        return $this->render("dados", NULL, TRUE);
    }

    public function salvaOuNao(array $dados){
        // echo "<pre>".print_r($dados);die();
        $tbAge = new Application_Model_Agendamento();
        
        try {
            $age_codigo = $tbAge->salvar($dados);
            $this->view->dados = array("success"=>TRUE,"age_codigo"=>$age_codigo);
            //colocar mais informações na sessao
        }catch (Zend_Validate_Exception $exc) { // Exceção de validação
            $this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
        }
    }

    public function imprimirAction(){
        $age_codigo = $this->_getParam("age", FALSE);
        $this->_helper->layout->setLayout("modelo-print");
        $tbAge = new Application_Model_Agendamento();
        $age = $tbAge->getAgendamento($age_codigo);
        if($this->_getParam("p_horario", FALSE)){
            $this->view->p_horario = $this->_getParam("p_horario", FALSE);
        } else {
            $tbConf = new Application_Model_Configuracao();
            if($tbConf->getConfig("IMPRIMIR_PRIMEIRO_HORARIO")){
                $tbConvAge = new Application_Model_ConvenioDiasSemanaAgendamento();
                $tbFun = new Application_Model_Funcoes();
                //$coni_codigo = $tbConI->getItemPorUsuarios($usr_codigo,$conv_codigo);
                $tbGrap = new Application_Model_GradePeriodo();
                // Criar array de horários com hora inicial e final, se for exceção pega da grade periodo, se não pega da tabela de convenio_horários
                $atendeQueDia = $tbFun->diaSemana($age->age_data);
                $condiAgeCod = $tbConvAge->getDadosDia($age->coni_codigo,$atendeQueDia)->condi_age_codigo;
                $horarios = $tbGrap->getHorariosDia($age->coni_codigo, $age->age_data, $condiAgeCod)->toArray();
                $primeiro_horario = "";
                foreach($horarios as $horario){
                    $arr_hora = explode(":",$horario[hora_inicial]);
                    $hora = $arr_hora[0].$arr_hora[1];

                    $arr_p_hora = explode(":",$primeiro_horario);
                    $p_hora = $arr_p_hora[0].$arr_p_hora[1];
                    if($hora < $p_hora || $p_hora == ""){
                        $primeiro_horario = $horario[hora_inicial];
                    }
                }
                $this->view->p_horario = $primeiro_horario;
            } else {
                $this->view->p_horario = $age->age_horario;
            }
        }
        
        $this->view->usr_tipo_medico = $age->usr_tipo_medico;
        $this->view->codigo = $age_codigo;
        $this->view->usu_codigo = $age->usu_codigo;
        $this->view->usu_nome = $age->usu_nome;
        $this->view->usu_datanasc = $age->usu_datanasc;
        $this->view->usu_cartao_sus = $age->usu_cartao_sus;
        $this->view->usu_mae = $age->usu_mae;
        $this->view->domicilio = $age;
        $this->view->age = $age;
    }
    
    /*private function getPrimeiroHorarioAgendamento($coni_codigo=FALSE,$dia=FALSE){
        $tbGrap = new Application_Model_GradePeriodo();
        $horarios = $tbGrap->getHorariosDia($coni_codigo,$dia,TRUE);
        echo "<pre>".print_r($horarios,1);die("1");
    }*/

    public function getAgendamentoAction(){
        $age_codigo = $this->_getParam("age",null);
        $tbAge = new Application_Model_Agendamento();
        $row = $tbAge->getAgendamento($age_codigo)->age_codigo;
        die($row);
    }

    public function realocarPacienteAction(){
        $tbAge = new Application_Model_Agendamento();
        $dados = $this->_request->getPost();
        die(json_encode(array('success'=>$tbAge->realocarPaciente($dados))));
    }   
}