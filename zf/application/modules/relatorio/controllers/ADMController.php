<?php

class Relatorio_AdmController extends Elotech_Controller_Action_Relatorio {
private $tbLog;

	public function init() {
		$this->view->title = "Administracao";

		$this->tbLog = new Application_Model_Log();
	}

	public function indexAction() {

	}

	public function formAcessoPorUsuariosAction() {
        $this->view->title = "Acesso Por Usuário";
    }

    public function relAcessoPorUsuariosAction() { 
        $this->view->title = "Acesso Por Usuário"; 
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);

        
            
            $where = $this->tbLog->relAcessoPorUsuario($usr_codigo, $data_inicial, $data_final);
            $this->view->where = $where;
        if ($data_inicial){
            $array["data_inicial"] = $data_inicial;
        }

            if ($data_final){
                $array["data_final"] = $data_final;
            }
            
        $this->view->usr_codigo     = $usr_codigo;
        $this->view->data_inicial   = $data_inicial;
        $this->view->data_final     = $data_final;
        $array  = array('data_inicial' => $data_inicial, 'data_final' => $data_final);
        
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
    
    }





    public function formProducaoPorProfissionalAction() {
        $this->view->title = "Produção por Profissional";
    }

    public function relProducaoPorProfissionalAction() { 
        $this->view->title = "Produção por Profissional"; 
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        $proc_codigo = $this->_request->getPost("proc_codigo", FALSE);

        if ($data_inicial){
            $array["data_inicial"] = $data_inicial;
        }

            if ($data_final){
                $array["data_final"] = $data_final;
            }
            
        $this->view->usr_codigo     = $usr_codigo;
        $this->view->data_inicial   = $data_inicial;
        $this->view->data_final     = $data_final;
        $array  = array('data_inicial' => $data_inicial, 'data_final' => $data_final);
        
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        
        if($usr_codigo != FALSE){
            $prof[0] = $tbUsr->getNomeProfissional($usr_codigo);
            $this->view->profs = $prof;
            $dados[0] = $tbAte->getProducaoProfissional($data_inicial, $data_final, $proc_codigo, $usr_codigo);
            $this->view->dados = $dados;
        } else {
            $prof = $tbAte->getProfissionalComProducaoPorPeriodo($data_inicial, $data_final, $proc_codigo);
            $this->view->profs = $prof;
            foreach ($prof as $key => $p) {
                $dados[$p->med_codigo] = $tbAte->getProducaoProfissional($data_inicial, $data_final, $proc_codigo, $p->med_codigo);
            }
            
            $this->view->dados = $dados;
        }
    
    }
    
    
}
?>