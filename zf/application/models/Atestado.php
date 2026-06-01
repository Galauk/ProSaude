<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Atestado extends Elotech_Db_Table_Abstract {

    protected $_name = 'atestado';
	protected $_primary = 'atest_codigo';
	protected $_sequence = 'seq_atestado';

	/**
	 * Salvar o item, insert ou update
	 * @param array $data chave=>valor
	 * @return int Primary Key
	 */
    public function salvar(array $data) {
        Zend_Registry::get("logger")->log($data, Zend_Log::INFO);
	//Apenas um atestado por atendimento
	$atest = $this->buscar();
        if($atest)
            $data['atest_codigo'] = $atest->atest_codigo;
        //$this->emptyToUnset($data);
        // Se for novo, e sem ate_codigo
        if (is_null($data['ate_codigo']) && is_null($data['atest_codigo'])) {
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimentoMedico();
            if (!$ate)
                    throw new Zend_Validate_Exception("Atendimento não encontrado!", 1009);
            $data['ate_codigo'] = $ate->ate_codigo;
        }
	Zend_Registry::get("logger")->log($data, Zend_Log::INFO);
        return parent::salvar($data);
    }
	
	public function buscar($atest_codigo = false){		
		if(!$atest_codigo){
			$tbAte = new Application_Model_Atendimento();
			$ate = $tbAte->temAtendimentoMedico();
			
			return $this->fetchRow("ate_codigo=".$ate->ate_codigo);
		}else{
			return $this->fetchRow("atest_codigo=".$atest_codigo);
		}
	}

	public function imprimir($atest_codigo = false, $cid = false) {
                $tbAte = new Application_Model_Atendimento();
                $tbConf = new Application_Model_Configuracao();
                $tbAtest = new Application_Model_Atestado();
                $atest = $this->buscar($atest_codigo);
		$dados = (object) $atest->toArray();
		$dados->codigo = $atest->atest_codigo;
		$ate = $tbAte->buscar($atest->ate_codigo);
                $cid = $tbAtest->fetchRow("atest_codigo=".$atest->atest_codigo)->cid_codigo;
                // Verificando se CID é obrigatório
                //if ($tbConf->getConfig("CID_OBRIGATORIO")=="1") {
                    if ($cid) {
                        $tbCid = new Application_Model_Cid();
                        $dados->cid = $tbCid->fetchRow("cd10_codigo=".$cid)->cd10_codigo_cid;
                    }
                //}
                $dados->data = $ate->ate_data;
		$dados->hora = $ate->ate_hora;
		
		$dados->itens = array();
		
		//if($atest->consulta_medica == 'S')
		//	$dados->itens []= "Esteve em consulta médica dia ".$ate->ate_data." as ".$ate->ate_hora.".";
		
		if($atest->acompanhando_filho == 'S')
			$dados->itens []= "Acompanhado por <strong>".$atest->acompanhando."</strong>.";
		
		if($atest->retorno_trabalho == 'S')
			$dados->itens []= "Devendo retornar ao trabalho ".$atest->retornoaotrabalho.".";
		
		if($atest->repouso_hs == 'S')
			$dados->itens []= "Devendo permanecer em repouso <strong>".$atest->repousohs_ini."hs.</strong> a partir das <strong>".$atest->repousohs_final."hs.</strong>";
		
		if($atest->repouso_hoje == 'S')
			$dados->itens []= "Devendo permanecer em repouso hoje.";
		
		if($atest->repouso_dia == 'S')
			$dados->itens []= "Devendo permanecer em repouso <strong>".$atest->repousodias." dias</strong>, a partir desta data.";
		
		$age = Application_Model_Agendamento::usuEmAberto();


		// dados do paciente
		$tbUsu = new Application_Model_Usuario();
		$usu = $tbUsu->find($age->usu_codigo)->current();
		
                $tbDom = new Application_Model_Domicilio();
                $dom_dados = $tbDom->getEnderecoPorUsuario($usu->usu_codigo);
                $dados->rua_nome = $dom_dados->rua_nome;
                $dados->dom_numero = $dom_dados->dom_numero;
                
                $dados->usu_sexo = $usu->usu_sexo;
                $dados->usu_datanasc = $usu->usu_datanasc;
                $dados->usu_cartao_sus = $usu->usu_cartao_sus;
                $dados->usu_prontuario = $usu->usu_prontuario;
		$dados->usu_codigo = $usu->usu_codigo; 
		$dados->usu_nome = $usu->usu_nome;
                $dados->idade = $usu->usu_datanasc;
		
		// dados do médico
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		$dados->usr_nome = $usr->usr_nome;
		$dados->usr_num_conselho = $usr->usr_num_conselho;
                $dados->cnes_sigla_est = $usr->cnes_sigla_est;
                $dados->con_descricao = $usr->con_descricao;
		
		// dados da unidade
		$tbUni = new Application_Model_Unidade();		
		$uni = $tbUni->buscarCidadeDaUnidade($age->uni_codigo)->current();                
                
                $dados->nome_cidade = $uni->cid_nome;
		$dados->uni_desc = $uni->uni_desc;
		$dados->uni_endereco = $uni->uni_endereco;
		
		// dados da secretaria
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$dados->secretaria = $sec->nome_secretaria;
		
		return $dados;
	}
        
        /*public function verificaSeExisteCidAtestado(){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array())
        }*/
}
