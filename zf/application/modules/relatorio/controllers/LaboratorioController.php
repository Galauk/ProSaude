<?php

class Relatorio_LaboratorioController extends Elotech_Controller_Action_Relatorio {

	public function init() {
            
	}

	public function indexAction(){
            $this->view->title = "Exames solicitados por prontuário eletrônico";
            $this->render("index");
	}
        
        public function relSolicitanteAgeAction(){
            $this->view->title = "Exames solicitados por agendamento";
        }
        
        public function formRelPacienteAction(){
            $this->view->title = "Livro do laboratório por paciente e solicitante";
        }
        
        public function relPacienteAction(){
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $tbAge = new Application_Model_Agenda();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "RELATÓRIO DE EXAMES SOLICITADOS POR PACIENTE";
            $this->view->livro = 1;
            //$this->_helper->layout->setLayout("retrato-print");
            $this->_helper->layout->setLayout("simples");
            $solicitante = $this->_request->getPost("usr_codigo", FALSE);
            $usu_codigo = $this->_request->getPost("usu_codigo", FALSE);

            $interno = $this->_request->getPost("interno", FALSE);

            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $rel = $tbAge->relPaciente($usu_codigo, $data_inicial, $data_final, $solicitante, $interno)->toArray();
            $rel = $this->montaArrayProcedimentos($rel, $data_inicial, $data_final);
            $this->view->rel = $rel;
            $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        }
        
        public function relPacienteExamesAction(){

            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $tbAge = new Application_Model_Agenda();
            $this->view->usr = $tbUsr->getUsrAtual();
            $this->view->secretaria  = $tbSec->getDadosSec();
            $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
            $this->view->tipo_impressao = "RELATÓRIO DE EXAMES SOLICITADOS POR PACIENTE";
            //$this->_helper->layout->setLayout("retrato-print");
            $this->_helper->layout->setLayout("simples");
            
            $med_codigo = $this->_getParam("med_codigo", FALSE);
            $proc_codigo = $this->_getParam("proc_codigo", FALSE);
            $data_inicial =  $this->_getParam("data_inicial", FALSE);
            $data_final =  $this->_getParam("data_final", FALSE);
            $rel = $tbAge->relProcedimento($usu_codigo,$data_inicial, $data_final,$proc_codigo,$med_codigo)->toArray();
            $rel = $this->montaArrayPacientes($rel,$data_inicial, $data_final);
            //echo "<pre>".print_r($rel,1);die("a");

                $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
            $this->view->rel = $rel;
        }
        
        private function montaArrayPacientes($rel,$data_inicial, $data_final){
            $tbAge = new Application_Model_Agenda();
            $i = 0;
            foreach($rel as $array_dados){
                $rel[$i][pacientes] = $tbAge->relProcPac($array_dados[proc_codigo],$data_inicial, $data_final)->toArray();
                $i++;
            }
            return $rel;
        }
        
        private function montaArrayProcedimentos($rel, $data_inicial, $data_final){
            $tbAge = new Application_Model_Agenda();
            $i = 0;
            foreach($rel as $array_dados){
                $rel[$i][procedimentos] = $tbAge->relProcPac($array_dados[age_codigo], $data_inicial, $data_final)->toArray();
                $i++;
            }
            
 
            return $rel;
        }
        
        
        public function buscarAction(){           
            $term = $this->_getParam("term", FALSE);
            $tipo = $this->_getParam("tipo",FALSE);
            if (!$term){
                    return false;
            }
            
            if($tipo == "consolidado"){    
		$tbUni = new Application_Model_Unidade();
		$this->view->dados = $tbUni->buscarLocais($term);
            }else if($tipo == "individualizado"){
                $tbUni = new Application_Model_Unidade();
		$this->view->dados = $tbUni->buscarLocais($term);
            }
            
            return $this->render("dados", NULL, TRUE);
        }

        public function formExtratoExameAction(){
            $this->view->title = "Extrato de Exames Autorizados por Prestador"; 
            $tbUni = new Application_Model_Unidade();
            $unidade = $tbUni->buscar();
           //die(var_dump($unidade));
            $this->view->unidades = $unidade;
        }
        
        public function relExtratoExameAction(){
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
            $dataInicial = $this->_request->getPost("data_inicial",FALSE);
            $dataFinal = $this->_request->getPost("data_final",FALSE);
            $uni_codigo = $this->_request->getPost("uni_codigo",FALSE); 
            $proc_sus = $this->_request->getPost("proc_sus",FALSE);
            $proc_codigo = $this->_request->getPost("proc_codigo",FALSE);
            //die(var_dump($proc_sus)); 
            $tbAge = new Application_Model_Agenda();
            $tbSec = new Application_Model_Secretaria();
            $tbConf = new Application_Model_Configuracao();
            $tbUsr = new Application_Model_Usuarios();
            $tbUni = new Application_Model_Unidade();
            $unidade = $tbUni->getDados($uni_codigo); 
            $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $unidade[0]->uni_desc
                            ); 
            //die(var_dump($uni_codigo." ".$proc_sus));
            $dados = $tbAge->getAgendados($uni_codigo, $dataInicial, $dataFinal, $proc_sus, $proc_codigo);
            $this->view->dados = $dados;
            $this->view->title = "Extrato de Exames Autorizados por Prestador";
            $this->view->params = $params;  
            //$this->view->usr = $tbUsr->getUsrAtual();
            //$this->view->secretaria  = $tbSec->getDadosSec();
            //$this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");           
        }

        public function formExtratoPacienteAction(){
            $this->view->title = "Extrato por Paciente"; 
            $tbUni = new Application_Model_Unidade();
            $unidade = $tbUni->buscar();
           //die(var_dump($unidade));
            $this->view->unidades = $unidade;
        }

        public function relExtratoPacienteAction(){
            $tbUni = new Application_Model_Unidade();
            $tbAge = new Application_Model_Agenda();
            $tbUsu = new Application_Model_Usuario();
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
             
            $dataInicial = $this->_request->getPost("data_inicial",FALSE);
            $dataFinal = $this->_request->getPost("data_final",FALSE);
            $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);
            $proc_codigo = $this->_request->getPost("proc_codigo",FALSE);
            $unidade = $tbUni->getDados($uni_codigo);
            $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $unidade[0]->uni_desc
                            );         
            $pacientes = $tbAge->getPacientesAgendadosPorPeriodo($uni_codigo, $dataInicial, $dataFinal, $proc_codigo);
            //die(var_dump($pacientes));
            foreach($pacientes as $paciente){
                $dados[$paciente[usu_codigo]] = $tbUsu->getAgendaItensPorPaciente($paciente[usu_codigo], $dataFinal, $dataInicial, $proc_codigo);
            }
            $this->view->params = $params;
            $this->view->title = "Extrato por Paciente";
            $this->view->pacientes = $pacientes;
            $this->view->dados = $dados;
        }

}

?>