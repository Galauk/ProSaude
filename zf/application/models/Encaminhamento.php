<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Encaminhamento extends Elotech_Db_Table_Abstract {

	protected $_name = 'encaminhamento';
	protected $_primary = 'enc_codigo';
	protected $_referenceMap = array(
		'Especialidade' => array(
			'columns' => 'esp_codigo',
			'refTableClass' => 'Application_Model_Especialidade',
			'refColumns' => 'esp_codigo'
		)
	);

	public function salvar(array $data,$obs=FALSE) {
		$tbAte = new Application_Model_Atendimento();
		$tbUsr = new Application_Model_Usuarios();
		$ate = $tbAte->temAtendimentoMedico();
		//die("ons".$obs);
		if($obs != "S"){
			if(is_null($data['ate_codigo']) || empty($data['ate_codigo']))
				$data['ate_codigo'] = $ate->ate_codigo;

			if(is_null($data['med_codigo']) || empty($data['med_codigo']))
				$data['med_codigo'] = $ate->med_codigo;

			if(is_null($data['usr_codigo']) || empty($data['usr_codigo']))
				$data['usr_codigo'] = $ate->med_codigo;
		
		}else{
			if(is_null($data['med_codigo']) || empty($data['med_codigo']))			
				$data['med_codigo'] = $tbUsr->getUsrAtual()->usr_codigo;
		
			if(is_null($data['usr_codigo']) || empty($data['usr_codigo']))
				$data['usr_codigo'] = $tbUsr->getUsrAtual()->usr_codigo;
		}
		if(is_null($data['enc_data']) || empty($data['enc_data']))
			$data['enc_data'] = date('Y-m-d');
		
		//echo "<pre>".print_r($data,1);exit;
		return parent::salvar($data);
	}

	public function getItens() {
		$tbAte = new Application_Model_Atendimento();
		$ate = $tbAte->temAtendimentoMedico();
		
		return $this->getHistorico($ate->ate_codigo);
	}

	public function getHistoricoItens($selecionados) {
//		die($selecionados);
		$e = explode(",",$selecionados);
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("e" => "encaminhamento"), array("enc_codigo","enc_descricao","enc_internacao","enc_urgencia"))
				->join(array("esp" => "especialidade"), "esp.esp_codigo=e.esp_codigo", "esp_nome");
	for($i=0;$i<=count($e);$i++) {
			(!empty($e[$i]))?$where->orWhere("enc_codigo=?", $e[$i]):"";
	}				
		$where->order("esp_nome");
		//die($where);
		return $this->fetchAll($where);
	}

	public function getHistorico($ate_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("e" => "encaminhamento"), array("enc_codigo","enc_descricao","enc_internacao","enc_urgencia"))
				->join(array("esp" => "especialidade"), "esp.esp_codigo=e.esp_codigo", "esp_nome")
				->where("ate_codigo=?", $ate_codigo)
				->order("esp_nome");
		return $this->fetchAll($where);
	}
        public function getItensInternacao($io_codigo) {		
			return $this->getHistoricoInternacao($io_codigo);
	}
	public function getHistoricoInternacao($io_codigo){
		$where = $this->select(FALSE)
				 ->setIntegrityCheck(FALSE)
				 ->from(array("e"=>"encaminhamento"),array("enc_codigo","enc_descricao","enc_internacao","enc_urgencia"))
				 ->join(array("esp" =>"especialidade"),"esp.esp_codigo=e.esp_codigo", "esp_nome")
				 ->join(array("ate"=>"atendimento"),"e.ate_codigo=ate.ate_codigo","")
				 ->join(array("ati" => "atendimento_internacao"),"ate.ate_codigo = ati.ate_codigo")
				 ->where("io_codigo =?",$io_codigo)
				 ->order("enc_data");		
		return $this->fetchAll($where);
	}
	
	/**
	 * Exclui um encaminhamento
	 * O método verifica se faz parte do atendimento atual
	 * @param int $enc_codigo 
	 */
	public function excluir($enc_codigo){
                //die($enc_codigo."-".$ate_codigo);
		$tbAte = new Application_Model_Atendimento();
		$ate = $tbAte->temAtendimentoMedico();
		
		$where = "enc_codigo=$enc_codigo";
		
		$item = $this->fetchRow($where);
		if($item)
			$item->delete ();
		
		return true;
	}

	public function imprimir($enc_codigo,$io_codigo=FALSE,$usu_codigo=FALSE) {
               // die($io_codigo."-".$usu_codigo."sdad");
		$enc = $this->find($enc_codigo)->current();
		//$dados = $enc->toArray();
		//$dados->esp_nome = $enc->findParentRow("Application_Model_Especialidade")->esp_nome;
		$dados->enc_descricao = $enc->enc_descricao;
		$dados->codigo = $enc->enc_codigo;
		$age = Application_Model_Agendamento::usuEmAberto();

		// dados do paciente
		$tbUsu = new Application_Model_Usuario();
                if($io_codigo){
                    $usu = $tbUsu->find($usu_codigo)->current();
                }else{
                    $usu = $tbUsu->find($age->usu_codigo)->current();
                }
		
		
		$tbDom = new Application_Model_Domicilio();
                $dom_dados = $tbDom->getEnderecoPorUsuario($usu->usu_codigo);
                $dados->rua_nome = $dom_dados->rua_nome;
                $dados->dom_numero = $dom_dados->dom_numero;
                
                $dados->usu_sexo = $usu->usu_sexo;
		$dados->usu_nome = $usu->usu_nome;
		$dados->usu_datanasc = $usu->usu_datanasc;
		$dados->genero = ($usu->usu_sexo=='M')?'o':'a';
                $dados->usu_cartao_sus = $usu->usu_cartao_sus;
                $dados->usu_prontuario = $usu->usu_prontuario;
                $dados->idade = $usu->usu_datanasc;
				$dados->usu_mae = $usu->usu_mae;
				$dados->usu_cpf = $usu->usu_cpf;
				$dados->rac_codigo = $usu->rac_codigo;
		
		// dados do médico
		$tbUsr = new Application_Model_Usuarios();
                $tbUsr = new Application_Model_Usuarios();
                $usr = $tbUsr->getUsrAtual();
                $usr_codigo = $usr->usr_codigo;                    

		$dados->usr_nome = $usr->usr_nome;
		$dados->usr_num_conselho = $usr->usr_num_conselho;
                $dados->cnes_sigla_est = $usr->cnes_sigla_est;
                $dados->con_descricao = $usr->con_descricao;
		
		// dados da unidade
		$tbUni = new Application_Model_Unidade();
                if($io_codigo){
                   $tbUsr = new Application_Model_Usuarios();
                   $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
                 
                   $log = new Application_Model_Logon();
                   $uni_codigo = $log->getDadosPeloUsuario($usr_codigo)->current();                 
                
                   $uni = $tbUni->buscarCidadeDaUnidade($uni_codigo->uni_codigo)->current();
                         
                }else{
                    $uni = $tbUni->buscarCidadeDaUnidade($age->uni_codigo)->current();
                }
		
                
        $dados->nome_cidade = $uni->cid_nome;		
		$dados->uni_desc = $uni->uni_desc;
		$dados->uni_endereco = $uni->uni_endereco;
		
		// dados da secretaria
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$dados->secretaria = $sec->nome_secretaria;
		
		return $dados;
	}
        
       

}
