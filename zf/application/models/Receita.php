<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Receita extends Elotech_Db_Table_Abstract {

	protected $_name = 'receita';
	protected $_primary = 'rec_codigo';
	protected $_sequence = 'seq_rec_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data,$obs=FALSE) {
		//echo "<pre>".print_r($obs,1);exit;
                
		if (!in_array($data['rec_tipo'], array("posto", "controlados", "externo","ficha"))) {
            throw new Zend_Validate_Exception("Tipo de receita desconhecido", 1008);
        }

		// #TODO: verificar data
		// Só pode haver uma receita de cada tipo (posto, controlados, externo)
		// para cada atendimento
		$rec = $this->temReceita($data['rec_tipo']);

		if ($rec && $rec->rec_codigo) {
            $data['rec_codigo'] = $rec->rec_codigo;
        }
		// Se for uma receita nova, e sem ate_codigo
		if (!$data['ate_codigo'] && !$data['rec_codigo']) {

			$tbAte = new Application_Model_Atendimento();
			$ate = $tbAte->temAtendimentoMedico();
			if (!$ate) {
				throw new Zend_Validate_Exception("Atendimento não encontrado!", 1009);
            }

			$data['ate_codigo'] = $ate->ate_codigo;
		}

		if (is_null($data['rec_data'])) {
			$data['rec_data'] = date("Y-m-d");
        }

		if (is_null($data['rec_finalizada'])) {
            $data['rec_finalizada'] = 'N';
        }
                
        if (empty($data['rec_validade'])){                  
            $tbConfig = new Application_Model_Configuracao();
            $tempo = $tbConfig->getConfig("VALIDADE_RECEITA");
            //$validade = date("d/m/Y",time()+3600*24*$tempo) ;
            $validade = date('d/m/Y', strtotime("+$tempo days"));

            $data["rec_validade"] = $validade;                   
        }
              
		$this->peloMenosUm(array("ate_codigo"), $data);
		return parent::salvar($data);
	}

	public function temReceita($tipo=NULL) {
		$tbAte = new Application_Model_Atendimento();
		$ate = $tbAte->temAtendimentoMedico();
		if (!$ate) {
            return FALSE;
        }
                
		if($tipo) {
			$tipoReceita = "rec_tipo='$tipo' AND ";
        }
        
        //echo"<pre>".print_r($ate,1);
        // echo '<br/>' ."$tipoReceita ate_codigo=" . $ate->ate_codigo;
		return $this->fetchRow("$tipoReceita ate_codigo=" . $ate->ate_codigo);
	}

	public function imprimir($tipo, $io_codigo = FALSE, $usu_codigo = FALSE, $selecionados = FALSE) {
		$dados = new stdClass();
        $rec = $this->temReceita($tipo);
		$dados->codigo = $rec->rec_codigo;
		$age = Application_Model_Agendamento::usuEmAberto();
                
		// Itens da receita
		$tbIRec = new Application_Model_ReceitaItens();
        // if para ver se é do prontuário ou do atendimento da UPA
        if($io_codigo) {
            $dados->itens = $tbIRec->getItensInternacao($io_codigo);
        } else {
            $dados->itens = $tbIRec->getItens($tipo, $dados->codigo, $selecionados);
        }
                
		// dados do paciente
		$tbUsu = new Application_Model_Usuario();
        if($io_codigo) {
            // $usu = $tbUsu->find($usu_codigo)->current();
            $usu = $tbUsu->getInfo($usu_codigo);
        } else {
            $usu = $tbUsu->getInfo($age->usu_codigo);
        }

        $dados->rua_nome = $usu->rua_nome;
        $dados->rua_bairro = $usu->rua_bairro;
        $dados->dom_numero = $usu->dom_numero;
        $dados->dom_codigo = $usu->dom_codigo;
		$dados->usu_nome = $usu->usu_nome;
        $dados->usu_prontuario = $usu->usu_prontuario;
        $dados->cid_nome = $usu->cid_nome;
        $dados->usu_sexo = $usu->usu_sexo;
        $dados->idade = $usu->usu_datanasc;
        $dados->usu_cartao_sus = $usu->usu_cartao_sus;
        $dados->usu_datanasc = $usu->usu_datanasc;
		$tbUsr = new Application_Model_Usuarios();
        
        if($io_codigo) {
            $tbUsr = new Application_Model_Usuarios();
            $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;

            $usr = $tbUsr->find($usr_codigo);     
        } else {
            $usr = $tbUsr->find($age->med_codigo);
        }
		
		$dados->usr_nome = $usr[0]->usr_nome;
		$dados->usr_num_conselho = $usr[0]->usr_num_conselho;
		//echo "<pre>".print_r($dados,1);die();
                
		// dados da unidade
		$tbUni = new Application_Model_Unidade();
        // if para ver se é do prontuário ou do atendimento da UPA
        
        if($io_codigo) {
            $tbUsr = new Application_Model_Usuarios();
            $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
            
            $log = new Application_Model_Logon();
            $uni_codigo = $log->getDadosPeloUsuario($usr_codigo);                 
            $uni = $tbUni->buscarCidadeDaUnidade($uni_codigo->uni_codigo);
        } else {
            $uni = $tbUni->buscarCidadeDaUnidade($age->uni_codigo);
        }
		
		$dados->uni_desc = $uni->uni_desc;
		$dados->nome_cidade = $uni->cid_nome;
		$dados->uni_endereco = $uni->uni_endereco;
        //echo "<pre>".print_r($dados,1);die();
		
		// dados da secretaria
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$dados->secretaria = $sec->nome_secretaria;
		//echo "<pre>".print_r($dados,1);exit;
		return $dados;
	}
        
    public function getReceita($tipo){
        $rec = $this->temReceita($tipo);
            
        $codigo = $rec->rec_codigo;
    }
        
    public function getReceitaPorCodigo($rec_codigo) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rec"=>"receita"))
                ->join(array("ate"=>"atendimento"),"ate.ate_codigo=rec.ate_codigo",array("usu_codigo"))
                ->join(array("usu"=>"usuario"),"usu.usu_codigo=ate.usu_codigo","usu_nome")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=ate.med_codigo",array("usr_nome","usr_codigo"))
                ->where("rec_codigo=$rec_codigo")
                ->where("rec_finalizada<>'S'")
                ->where("rec_tipo <> 'externo'");
        //die($where);
        return $this->fetchRow($where);
    }
        
    public function alteraStatus($data) {
        $where = $this->select()->where("rec_codigo =?", $data[rec_codigo])->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        unset($data["rec_codigo"]);
        return $this->update($data, $where);
    }
}