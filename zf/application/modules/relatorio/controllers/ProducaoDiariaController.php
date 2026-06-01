<?php

class Relatorio_ProducaoDiariaController extends Elotech_Controller_Action_Relatorio {

	public function indexAction(){

            $rel = $this->_getParam("tipo",false);
            $this->view->title = "Produção Diária $rel";
            $this->render("index");

	}

        public function producaoDiariaAction(){
                Zend_Layout::getMvcInstance()->setLayout("relatorio");

		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);
        $usr_codigo = $this->_request->getPost("usr_codigo", FALSE);
        $cd10_codigo = $this->_request->getPost("cd10_codigo",FALSE);
        $tipo_consulta = $this->_request->getPost("tipo_consulta",FALSE);
        //die(var_dump($tipo_consulta));
        //echo "<pre>".print_r($tipo_consulta,1);die();
        $tbAte = new Application_Model_Atendimento();
        $dados_consulta = $tbAte->producaoDiariaConsulta($usr_codigo,$data_inicial,$data_final,$cd10_codigo,$tipo_consulta);
        $dados_preconsulta = $tbAte->producaoDiariaPreConsulta($usr_codigo,$data_inicial,$data_final,$cd10_codigo,$tipo_consulta);
        $this->view->dados1 =  $dados_consulta;
        $this->view->dados2 =  $dados_preconsulta;

                $this->view->data_inicial = $data_inicial;
        $this->view->data_final = $data_final;
        $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        $this->view->title = "Produção Diária";

	}

}

