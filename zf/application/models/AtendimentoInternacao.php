<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_AtendimentoInternacao extends Elotech_Db_Table_Abstract {

	protected $_name = 'atendimento_internacao';
	protected $_primary = 'atin_codigo';
	protected $_sequence = 'atendimento_internacao_atin_codigo_seq';
	protected $_dependentTables = array();

	const ATENDIMENTO = "ate_codigo";
	const INTERNACAO_OBSERVACAO = "io_codigo";


	public function salvar($data) {            
            $this->addRealName(array("io_codigo" => "internacao"));                
            return parent::salvar($data);
	}

	/**
	 * @param int $usu_codigo
	 * @param string $data_inicial Opcional. Proceimento realizados a partir esta data (inclusive)
	 * @param string $data_final Opcional. Proceimento realizados até esta data (inclusive)
	 */
	public function getHistoricoPorPaciente($usu_codigo, $data_inicial=FALSE, $data_final=FALSE) {
		$sqlAte = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pa" => "procedimento_atendimento"), "pat_codigo")
				->join(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
				->join(array("ate" => "atendimento"), "ate.ate_codigo=pa.ate_codigo", array("ate_codigo","ate_hora","ate_data"))
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
				->join(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
				->join(array("usr" => "usuarios"), "usr.usr_codigo=ate.med_codigo", "usr_nome")
				->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
				->where("ate.usu_codigo=?", $usu_codigo);
		
		if ($data_inicial) {
			$sqlAte->where("ate.ate_data >= ?", $data_inicial);
			
		}

		if ($data_final) {
			$sqlAte->where("ate.ate_data <= ?", $data_final);
			
		}
		
		$sqlPre = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pa" => "procedimento_atendimento"), "pat_codigo")
				->join(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
				->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pa.ate_codigo", array("ate_codigo", "ate_hora"))
				->joinLeft(array("pre" => "pre_consulta"), "pre.pc_codigo=pa.pc_codigo", "")
				->joinLeft(array("age" => "agendamento"), "age.age_codigo=pre.age_codigo", "age_data")				
				->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
				->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=pa.usr_codigo", "usr_nome")
				->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
				->where("age.usu_codigo=?", $usu_codigo);
		if ($data_inicial)
			$sqlPre->where("age.age_data >= ?", $data_inicial);

		if ($data_final)
			$sqlPre->where("age.age_data <= ?", $data_final);
		
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->union(array($sqlAte, $sqlPre), Zend_Db_Select::SQL_UNION)
				->order(array("ate_data DESC", "ate_hora DESC"));
		//die($where);

		return $this->fetchAll($where);
	}

	public function getHistoricoPorAgendamento($age_codigo) {
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("age" => "agendamento"), "")
				->join(array("uni"=>"unidade"),"uni.uni_codigo=age.uni_codigo","uni_desc")
				->join(array("esp"=>"especialidade"),"esp.esp_codigo=age.esp_codigo","esp_nome")
				->joinLeft(array("ate" => "atendimento"), "ate.age_codigo=age.age_codigo", "")
				->joinLeft(array("pc" => "pre_consulta"), "pc.age_codigo=age.age_codigo", "")
				->joinLeft(array("pe" => "posto_enfermagem"), "pe.ate_codigo=ate.ate_codigo", "")
				->joinLeft(array("pat" => "procedimento_atendimento"), "pat.ate_codigo=ate.ate_codigo OR pat.pc_codigo=pc.pc_codigo OR pat.pe_codigo=pe.pe_codigo","")
				->joinLeft(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
				->where("age.age_codigo=?",$age_codigo)
				->group(array("proc_nome","uni_desc","esp_nome"))
				->order("proc_nome");
		
		return $this->fetchAll($where);
	}

	public function getHistoricoGeral() {
		$tbAte = new Application_Model_Atendimento();
		$ate = $tbAte->temAtendimento();
		if ($ate) {
			return $this->getHistorico($ate->ate_codigo, self::ATENDIMENTO);
		} else {
			return $this->getHistorico(FALSE, self::PRE_CONSULTA);
		}
	}

	public function getHistorico($codigo=FALSE, $tipo=self::ATENDIMENTO) {
		if ($tipo == self::ATENDIMENTO && !$codigo) {
			$tbAte = new Application_Model_Atendimento();
			$ate = $tbAte->temAtendimento();
			if (!$ate)
				return false;

			$codigo = $ate->ate_codigo;
		}

		if ($tipo == self::PRE_CONSULTA && !$codigo) {
			$tbPre = new Application_Model_PreConsulta();
			$pre = $tbPre->getUltima();
			if (!$pre)
				return false;

			$codigo = $pre->pc_codigo;
		}

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pa" => "procedimento_atendimento"), "pa.pat_codigo")
				->join(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
				->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
				->where($tipo . "=?", $codigo);

		return $this->fetchAll($where);
	}

	public function getHistoricoPostoEnfermagem() {
		
	}

	/**
	 * Retorna as informações de um procedimento realizado
	 * Busca nas tabelas PC, PE e ATE
	 * @param int $pat_codigo
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function buscar($pat_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pat" => "procedimento_atendimento"), "pat_codigo")
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pat.usr_codigo", "usr_nome")
				->join(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
				->joinLeft(array("cd10" => "cid10"), "cd10.cd10_codigo=pat.cd10_codigo")
				->joinLeft(array("pe" => "posto_enfermagem"), "pe.pe_codigo=pat.pe_codigo", "")
				->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pe.ate_codigo OR ate.ate_codigo=pat.ate_codigo", array("ate_codigo", "ate_data"))
				->joinLeft(array("pc" => "pre_consulta"), "pc.pc_codigo=pat.pc_codigo", "")
				->joinLeft(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo OR age.age_codigo=pc.age_codigo", "")
				->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=pc.esp_codigo OR esp.esp_codigo=pe.esp_codigo OR esp.esp_codigo=age.esp_codigo", "esp_nome")
				->joinLeft(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", "usu_nome")
				->where("pat.pat_codigo=?", $pat_codigo);
                //die($where);
		return $this->fetchRow($where);
	}

	public function excluir($pat_codigo) {
		return $this->delete("pat_codigo=$pat_codigo");
	}
	public function getInternacaoEAgendamento($age_codigo){
		$where = $this->select(FALSE)
				 ->setIntegrityCheck(FALSE)
				 ->from(array("age"=>"agendamento"))
				 ->joinLeft(array("ate"=>"atendimento"),"age.age_codigo = ate.age_codigo","")
				 ->joinLeft(array("ati"=>"atendimento_internacao"),"ate.ate_codigo = ati.ate_codigo","io_codigo")
			     ->where("age.age_codigo=?",$age_codigo);
		// die ($where);
		return $this->fetchAll($where);		
	}
        
        public function verificaAtendimentosPorInternacao($io_codigo){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ai"=>"atendimento_internacao"),"count (atin_codigo) as qtde")
                          ->where("io_codigo=?",$io_codigo);
            return $this->fetchRow($where);
        }
	
	public function getInternacao($io_codigo){
		$where = $this->select(FALSE)
				 ->setIntegrityCheck(FALSE)
				 ->from(array("age"=>"agendamento"),array("uni_codigo","age_codigo"))
				 ->joinLeft(array("ate"=>"atendimento"),"age.age_codigo = ate.age_codigo",array("ate.usu_codigo","ate.med_codigo"))
				 ->joinLeft(array("ati"=>"atendimento_internacao"),"ate.ate_codigo = ati.ate_codigo","io_codigo")
                                 ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=ate.med_codigo","usr_nome")
			     ->where("ati.io_codigo=?",$io_codigo)
				->order("ate.ate_codigo");
	//die ($where);
		return $this->fetchAll($where);
	//	return $where;
	}
	public function getHistoricoInternacao($io_codigo){
		$where = $this->select(FALSE)
				 ->setIntegrityCheck(FALSE)
				 ->from(array("ati"=>"atendimento_internacao"),array("ati.io_codigo"))
				 ->join(array("ate"=>"atendimento"),"ate.ate_codigo = ati.ate_codigo")
				 ->join(array("usr"=>"usuarios"),"ate.med_codigo = usr.usr_codigo","usr.usr_nome")
			     ->where("ati.io_codigo=?",$io_codigo)
				->order(array("ate.ate_codigo","ate.ate_hora"));
	//die ($where);
		return $this->fetchAll($where);
	//	return $where;
	}
        
        public function getAtendimentoDeOrigem($io_codigo=FALSE){
            $where = $this->select(FALSE)
				 ->setIntegrityCheck(FALSE)
				 ->from(array("ati"=>"atendimento_internacao"),array("ati.io_codigo"))
				 ->join(array("ate"=>"atendimento"),"ate.ate_codigo = ati.ate_codigo")
				 ->join(array("age"=>"agendamento"),"age.age_codigo = ate.age_codigo","")
			     ->where("ati.io_codigo=?",$io_codigo)
				->order(array("ate.ate_codigo"));
	//die ($where);
		return $this->fetchRow($where);
        }
        
        public function getDadosInternaObservacao($ate_codigo){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("atei"=>"atendimento_internacao"))
                          ->join(array("io"=>"internacao_observacao"),"io.io_codigo=atei.io_codigo")
                          ->where("ate_codigo=$ate_codigo");
            return $this->fetchRow($where);
        }
}