<?php

class Prontuario_IndexController extends Zend_Controller_Action {

    private $_p;

    public function init() {

        Zend_Layout::getMvcInstance()->setLayout("prontuario");
        $this->_p = new Zend_Session_Namespace("prontuario");

            $usucod = $_SESSION['prontuario']['age']->usu_codigo;
        if(!empty($usucod)) {
            $tbUsu = new Application_Model_Usuario();
            $usuDados = $tbUsu->listaDadosUsuario($usucod);
            $from = new DateTime($usuDados[0]['datanascimento']);
            $to   = new DateTime('today');
            $idade = $from->diff($to)->y;
            $_SESSION['usu_nome'] = $usuDados[0]['nome'];
            $_SESSION['idade'] = $idade;
            $_SESSION['usu_mae'] = $usuDados[0]['pep_mae'];
            $_SESSION['est_risco1'] = $est_risco['ate_estratificacao_risco_g1'];
            $_SESSION['est_risco2'] = $est_risco['ate_estratificacao_risco_g2'];
        }
    }

    public function indexAction() {

        $this->view->title = "Prontuário Eletrônico";
        // ha paciente em atendimento?

        if (false !== ($age = Application_Model_Agendamento::usuEmAberto())) {
            $this->view->age = $age;

            // filtrar atendimentos?
            $this->view->term = $this->_request->getPost("term", FALSE);
        }
        else // vai para "agenda do dia"
            return $this->_redirect("/prontuario/agenda-do-dia");
    }

    public function menuAction() {
        $tbUsr = new Application_Model_Usuarios();
        $tbAte = new Application_Model_Atendimento();
        $tbPC = new Application_Model_PreConsulta();
        $tbAgenda = new Application_Model_Agendamento();
        $medicos = $tbAgenda->getAgenda();
                
        if($tbUsr->isMedico()){
            $this->view->medicos = $medicos;
        }else{
            $listaDeMedicos = array();
            foreach ($medicos as $medico) {
                $contadorControle = 0;
                foreach ($listaDeMedicos as $medicoDaLista) {
                    if($medico['usr_nome'] == $medicoDaLista['usr_nome']){
                        $contadorControle = $contadorControle + 1;
                    }
                }
                if($contadorControle == 0){
                    $listaDeMedicos[] = $medico;
                }
            }
            $this->view->medicos = $listaDeMedicos;
        }
        
        if (isset($this->_p->age)) {
            $this->view->age = $this->_p->age;

            $this->view->temAtendimento = $tbAte->temAtendimentoMedico($this->_p->age->age_codigo);

            $this->view->temPreConsulta = $tbPC->temPreConsulta($this->_p->age->age_codigo);
            $this->view->isConsultaComEnfermeiro = ($this->_p->age->med_codigo == $tbUsr->getUsrAtual()->usr_codigo);
            $this->view->enfUpa = $this->_p->age->med_codigo;
        }
        $this->view->isMedico = $tbUsr->isMedico();
        $this->view->fazPreConsulta = $tbUsr->fazPreConsulta();
        $this->view->tipo_usr = $tbUsr->getUsrAtual()->usr_tipo_medico;
    }

    public function iniciarAction() {

        $age_codigo = $this->_getParam("cod", FALSE);
        $tbAge = new Application_Model_Agendamento();
        if ($age_codigo && !$tbAge->usuEmAberto()){
            $tbAge->iniciar($age_codigo);
        }

        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual();
        
        return $this->_redirect("/prontuario");
    }

    public function cancelarAction() {
        // die("01");

        Application_Model_Agendamento::cancelarAgendaAtual();
        $tbUsr = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $age_codigo = $this->_getParam("age", FALSE);
        $tbAte = new Application_Model_Atendimento();
        $ate = $tbAte->estaEmAtendimento($age_codigo);
        //die("02");
        if ($ate->age_atendido == "E") {
            if ($tbUsr->isMedico()) {
                $retorno = $tbAte->buscaRetornoOrigem($age_codigo);
                if ($retorno->ate_encaminhamento == "S") {
                    $tbAge->alteraSituacao("I", $age_codigo);
                } else {
                    $tbAge->alteraMedico($age_codigo, $ate->med_codigo, 'P');
                    //$tbAge->alteraMedico($age_codigo, '99999', 'P');
                }
            } else {
                $retorno = $tbAte->buscaRetornoOrigem($age_codigo);
                if ($retorno->ate_encaminhamento == "S") {
                    $tbAge->alteraSituacao("I", $age_codigo);
                } else {
                    $tbAge->alteraMedico($age_codigo, $ate->med_codigo, 'S');
                    //$tbAge->alteraMedico($age_codigo, '99999', 'S');
                }
            }
        }
        setcookie("usu_nome",' ');
        setcookie("usu_mae",' ');
        setcookie("idade",' ');
        setcookie("est_risco",' ');
        $this->_redirect("/prontuario/agenda-do-dia");
    }

    public function destroiSessionAction() {
        setcookie("usu_nome",' ');
        setcookie("usu_mae",' ');
        setcookie("idade",' ');
        setcookie("est_risco",' ');        
        $s = new Zend_Session_Namespace("logon");
        $s->unsetAll();
        $s = new Zend_Session_Namespace("prontuario");
        $s->unsetAll();
        return $this->_redirect("/prontuario/agenda-do-dia");
    }

}

