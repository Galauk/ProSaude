<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_VacinaUsuario extends Elotech_Db_Table_Abstract {

    protected $_name = 'vacina_usuario';
	protected $_primary = 'vac_usu_codigo';
	
	const APLICAR = "A";
	const APRAZAR = "Z";
	const PREENCHER = "P";
	const CANCELAR = "C";
	const REFORCO = "6";

    public function salvar(array $data) {
				
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		$data['usr_codigo'] = $usr->usr_codigo;
		$data['vac_unidade'] = $usr->uni_codigo;
		
		$this->notEmpty(array("pro_codigo","vac_acao","usu_codigo","vac_dose"), $data);
		
		switch ($data['vac_acao']) {
			case "A":
				return $this->aplicar($data);
            break;
			case "C":
				return $this->cancelar($data);
            break;
			case "Z":
			case "P":
				return $this->acao($data);
            break;
			default:
				throw new Zend_Validate_Exception("Ação inválida");
            break;
        }
    }
	
	/**
	 * Registra vacinas do tipo preenchidas ou aprazadas
	 * @param array $data 
	 */
	private function acao($data){
		// verifica se já é vacina preenchida/aprazada ou mesmo aplicada
		$this->verificarSeJaTomou($data);
		parent::salvar($data);
	}
	
	/**
	 * Registra uma vacina na carteirinha do paciente, baixa o estoque (controlefracionado)
	 * @param array $data 
	 */
	private function aplicar($data){
		
		$this->getAdapter()->beginTransaction();
        Zend_Registry::get("logger")->log("Aplicando vacina", Zend_Log::INFO);
        
		try {
			// verifica se já é vacina preenchida/aprazada ou mesmo aplicada
			$this->verificarSeJaTomou($data);
			Zend_Registry::get("logger")->log($data, Zend_Log::INFO);
			
			$tbUsr = new Application_Model_Usuarios();
			$usr = $tbUsr->getUsrAtual();
			
			$tbPro = new Application_Model_Produto();
			$melhorLote = $tbPro->selecionaMelhorLoteFracionado($data['pro_codigo'], $usr->set_codigo);

			Zend_Registry::get("logger")->log($melhorLote->toArray(), Zend_Log::INFO);
			
			if (!$melhorLote->count()) {
				throw new Zend_Exception("Não há frasco aberto para esta vacina");
			}
			$melhorLote = $melhorLote->current();

			// baixar no controlefracionado
			$tbCont = new Application_Model_ControleFracionado();
			$cont = $tbCont->dispensar($melhorLote->cont_codigo);
			
			$data['cont_codigo'] = $cont->cont_codigo;
			$data['vac_qtde'] = 1;
			Zend_Registry::get("logger")->log($cont->toArray(), Zend_Log::INFO);
			Zend_Registry::get("logger")->log($data, Zend_Log::INFO);
			
			$dados = parent::salvar($data);
            $this->getAdapter()->commit();

            return $dados;
            
		} catch (Exception $e) {
			Zend_Registry::get("logger")->log("Exception: ".$e->getMessage(), Zend_Log::WARN);
            Zend_Registry::get("logger")->log($e->getTrace(), Zend_Log::INFO);
            throw new Zend_Exception($e->getMessage());
			$this->getAdapter()->rollBack();
		}		
	} 
	
	/**
	 * Verifica se o paciente já tomou uma vacina
	 * Somente reforço pode ser sobreposto
	 * @param array $data 
	 */
	private function verificarSeJaTomou(&$data){
		// verifica se já é vacina preenchida/aprazada ou mesmo aplicada
		$vac_usu = $this->getVacinaUsuario($data);
		Zend_Registry::get("logger")->log("Verificar se o paciente já tomou a vacina", Zend_Log::INFO);
				
		if($vac_usu){
			Zend_Registry::get("logger")->log($vac_usu->toArray(), Zend_Log::DEBUG);
			if(in_array($vac_usu->vac_acao, array("A", "P")) && $vac_usu->vac_dose != "R"){
				throw new Zend_Exception(sprintf("Este paciente já tomou a %dª dose desta vacina", $data['vac_dose']));
			}
			// forçar update
			if($vac_usu->vac_dose != "R"){
                $data['vac_usu_codigo'] = $vac_usu->vac_usu_codigo;
            }
		}
	}
	
	/**
	 * Seleciona um registro da vacina_usuario, baseado no produto, paciente e dose
	 * Não importa a ação
	 * @param array $data
	 * @return Zend_Db_Table_Row_Abstract 
	 */
	private function getVacinaUsuario($data){
		$where = $this->select()
				->where("pro_codigo=?",$data['pro_codigo'])
				->where("usu_codigo=?",$data['usu_codigo'])
				->where("vac_dose=?",$data['vac_dose']);
		
		return $this->fetchRow($where);
	}
	
	/**
	 * Cancela uma vacina já aplicada (A/P/Z)
	 * @param array $data 
	 * @todo verificar regra para voltar o estoque em vacinas já aplicadas
	 */
	private function cancelar($data){		
		$vac_usu = $this->getVacinaUsuario($data);
		
		// Produto aplicado?
		if($vac_usu->vac_acao == self::APLICAR){			
			// buscar informações sobre essa dose
			$dose = $this->getDoseInfo($vac_usu->vac_usu_codigo);
			
			// setor atual?
			$tbUsr = new Application_Model_Usuarios();
			$set_codigo = $tbUsr->getUsrAtual()->set_codigo;
			
			// Somente se foi aplicada no mesmo setor que está cancelando
			// e se foi aplicada hoje
			if($set_codigo == $dose->set_codigo && $dose->vac_data == date("Y-m-d")){
				$tbCont = new Application_Model_ControleFracionado();
				$tbCont->devolverFracao($dose->cont_codigo);			
			}
		}
		
		return $vac_usu->delete();
	}
	
	/**
	 * Retorna o pro_codigo, vac_data, set_codigo, cont_dose e cont_codigo de uma dose (produto)
	 * @param int $vac_usu_codigo
	 * @return Zend_Db_Table_Row_Abstract 
	 */
	private function getDoseInfo($vac_usu_codigo){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("vac"=>"vacina_usuario"),array("pro_codigo","vac_data"))
				->join(array("cont"=>"controlefracionado"),"cont.cont_codigo=vac.cont_codigo",array("cont_codigo","set_codigo","cont_dose"))
				->where("vac_usu_codigo=?",$vac_usu_codigo);
		
		return $this->fetchRow($where);
	}
	
	/**
	 * Cancela uma vacina pelo vac_usu_codigo (pk)
	 * @param int $vac_usu_codigo 
	 */
	public function deletar($vac_usu_codigo){
		$dados = $this->find($vac_usu_codigo)->current()->toArray();
		return $this->cancelar($dados);
	}
	
	/**
	 * Informa quais vacinas foram aplicadas(A/P/Z) no paciente
	 * @param int $usu_codigo
	 * @param array $tipo A=Aplicada, P=Preenchida, Z=Aparazada (padrão: todas)
	 * @param string $data_inicial
	 * @param string $data_final
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function getHistorico($usu_codigo, $tipo=FALSE, $data_inicial=FALSE, $data_final=FALSE){		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("vac"=>"vacina_usuario"),array("vac_usu_codigo","vac_data","vac_dose","vac_acao"))
				->joinLeft(array("cont"=>"controlefracionado"),"cont.cont_codigo=vac.cont_codigo","")
				->joinLeft(array("ite"=>"itens_movimento"),"ite.ite_codigo=cont.ite_codigo",array("ite_lote"=>"COALESCE(ite_lote,'--')","ite_validade"=>"COALESCE(ite_validade,'1900-01-01')"))
				->joinLeft(array("pro2"=>"produto"),"pro2.pro_codigo=vac.pro_codigo",array("pro_nome","pro_codigo"))
				->join(array("uni"=>"unidade"),"uni.uni_codigo::text=vac.vac_unidade","uni_desc")
				->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=vac.usr_codigo",array("usr_nome"=>"COALESCE(usr_nome,'--')"))
				->where("vac.usu_codigo=?",$usu_codigo)
				->order("vac.vac_data ASC");
		
		if($tipo){
            $where->where ("vac_acao IN (?)", $tipo);
        }
		
		if($data_inicial){
            $where->where ("vac_data >= ?", $data_inicial);
        }
		
		if($data_final){
            $where->where ("vac_data <= ?", $data_final);
        }
		return $this->fetchAll($where);		
	}
	
	public function imprimir($usu_codigo, $ate_data) {
		$fun = new Application_Model_Funcoes();
		$dados = new stdClass();

		$dados->codigo = $usu_codigo;
		
		// seleciona ultimo aprazamento:
		$ultimoZ = $this->fetchRow("usu_codigo=$usu_codigo AND vac_acao='Z' AND vac_data < '$ate_data'", "vac_data ASC");
		if($ultimoZ){
			$dados->data = $ultimoZ->vac_data;
		} else {
			$dados->data = $fun->invertData($ate_data);
		}
		
		$dados->dataSolicitada = $fun->invertData($ate_data);

		// dados do paciente
		$tbUsu = new Application_Model_Usuario();
		$usu = $tbUsu->find($usu_codigo)->current();

		$end = array();
		$end [] = $usu->usu_end_rua;
		$end [] = $usu->usu_end_nr;
		$end [] = $usu->usu_end_compl;
		$end [] = $usu->usu_end_bairro;
        $end [] = $usu->usu_end_cidade;
        
		foreach ($end as $k => $item) {
			if (empty($item)){
                unset($end[$k]);
            }
		}
		
		$dados->usu_nome = $usu->usu_nome;
		$dados->usu_endereco = implode(", ",$end);
		
		// dados do médico
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		$dados->usr_nome = $usr->usr_nome;
		$dados->usr_num_conselho = $usr->usr_num_conselho;
		
		// dados da unidade
		$tbUni = new Application_Model_Unidade();
		$uni = $tbUni->find($usr->uni_codigo)->current();
		
		$dados->uni_desc = $uni->uni_desc;
		$dados->uni_endereco = $uni->uni_endereco;
		
		// dados da secretaria
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$dados->secretaria = $sec->nome_secretaria;
        $dados->nome_cidade = $sec->nome_cidade;
		
		return $dados;
	}

    public function imprimirDados($usu_codigo) {
		$fun = new Application_Model_Funcoes();
		$dados = new stdClass();

		$dados->codigo = $usu_codigo;
			
		$dados->dataSolicitada = $fun->invertData($ate_data);

		// dados do paciente
		$tbUsu = new Application_Model_Usuario();
		$usu = $tbUsu->find($usu_codigo)->current();
                
        $dados->usu_nome = $usu->usu_nome;
        $newdt = explode("-",$usu->usu_datanasc);
        $dt = $newdt[2]."/".$newdt[1]."/".$newdt[0];
        $dados->usu_datanasc = $dt;
        
        $dados->usu_mae = $usu->usu_mae;
		
		// dados do médico
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		$dados->usr_nome = $usr->usr_nome;
		$dados->usr_num_conselho = $usr->usr_num_conselho;
		
		// dados da unidade
		$tbUni = new Application_Model_Unidade();
		$uni = $tbUni->find($usr->uni_codigo)->current();
		
		$dados->uni_desc = $uni->uni_desc;
		$dados->uni_endereco = $uni->uni_endereco;
		
		// dados da secretaria
		$tbSec = new Application_Model_Secretaria();
		$sec = $tbSec->fetchRow();
		
		$dados->secretaria = $sec->nome_secretaria;
        $dados->nome_cidade = $sec->nome_cidade;
		
		return $dados;
	}
     
    public function regImpVia($usu_codigo) {
        $dados = new stdClass();
        $dados->codigo = $usu_codigo;
        // dados do paciente
        $tbPrint = new Application_Model_ImpressoesVia();
		
		$tbUsu = new Application_Model_Usuario();
		$usu = $tbUsu->find($usu_codigo)->current();
                        
        // dados da unidade
        $tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
                
		$tbUni = new Application_Model_Unidade();
		$uni = $tbUni->find($usr->uni_codigo)->current();
        $dt = date("d/m/Y H:i:s");
        $via = $tbPrint->getVia($usu->usu_codigo);
                
        if($via[prt_via]=="") {
            $numvia = "1"; 
        } else {
            $numvia = $via[prt_via]+1;
        }
        
        $dados = array(
            "usu_codigo" => $usu->usu_codigo,
            "prt_programa" => 'VACINA',
            "uni_codigo" => $uni->uni_codigo,
            "prt_data" => $dt,
            "prt_via" => $numvia				
        );
  
        $tbPrint->salvar($dados);
        //$this->view->dialog = array("Confirmação", "Agendamento salvo com sucesso!", 300, 140);
    }

    public function dadosVacinadosSiPni($codigo_sistema=FALSE,$uni_codigo,$competencia,$ibge = false){        
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
                ->distinct()
				->from(array("vac"=>"vacina_usuario"),array(new Zend_Db_Expr ("{$codigo_sistema} AS codigo_sistema"),"usu_codigo"))	
                ->join(array("uni"=>"unidade"),"uni.uni_codigo::text=vac.vac_unidade","uni_cnes")
                ->join(array("usu"=>"usuario"),"usu.usu_codigo = vac.usu_codigo",array("usu_cartao_sus",
                    "usu_nome",
                    "usu_mae",
                    "CASE WHEN (usu_sexo not in ('M','F'))
                        THEN 'M'
                        ELSE
                        usu_sexo
                    END usu_sexo","to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc"))
                    ->join(array("rac"=>"raca"),"rac.rac_codigo = usu.rac_codigo",array("CASE WHEN (rac.rac_codigo = '3') THEN '5'
                            WHEN rac.rac_codigo = '4' THEN '3'
                            WHEN rac.rac_codigo = '5' THEN '4'
                            WHEN rac.rac_codigo = '9' THEN '99'					          
                            WHEN rac.rac_codigo = 'X' THEN '99'
                            ELSE rac.rac_codigo
                        END rac_codigo"))
                    ->join(array("dom"=>"domicilio"),"dom.dom_codigo = usu.dom_codigo","")
				    ->join(array("rua"=>"rua"),"rua.rua_codigo = dom.rua_codigo","")
                    ->join(array("bai"=>"bairro"),"bai.bai_codigo = rua.bai_codigo","")                               
                    ->join(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo",array(new Zend_Db_Expr ("'' AS zona"),new Zend_Db_Expr ('10 AS pais_codigo'),"COALESCE(substring(cid_codigo_ibge,1,6),'{$ibge}') as cid_codigo_ibge"))
				->where("uni.uni_codigo=?",$uni_codigo)
                ->where("to_char(vac_data,'MM/YYYY') = '$competencia'");		
		// die($where);
		return $this->fetchAll($where);
    }
    
    public function salvarVacinaCampanhaEstrategia($dados){
        $this->getDefaultAdapter()->query("INSERT INTO vac_est_cam (vacinacao, estrategia, campanha) VALUES ({$dados['vacina']}, {$dados['estrategia']}, {$dados['campanha']})")->fetch();
        return true;
    }
    
    public function salvarDadosVacina($vacinacao, $dose_tipo, $dose_trat){
        try {
            $this->getDefaultAdapter()->query("INSERT INTO vacinacao_aplicacao (vac_codigo, dose_tipo, dose_trat) VALUES ($vacinacao, '$dose_tipo', '$dose_trat')")->fetch();
            return true;
        } catch(Exception $e){
            $json = array(
                "success" => false,
                "mensagem" => array(
                    "titulo" => "Erro",
                    "mensagem" => $e->getMessage(),
                    "x" => 300,
                    "y" => 200
                )
            );            
            return $json;
        }
    }
}