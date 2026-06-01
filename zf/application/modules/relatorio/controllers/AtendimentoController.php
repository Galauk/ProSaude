<?php

class Relatorio_AtendimentoController extends Elotech_Controller_Action_Relatorio {

	private $tbAte;
	
	public function init() {
                $this->_helper->acl->allow(NULL);
		$this->view->title = "Atendimento por procedimento";		
		$this->tbAte = new Application_Model_Atendimento();
	}

	public function indexAction() {
		
	}
	
	public function procedimentosPorUnidadeAction(){		
		$uni_codigo = $this->_request->getPost("uni_codigo", FALSE);
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);

		if (!$uni_codigo) {
			$this->view->action = array("action" => "procedimentos-por-unidade");
			return $this->render("unidade-data", NULL, TRUE); // mostra action para pedir os dados		
		}

		$where = $this->tbAte->relProcedimentoPorUnidade($this->view, $uni_codigo, $data_inicial, $data_final);
		$this->relatorio($where);
	}
        public function relAtendimentoPorIdadeAction(){
                Zend_Layout::getMvcInstance()->setLayout("relatorio");
                //echo "<pre>".print_r($_POST,1);die();
		$uni_codigo = $this->_request->getPost("uni_codigo", FALSE);
                $uni_desc = $this->_request->getPost("uni_desc", FALSE);
                $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
                $usr_nome = $this->_request->getPost("usr_nome", FALSE);
                $esp_codigo = $this->_request->getPost("esp_codigo", FALSE);
		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);                
		$usu_sexo = $this->_request->getPost("usu_sexo", FALSE);  
                
                              
                $this->view->unidade = $uni_desc;
                $this->view->usr_nome = $usr_nome;
                $this->view->sexo = $usu_sexo;
		$this->view->dados = $this->tbAte->relAtendimentoPorIdade($uni_codigo, $usr_codigo,$esp_codigo,$data_inicial, $data_final, $usu_sexo);                
                $array = array('data_inicial' => $data_inicial,
                               'data_final' => $data_final,
                               'uni_desc'=> $uni_desc,
                               'usr_nome' => $usr_nome);
                $this->view->params = serialize($array);
                $this->view->title = "Relatório de atendimento por idade";
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
		return $this->render("atendimento-por-idade");
	}
        public function carregaEspecialidadePorMedicoAction() {
            $this->_helper->layout->disableLayout();
            $usr_codigo = $this->_getParam("usr_codigo", false);
            $tbMes = new Application_Model_MedicoEspecialidade();
            $this->view->dados = $tbMes->getEspecialidadePorMedico($usr_codigo)->toArray();
            return $this->render("dados", NULL, TRUE);
	}
        
        public function formRelAtendimentoSimplificadoAction(){
            $this->view->title = "Relatório Atendimento por Procedimento";
                    
        } 
        
        public function relAtendimentoSimplificadoAction(){
            Zend_Layout::getMvcInstance()->setLayout("relatorio");
            $dataInicial = $this->_request->getPost("data_inicial",FALSE);
            $dataFinal = $this->_request->getPost("data_final",FALSE);
            $usuCodigo = $this->_request->getPost("usu_codigo",FALSE);
            $usrCodigo = $this->_request->getPost("usr_codigo",FALSE);
            $procCodigo = $this->_request->getPost("proc_codigo",FALSE);
            // Lista dados relatório
            $tbAte = new Application_Model_Atendimento();
            $procedimentos = $tbAte->relListaAtendimentosSimplificados($dataInicial,$dataFinal,$usuCodigo,$usrCodigo,$procCodigo)->toArray();
            $this->view->dados = $this->montaArrayDetalhes($dataInicial,$dataFinal,$usuCodigo,$usrCodigo,$procedimentos);
            // Dados Layout Padrão Relatório
            $tbUsr = new Application_Model_Usuarios();
            $array = array(
                'uni_desc'=> $tbUsr->getUsrAtual()->uni_desc,
                'set_nome' => $set_nome
            );
            if ($data_inicial)
                $array["data_inicial"] = $dataInicial;
            if ($data_final)
                $array["data_final"] = $dataFinal;
            $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        }
        
        public function montaArrayDetalhes($dataInicial,$dataFinal,$usuCodigo,$usrCodigo,$array_proc){
            $tbAte = new Application_Model_Atendimento();
            foreach($array_proc as $ind=>$val)
            {   
                $procedimentos = $tbAte->relListaAtendimentosSimplificadosDetalhes($dataInicial,$dataFinal,$usuCodigo,$usrCodigo,$val["proc_codigo"])->toArray();
                $array_proc[$ind]["dados"] = $procedimentos; 
            }
           //echo "<pre>".print_r($array_proc,1);die();
            return $array_proc;

        }
        
        public function formAgendamentoDemandaAction(){
            $this->view->title = "Relatório Agendamento x Demanda";
                    
        }
        
    public function relAgendamentoDemandaAction(){       
		$tbAge = new Application_Model_Agendamento();
		// Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts  
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		// Recebendo os dados do formulário 
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$nu_ine		= $this->_request->getPost("nu_ine", FALSE);
        $uni_codigo     = $this->_request->getPost("uni_codigo", FALSE);
		// Chamando o método 
//               echo "<pre>".print_r($tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total,1)."aaaaaa";die();
                
                $total = $tbAge->getTotalAgendamento($data_inicial, $data_final,$nu_ine,$uni_codigo)->total;
                $agenda = $tbAge->getTotalConsultaAgendada($data_inicial, $data_final,$nu_ine,$uni_codigo)->total;
                $demanda = $tbAge->getTotalDemanda($data_inicial, $data_final,$nu_ine,$uni_codigo)->total;
                //x = (valor1 * 100) / total
               
                $agendaPor = (((int)$agenda * 100) /(int)$total);               
                $demandaPor = (((int)$demanda * 100) /(int)$total) ;
                 
                $this->view->total = $total;
                $this->view->agenda = $agenda." (". number_format($agendaPor, 2, '.', '')."%)";
                $this->view->demanda = $demanda." (". number_format($demandaPor, 2, '.', '')."%)";
		
               
		$this->view->data_inicial	= $data_inicial;
		$this->view->data_final		= $data_final;
		$array	= array('data_inicial' => $data_inicial, 'data_final' => $data_final);
		
		$params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
		// Seta o título da página
		$this->view->title			= "Relatório de Demanda x Agenda";
        
    }
    public function formAtendimentoEncaminhamentoAction(){
            $this->view->title = "Relatório Atendimento x Encaminhamento";
                    
    }
    public function relAtendimentoEncaminhamentoAction(){       
		$tbAge = new Application_Model_Agendamento();
		// Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts  
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		// Recebendo os dados do formulário 
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$nu_ine		= $this->_request->getPost("nu_ine", FALSE);
		// Chamando o método 
//               echo "<pre>".print_r($tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total,1)."aaaaaa";die();
                
                $total = $tbAge->getTotalAgendamento($data_inicial, $data_final,$nu_ine)->total;
                $encaminhamento = $tbAge->getTotalEncaminhamento($data_inicial, $data_final,$nu_ine)->total;
                //x = (valor1 * 100) / total
               
                $encaminhamentoPor = (((int)$encaminhamento * 100) /(int)$total);               
                 
                $this->view->total = $total;
                $this->view->encaminhamento = $encaminhamento." (". number_format($encaminhamentoPor, 2, '.', '')."%)";
               
		$this->view->data_inicial	= $data_inicial;
		$this->view->data_final		= $data_final;
		$array	= array('data_inicial' => $data_inicial, 'data_final' => $data_final);
		
		$params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
		// Seta o título da página
		$this->view->title			= "Relatório de Encaminhamento";
        
    }
    
    public function formRelatorioRecemNascidoAction(){
        $this->view->title = 'Relatório - Relatório de Recém Nascidos';
    }
    
    public function relRelatorioRecemNascidoAction(){
        $tbINE = new Application_Model_TbEquipe();
        // Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        // Recebendo os dados do formulário
        $data_inicial	= $this->_request->getPost("data_inicial", FALSE);
        $data_final		= $this->_request->getPost("data_final", FALSE);
        $nu_ine		= $this->_request->getPost("nu_ine", FALSE);
        // Chamando o método
        //               echo "<pre>".print_r($tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total,1)."aaaaaa";die();
        
        $total = $tbINE->getTotalRecemNascidos($data_inicial, $data_final,$nu_ine)->total;
        
        $atendPrimeiraSemana = $tbINE->getTotalAtendPrimeiraSemanaRecemNascidos($data_inicial, $data_final,$nu_ine)->total;
        //$enfermeiros = $tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total;
        //x = (valor1 * 100) / total
        $atendPor = (((int)$atendPrimeiraSemana * 100) /(int)$total) ;
        //$enfermeirosPor = (((int)$enfermeiros * 100) /(int)$total) ;
        $atendPor = number_format($atendPor,2,",",".");
        $this->view->total = $total;
        $this->view->atendPrimeiraSemana = $atendPrimeiraSemana." ({$atendPor}%)";
        //$this->view->enfermeiros = $enfermeiros." ({$enfermeirosPor}%)";
        
        
        $this->view->data_inicial	= $data_inicial;
        $this->view->data_final		= $data_final;
        $array	= array('data_inicial' => $data_inicial, 'data_final' => $data_final);
        
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        // Seta o título da página
        $this->view->title			= "Relatório de Recém Nascidos";
        
    }

    public function formConsultaOdontoAction() {
        $this->view->title = "Primeira Consulta x Tratamento Concluido";
    } 
    
    public function relConsultaOdontoAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");

        $data_inicial	= $this->_request->getPost("data_inicial", FALSE);
        $data_final		= $this->_request->getPost("data_final", FALSE);
        $nu_ine		= $this->_request->getPost("nu_ine", FALSE);
        
        $tbTbEq = new Application_Model_TbEquipe();
        $tratConcluido = $tbTbEq->getTotalTratamentosConcluidosOdonto($data_inicial, $data_final, $nu_ine);

        //$tbOdoProc = new Application_Model_OdontoProcedimentos();
        //5450 -  PRIMEIRA CONSULTA ODONTOLOGICA PROGRAMÁTICA
        //$primConsulta = $tbOdoProc->getTotalTratamentosPorProcOdonto($data_inicial, $data_final, $nu_ine, 5450);
        $primConsulta = $tbTbEq->getTotalTratamentosAbertosOdonto($data_inicial, $data_final, $nu_ine);

        $this->view->tratConcluido = $tratConcluido->total;
        $this->view->primConsulta = $primConsulta->total;
        $total = $tratConcluido->total + $primConsulta->total;
        $this->view->tratConcluidoPerc = (100*$tratConcluido->total)/$total;
        $this->view->primConsultaPerc = (100*$primConsulta->total)/$total;
        $this->view->title	= "Relatório Odontológico de Primeira Consulta x Tratamento Concluido ";
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;

        //die(var_dump($primConsulta->total));
    }


    public function formProcedimentoPorMesPorProfissionalAction(){
        $this->view->title = 'Relatório Procedimentos por mês por Profissional';
    }
    
    // public function relProcedimentoPorMesPorProfissionalAction(){
    //     Zend_Layout::getMvcInstance()->setLayout("relatorio");
    //     // Recebendo os dados do formulário
    //     $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
    //     $data_inicial   = $this->_request->getPost("data_inicial", FALSE);
    //     $data_final     = $this->_request->getPost("data_final", FALSE);
    //     $proc_codigo = $this->_request->getPost("proc_codigo",FALSE);
    //     $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);
    //     $tbAte = new Application_Model_Atendimento();
    //     $tbUsr = new Application_Model_Usuarios();
    //     //die(var_dump("here"));
    //     if($usr_codigo != FALSE){
    //         $profs[0]=$tbUsr->getNomeProfissional($usr_codigo);
    //         $sql[0] = $tbAte->getTotalProcedimentoPorMesPorProfissional($usr_codigo,$data_inicial, $data_final, $proc_codigo,$uni_codigo);
    //     } else {
    //         $profs=$tbAte->getProfissionaisProcedimentoPorMes($data_inicial, $data_final, $proc_codigo,$uni_codigo);
    //         foreach ($profs as $key => $prof) {
    //             //die(var_dump($prof->usr_codigo));
    //             $sql[$prof->usr_codigo] = $tbAte->getTotalProcedimentoPorMesPorProfissional($prof->usr_codigo,$data_inicial, $data_final, $proc_codigo,$uni_codigo);
    //         }
    //     }
    //     //die(var_dump($sql));
    //     $this->view->sql = $sql;
    //     $this->view->profs = $profs;
        
    //     $params = array($data_inicial = $data_inicial,
    //                         $data_final = $data_final,
    //                         $uni_nome = $uni_desc
    //                         );
    //     $this->view->params = $params;
    //     // Seta o título da página
    //     $this->view->title = 'Relatório Procedimentos por mês por Profissional';
        
    // }

      
    public function relProcedimentoPorMesPorProfissionalAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        // Recebendo os dados do formulário
        $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
        $data_inicial   = $this->_request->getPost("data_inicial", FALSE);
        $data_final     = $this->_request->getPost("data_final", FALSE);
        $proc_codigo = $this->_request->getPost("proc_codigo",FALSE);
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
		if($usr_codigo != FALSE){
		   $profs = $tbUsr->getNomeProfissional($usr_codigo);
		}
		$sql = $tbAte->getTotalProcedimentoPorMesPorProfissional($usr_codigo,$data_inicial, $data_final, $proc_codigo);
		 $params = array($data_inicial = $data_inicial,
									 $data_final = $data_final,
									 $uni_nome = $uni_desc
									 );      
		$this->view->params = $params;
    	
//       die(var_dump($sql));
        $this->view->title = 'Relatório Procedimentos por mês por Profissional';
       $this->view->profs = $profs;
		$this->view->sql = $sql;

    }

    public function formVisitaParaInternamentoAction(){
         $this->view->title = "Visita Para Internamento";
    }

    public function relVisitaParaInternamentoAction(){
        //die(var_dump("here"));
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
        $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
        $usr_codigo = $this->_request->getPost("usr_codigo",FALSE);
        //$nu_ine = $this->_request->getPost("nu_ine",FALSE);
        $select_relatorio = $this->_request->getPost("select_relatorio");
        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        
        $this->view->select = $select_relatorio;

        if ($select_relatorio == 0){
            $this->view->title = "Visita Para Internamento Analítico";
            if($usr_codigo != null && $usr_codigo != ""){
                $profs[0]=$tbUsr->getNomeProfissional($usr_codigo);
                $dados[0]=$tbAte->getVisitaInternamento($usr_codigo, $dataInicial, $dataFinal);
                $this->view->profs = $profs;
                $this->view->dados = $dados;
            } else {
                //die(var_dump("here"));
                $profs = $tbAte->getProfissionaisVisitaInternamento($dataInicial, $dataFinal);
                //die(var_dump($profs));
                foreach($profs as $prof){
                     //die(var_dump($prof));
                    $dados[$profs->usr_codigo] = $tbAte->getVisitaInternamento($usr_codigo, $dataInicial, $dataFinal);
                }
                $this->view->profs = $profs;
                $this->view->dados = $dados;
            }
        }else{
            $this->view->title = "Visita Para Internamento Sintético";
            if($usr_codigo != null && $usr_codigo != ""){
                $profs[0]=$tbUsr->getNomeProfissional($usr_codigo);
                $dados[0]=$tbAte->getVisitaInternamentoQtde($usr_codigo, $dataInicial, $dataFinal);
                $this->view->profs = $profs;
                $this->view->dados = $dados;
            } else {
                $profs = $tbAte->getProfissionaisVisitaInternamento($dataInicial, $dataFinal);
                foreach($profs as $prof){
                   
                    $dados[$profs->usr_codigo] = $tbAte->getVisitaInternamentoQtde($usr_codigo, $dataInicial, $dataFinal);
                }
                $this->view->profs = $profs;
                $this->view->dados = $dados;
            }
        }

    }

    public function formEstratificacaoRiscoAction(){
         $this->view->title = "Estratificação de Risco";
    }

    public function relEstratificacaoRiscoAction(){
        //die(var_dump("here"));
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
        $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
        $usr_codigo = $this->_request->getPost("usr_codigo",FALSE);
        $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);
        $uni_desc = $this->_request->getPost("uni_desc",FALSE);
        

        $select_relatorio = $this->_request->getPost("select_relatorio");
        $select_grupo = $this->_request->getPost("select_grupo");
        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        
        $this->view->select_relatorio = $select_relatorio;
        $this->view->select_grupo = $select_grupo;



        if ($select_grupo == 0){
            $this->view->title = "Estratificação de Risco de Todos os Grupos";
            // metodo que faz select de tds os grupos
        }

        if ($select_grupo == 1){
            $this->view->title = "Estratificação de Risco Grupo 1";
            // metodo que faz select do grupo 1 
        }

        if ($select_grupo == 2){
            $this->view->title = "Estratificação de Risco Grupo 2";
            // metodo que faz select do grupo 2         
        } else {
            $this->view->title = "Estratificação de Pessoas pertencentes aos dois Grupos";
        }


        //die(var_dump("here"));      
        if($usr_codigo != null && $usr_codigo != ""){
            $profs[0]=$tbUsr->getNomeProfissional($usr_codigo);
            $pacientes = $tbAte->getPacientesComEstratificacaoRisco($dataInicial, $dataFinal, $uni_codigo, $select_grupo, $usr_codigo);
        } else {
            $profs = $tbAte->getProfissionaisEstratificacaoRisco($dataInicial, $dataFinal, $uni_codigo, $select_grupo);
            $pacientes = $tbAte->getPacientesComEstratificacaoRisco($dataInicial, $dataFinal, $uni_codigo, $select_grupo);
            //die(var_dump($pacientes));
        }
        if ($select_relatorio == 0){
            foreach ($pacientes as $key => $paciente) {
                $dadosG1[$paciente->usu_codigo] = $tbAte->getDadosEstratificacaoRiscoG1($dataInicial, $dataFinal, $uni_codigo, $select_grupo, $paciente->usu_codigo);
            }
            foreach ($pacientes as $key => $paciente) {
                $dadosG2[$paciente->usu_codigo] = $tbAte->getDadosEstratificacaoRiscoG2($dataInicial, $dataFinal, $uni_codigo, $select_grupo, $paciente->usu_codigo);
            }     
        }
        //die(var_dump($dadosG2));
        $this->view->select_grupo = $select_grupo;
        $this->view->profs = $profs;
        $this->view->dadosG1 = $dadosG1;
        $this->view->dadosG2 = $dadosG2;
        $this->view->pacientes = $pacientes;
    }
}