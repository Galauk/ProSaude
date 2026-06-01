<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_PreConsulta extends Elotech_Db_Table_Abstract {

	protected $_name = 'pre_consulta';
	protected $_primary = 'pc_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data,$json=FALSE) {
		// Verifica se já um paciente sendo atendido
                if($data['age_codigo'] == NULL){
                    $age = Application_Model_Agendamento::usuEmAberto();
                    $data['age_codigo'] = $age->age_codigo;
                }else{
                    $age = $data['age_codigo'];
                }
                if($data['usr_codigo'] == NULL)
                    $data['usr_codigo'] = $usr->usr_codigo;
                if($data['esp_codigo'] == NULL)
                    $data['esp_codigo'] = $usr->esp_codigo;
                if($data['pc_data'] == NULL)
                    $data['pc_data'] = "NOW()";
		
		$tbUsr = new Application_Model_Usuarios();
		
		$nomes = array(
			"pc_temperatura" => "temperatura",
			"pc_peso" => "peso",
			"pc_altura" => "altura",
			"pc_perimetro_cefalico" => "perímetro cefálico",
			"pc_pressao_sistolica" => "pressão sistólica",
			"pc_pressao_diastolica" => "pressão diastólica",
			"pc_freq_cardiaca" => "freq. cardíaca",
			"pc_freq_respiratoria" => "freq. respiratória",
			"pc_dados" => "outras informações"
		);
		$this->addRealName($nomes);

		if (!$age)
			throw new Zend_Validate_Exception("Agendamento não encontrado!", 1000);
		$usr = $tbUsr->getUsrAtual();
                
                
		$this->filterFloat(array("pc_temperatura", "pc_peso", "pc_altura", "pc_perimetro_cefalico"), $data);
		$this->filterDigits(array("pc_pressao_sistolica", "pc_pressao_diastolica", "pc_freq_cardiaca", "pc_freq_respiratoria"), $data);
		$this->emptyToNull($data);

		$range = array(
			"pc_temperatura" => array(30, 45),
			"pc_peso" => array(0, 200),
			"pc_altura" => array(0, 2.5)
		);
		$this->range($range, $data);

		// Há alguma informação? (impedir envio de PC totalmente vazia)
		//$this->peloMenosUm(array("pc_temperatura", "pc_peso", "pc_altura", "pc_perimetro_cefalico", "pc_pressao_sistolica", "pc_pressao_diastolica", "pc_freq_cardiaca", "pc_freq_respiratoria", "pc_dados"), $data);
		$pc_codigo = parent::salvar($data);
               
		// Procedimento realizado: aferição de pressão (0301100039)
                if (!empty($data['pc_pressao_sistolica']) && $json==FALSE) {
                    $this->geraProcedimento($pc_codigo,$data[usr_codigo],"0301100039");
                }
                
                if (!empty($data['pc_peso']) && !empty($data['pc_altura'])) {
                    $this->geraProcedimento($pc_codigo,$data[usr_codigo],"0101040024");
                }
                
                if (!empty($data['pc_glicose']) && $data['pc_glicose'] != 0) {
                    $this->geraProcedimento($pc_codigo,$data[usr_codigo],"0214010015");
                }
                
                
		return $pc_codigo;
	}
        
        public function geraProcedimento($pc_codigo=FALSE,$usr_codigo=FALSE,$proc_codigo_sus=FALSE){
            $tbProc = new Application_Model_Procedimento();
            $tbPat = new Application_Model_ProcedimentoAtendimento();

            $proc = $tbProc->fetchRow("proc_codigo_sus='$proc_codigo_sus'");
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimento();
            $dados = array(
                    "pc_codigo" => $pc_codigo,
                    "proc_codigo" => $proc->proc_codigo,
                    "usr_codigo" => $usr_codigo,
                    "ate_codigo" => ($ate ? $ate->ate_codigo : "")
            );
            if($tbPat->verificaSeRealizou($dados))
                $tbPat->salvar($dados);
            
            return true;
        }
        

	public function temPreConsulta($age_codigo) {
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("pc"=>"pre_consulta"))
                        ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo","age_atendido")
                        ->where("age.age_codigo=?",$age_codigo)
                        ->where("age.age_atendido='I'");
            //die($sql);
            return $this->fetchRow($sql);
	}
        
        public function getDadosPorAgendamento($age_codigo=FALSE) {
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("pc"=>"pre_consulta"))
                        ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo","age_atendido")
                        ->where("age.age_codigo=?",$age_codigo)
                        ->where("age.age_atendido='A'");
            //die($sql);
            return $this->fetchRow($sql);
        }
        
        
        
         public function validaDataNormal($dat){
            $data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como referência
            $d = $data[0];
            $m = $data[1];
            $y = $data[2];
            $res = checkdate($m,$d,$y);
            if ($res == 1){
                return true;
            } else {
                return false;
            }
        }
        /**
	 * Se não passar usu_codigo, irá pegar somente as PC deste agendamento
	 * @param int $usu_codigo
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param array $opcoes
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function getHistorico($usu_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $opcoes=FALSE,$limit=FALSE,$term=FALSE) {
		if (!$usu_codigo) {

			$age = Application_Model_Agendamento::usuEmAberto();
			if (!$age)
				throw new Exception("Agendamento não encontrado!!");
			return $this->fetchAll("age_codigo=" . $age->age_codigo);
		}
                
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pc" => "pre_consulta"), array("pc_codigo","pc_dados", "pc_temperatura", "pc_peso", "pc_altura", "pc_pressao_sistolica", "pc_pressao_diastolica","pc_clas_risco","pc_saturacao","pc_freq_cardiaca","pc_freq_respiratoria","pc_perimetro_cefalico","pc_glicose","pc_data"))
				->join(array("age" => "agendamento"), "age.age_codigo=pc.age_codigo", "age_data")
				->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
				->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=age.med_codigo", "usr_nome")
                                ->joinLeft(array("usr2" => "usuarios"), "usr2.usr_codigo=pc.usr_codigo", "usr_nome as usr_nome_enf")
				->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
				->where("age.usu_codigo=?", $usu_codigo)
				->order(array("pc.pc_data DESC"));

		if ($data_inicial)
                    $where->where("age.age_data >= ?", $data_inicial);

		if ($data_final)
                    $where->where("age.age_data <= ?", $data_final);
                
                if($limit)
                    $where->limit($limit);
                
                if($term){
                    if($this->validaDataNormal($term)){
                        $where->where("to_char(pc_data,'DD/MM/YYYY')  = '$term'");
                    }else{
                        $where->where("COALESCE(usr.usr_nome,'') || COALESCE(uni_desc,'') ILIKE ('%$term%')");
                    }
                }
                //die($where);
		return $this->fetchAll($where);
	}

	public function getPC($pc_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pc" => "pre_consulta"), array("pc_dados","pc_temperatura", "pc_peso", "pc_altura", "pc_pressao_sistolica", "pc_pressao_diastolica","pc_clas_risco","pc_codigo","to_char(pc_data,'dd/mm/yyyy') as pc_data"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pc.usr_codigo", "usr_nome")
				->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=pc.esp_codigo", "esp_nome")
				->join(array("age" => "agendamento"), "age.age_codigo=pc.age_codigo", "age_data")
				->join(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
				->where("pc.pc_codigo=?", $pc_codigo);
		return $this->fetchRow($where);
	}

	/**
	 * 
	 * Ele retorna toda uma linha, não só o id da ultima pré-consulta
	 * @throws Exception
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getUltima() {
		$age = Application_Model_Agendamento::usuEmAberto();
		if (!$age){
			throw new Exception("Agendamento não encontrado!!!");
                }
		return $this->fetchRow("age_codigo=" . $age->age_codigo, "pc_codigo DESC");
	}
        
         public function buscar($age_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("pc"=>"pre_consulta"),array("to_char(pc_data,'YYYY-MM-DD') as ate_data",
                                                                 "pc_codigo",
                                                                 "to_char(pc_hora_final,'HH24:MI') as ate_horafinal",
																 "to_char(pc_data,'HH24:MI') as ate_hora",
                                                                 "to_char(pc_hora_final,'YYYY-MM-DD') as ate_datafinal" ))
                        ->join(array("usr"=>"usuarios"),"usr.usr_codigo=pc.usr_codigo","usr_nome")
                        ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo","age_codigo")
                        ->join(array("esp"=>"especialidade"),"esp.esp_codigo=age.esp_codigo","esp_nome")
                        ->join(array("uni"=>"unidade"),"uni.uni_codigo=age.uni_codigo","uni_desc")
                        ->where("pc.age_codigo=?",$age_codigo);
            return $this->fetchRow($sql);
        }
       /* public function getPreConsulta($pc_codigo){
            $where = $this->select(FALSE)
                      ->setIntegrityCheck()
                      ->from(array("pc"=>"pre_consulta"))
                      ->order("");
        }*/
        
        public function excluir($pc_codigo=FALSE){
            $item = $this->fetchRow("pc_codigo=$pc_codigo");
            if ($item)
                $item->delete();

            return true;
        }

}
