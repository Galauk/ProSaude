<?php

class Materiais_RelatorioController extends Zend_Controller_Action {

	
	public function init() {
            set_time_limit(10000000000000000000);
            ini_set('max_execution_time', 900);
	}
	public function indexAction() {
	// action body
	}
        
        public function balancoProdutoSetorAction(){
            $this->view->title = "Balanço Completo de Medicamentos";
            $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio/materiais/materiais.css',"all");
            
            $tbSec = new Application_Model_Secretaria();
            $this->view->sec = $tbSec->getDadosSec()->toArray();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
           
            
            
            Zend_Layout::getMvcInstance()->setLayout("simples");
            
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $psi = $this->_request->getPost("psi", FALSE);

            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $portarias = $this->_request->getPost("psico_codigo", FALSE);
            $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);
            $this->view->periodo_meses = $this->verificaQuantosMesesAction($data_inicial, $data_final);

            $arr_final = explode('/',$data_final);
            $this->view->ano = $arr_final[2];
            $this->view->periodo_rel = array("ini"=>$data_inicial,"fim"=>$data_final);
            //die(var_dump($portarias));
            $tbPro = new Application_Model_Produto();
            $this->view->dados = $tbPro->relBalanco($set_codigo, $data_inicial, $data_final,$psi,$portarias);
            $this->view->dados_aquisicao = $tbPro->getEntrada($set_codigo, $data_inicial, $data_final,$psi,$portarias);
            //die("aaa");

            // $tbMov = new Application_Model_Movimento();
            // $this->view->dados_aquisicao = $tbMov->getEntradas($set_codigo, $data_inicial, $data_final,$psi,$portaria)->toArray();
        }

        public function saidaProdutoSetorAction(){
            $this->view->title = "Balanço Completo de Medicamentos";
            $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio/materiais/materiais.css',"all");
            
            $tbSec = new Application_Model_Secretaria();
            $this->view->sec = $tbSec->getDadosSec()->toArray();
            $tbUsr = new Application_Model_Usuarios();
            $this->view->usr = $tbUsr->getUsrAtual();
           
            
            
            Zend_Layout::getMvcInstance()->setLayout("simples");
            
            $set_codigo = $this->_request->getPost("set_codigo", FALSE);
            $psi = $this->_request->getPost("psi", FALSE);

            $data_inicial = $this->_request->getPost("data_inicial", FALSE);
            $data_final = $this->_request->getPost("data_final", FALSE);
            $portarias = $this->_request->getPost("psico_codigo", FALSE);
            $portaria = "";
                foreach ($portarias as $p){
                    $portaria .= $p.",";
                }
                $portaria = substr($portaria,0,-1);
            $this->view->periodo_meses = $this->verificaQuantosMesesAction($data_inicial, $data_final);

            $arr_final = explode('/',$data_final);
            $this->view->ano = $arr_final[2];
            $this->view->periodo_rel = array("ini"=>$data_inicial,"fim"=>$data_final);
            //die(var_dump($portarias));
            $tbPro = new Application_Model_Produto();
            $this->view->dados_saida = $tbPro->getSaida($set_codigo, $data_inicial, $data_final,$psi,$portarias);
            //die("aaa");

            // $tbMov = new Application_Model_Movimento();
            // $this->view->dados_aquisicao = $tbMov->getEntradas($set_codigo, $data_inicial, $data_final,$psi,$portaria)->toArray();
        }
        
        
        
        public function verificaQuantosMesesAction($dt_inicial=FALSE,$dt_final=FALSE){
            $data1 = $dt_inicial; 
            $arr = explode('/',$data1); 

            $data2 = $dt_final; 
            $arr2 = explode('/',$data2); 

            $dia1 = $arr[0]; 
            $mes1 = $arr[1]; 
            $ano1 = $arr[2]; 

            $dia2 = $arr2[0]; 
            $mes2 = $arr2[1]; 
            $ano2 = $arr2[2]; 

            $a1 = ($ano2 - $ano1)*12;
            $m1 = ($mes2 - $mes1)+1;
            $m3 = ($m1 + $a1);
            return $m3;
        }

}

