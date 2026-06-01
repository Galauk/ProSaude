<?php

class Relatorio_TransporteController extends Elotech_Controller_Action_Relatorio {

	public function formVeiculoAction() {
		/* Considerações
		 * A paginá HTML que está localizada na pasta relatórios/script/view/transporte/
		 * tem que possuir o mesmo nome da chamada do form por ex: form-veiculo
		 * incluindo sempre o action na frente
		 */
		// Chamando o model de veículos
		$tbVei = new Application_Model_Veiculo();
		// Encaminha um array com todos os veículos cadastrados
		$this->view->veiculo = $tbVei->getVeiculos();
		// Título da view
		$this->view->title = "Viagem por veículo";
		// Chamando a view de form-veiculo
		$this->render("form-veiculo");
	}

	public function veiculoAction() {
		// Chamando o model de viagem
		$tbVia = new Application_Model_Viagem();
		// Chamando o Layout padrão de relatórios, que está na pasta layout/default/scripts  
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		// Recebendo os dados do formulário 
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$vei_codigo		= $this->_request->getPost("vei_codigo", FALSE);
		// Chamando o método de viagem por veículo
		$this->view->dados = $tbVia->getViagemPorVeiculo($vei_codigo, $data_inicial, $data_final);
		// Não sei o que faz
		$this->view->uni_cnes = $uni_cnes;
		$this->view->data_inicial	= $data_inicial;
		$this->view->data_final		= $data_final;
		$array	= array('data_inicial' => $data_inicial, 'data_final' => $data_final);
		// Não sei o que faz
		$this->view->params			= serialize($array);
		// Seta o título da página
		$this->view->title			= "Relatório de Viagem por Veículo";
	}
	
	public function formMotoristaAction() {
		$tbVia = new Application_Model_Viagem();
		$this->view->motorista = $tbVia->getViagemPorMotorista();

		$this->view->title = "Viagem por Motorista";
		$this->render("form-motorista");
	}

	public function motoristaAction() {
		$tbVia = new Application_Model_Viagem();
		//echo "<pre>".print_r($_POST,1);die();
		Zend_Layout::getMvcInstance()->setLayout("relatorio");

		$data_inicial = $this->_request->getPost("data_inicial", FALSE);
		$data_final = $this->_request->getPost("data_final", FALSE);
		$usr_codigo = $this->_request->getPost("usr_codigo", FALSE);

		//echo "<pre>".print_r($tipo_consulta,1);die();

		$this->view->dados = $tbVia->getViagemPorMotorista($usr_codigo, $data_inicial, $data_final);



		$this->view->data_inicial = $data_inicial;
		$this->view->data_final = $data_final;
		$array = array('data_inicial' => $data_inicial,
			'data_final' => $data_final);
		$this->view->params = serialize($array);
		$this->view->title = "Relatório de Viagem por Motorista";
	}

	public function formEncaminhamentosAction() {
		// Chamando o model de veículos
		$tbVei = new Application_Model_Veiculo();
		// Chamando o método que retorna todos os veículos cadastrados
		$this->view->veiculo = $tbVei->getVeiculos();
		// Setando o título da página
		$this->view->title = "Encaminhamento de Veículos por Viagens";
		// Chama a página e faz o redirecionamento
		$this->render("form-encaminhamentos");
	}
	
	public function encaminhamentosAction(){
		//  Chamando o model de viagens e Veículos
		$tbVia = new Application_Model_Viagem();
		$tbVei = new Application_Model_Veiculo();
		// Setando o layout de relatório
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		// Recebe as variáveis via post
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$vei_codigo		= $this->_request->getPost("vei_codigo", FALSE);
		// Se código do veículo for diferente de vázio, busca o nome do carro
		if ($vei_codigo) { 
			$vei_descricao	= $tbVei->getVeiculo($vei_codigo); 
		}
		// Array de informações
		$array = array('data_inicial'=>$data_inicial,'data_final'=>$data_final,'titulo'=>'Veículo','dados'=>$vei_descricao->vei_descricao);
		// Params é referente parametro nada do zend, serialize uma forma segura
		$this->view->params = serialize($array);
		// Array de Encaminhamentos pras viagens
		$arrayEncViag = $tbVia->getViagemPorEncaminhamento($data_inicial,$data_final,$vei_codigo)->toArray();
		// Percorrendo os encaminhamentos
		$i=0;
		foreach ($arrayEncViag as $encViag) {
			// Trazendo os acomnpanhantes de acordo com os encaminhamentos
			$arrayEncViag[$i][acompanhantes] = $tbVia->getAcompanhantesViagem($encViag["viausu_codigo"])->toArray();
			$i++;
		}
		// Encaminhando array pra view
		$this->view->dados = $arrayEncViag;
		// Setando um título pra página
		$this->view->title = "Relatório de Encaminhamentos";
	}

	public function formCustoViagemAction(){
		$tbVia = new Application_Model_Viagem();
		$this->view->motorista = $tbVia->getViagemPorMotorista();
		// Chamando o model de veículos
		$tbVei = new Application_Model_Veiculo();
		// Chamando o método que retorna todos os veículos cadastrados
		$this->view->veiculo = $tbVei->getVeiculos();
		$this->view->title = "Relatório de Custo de Viagem";
	}

	public function relCustoViagemAction(){
		Zend_Layout::getMvcInstance()->setLayout("relatorio");
		$data_inicial	= $this->_request->getPost("data_inicial", FALSE);
		$data_final		= $this->_request->getPost("data_final", FALSE);
		$vei_codigo		= $this->_request->getPost("vei_codigo", FALSE);
		$usr_codigo		= $this->_request->getPost("usr_codigo", FALSE);
		$cid_codigo		= $this->_request->getPost("cid_codigo", FALSE);
		//die(var_dump($this->_request->getPost("cid_codigo", FALSE)));
		$tbVpu = new Application_Model_ViagemProcedimentoUsuario();
		$dados = $tbVpu->getProcedimentosPorViagem($data_inicial, $data_final, $vei_codigo, $usr_codigo, $cid_codigo);
		$this->view->dados = $dados;
	}

}

