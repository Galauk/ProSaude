<?php

class Relatorio_EsfController extends Elotech_Controller_Action_Relatorio {
    public function formRelatorioMediaAtendimentosAction(){
        $this->view->title = 'Relatório - Média de atendimentos';
        
    }
    
    public function relRelatorioMediaAtendimentosAction(){       
		$tbINE = new Application_Model_TbEquipe();
		// Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts  
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		// Recebendo os dados do formulário 
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$nu_ine		= $this->_request->getPost("nu_ine", FALSE);
		// Chamando o método 
//               echo "<pre>".print_r($tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total,1)."aaaaaa";die();
                
                $total = $tbINE->getTotalAteMedEnf($data_inicial, $data_final,$nu_ine)->total;
                $medicos = $tbINE->getTotalAteMedicos($data_inicial, $data_final,$nu_ine)->total;
                $enfermeiros = $tbINE->getTotalAteEnfermeiros($data_inicial, $data_final,$nu_ine)->total;
                //x = (valor1 * 100) / total
                $medicosPor = (((int)$medicos * 100) /(int)$total) ;
                $enfermeirosPor = (((int)$enfermeiros * 100) /(int)$total) ;
               
                $this->view->total = $total;
                $this->view->medicos = $medicos." ({$medicosPor}%)";
                $this->view->enfermeiros = $enfermeiros." ({$enfermeirosPor}%)";
		
               
		$this->view->data_inicial	= $data_inicial;
		$this->view->data_final		= $data_final;
		$array	= array('data_inicial' => $data_inicial, 'data_final' => $data_final);
		
		$params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
		// Seta o título da página
		$this->view->title			= "Relatório de Média de atendimentos";
        
    }
    
    public function formFamiliaPorIneAction(){
        $this->view->title = 'Relatório - Família por Ine';
        
    }
     public function relFamiliaPorIneAction(){
        $this->view->title = 'Relatório - Família por Ine';

        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        // Recebendo os dados do formulário 
        $uni_codigo   = $this->_request->getPost("uni_codigo", FALSE);
        $usr_codigo     = $this->_request->getPost("usr_codigo", FALSE);
        $nu_ine     = $this->_request->getPost("nu_ine", FALSE);   

    


    }
    
    
}
?>