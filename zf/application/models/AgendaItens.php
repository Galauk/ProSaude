<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

/**
 * Essa agenda é resposável pelo novo agendamento
 * @todo agendar exames e consultas, por quantidade (cota) e valor
 */
class Application_Model_AgendaItens extends Elotech_Db_Table_Abstract {

	protected $_name = 'agenda_itens';
	protected $_primary = 'agei_codigo';
	protected $_dependentTables = array();
	
	/**
	 * Status do item da agenda
	 */
	const AGENDADO = "A";
	const RECEPCIONADO = "R";
	const FALTA = "F";
	const CANCELADO = "C";
	const TRANSFERENCIA = "T";	
	
	/**
	 * Insert ou update em um item
	 * @param array $data dados do formulário
	 * @return int chave primária do registro inserido ou atualizado 
	 * @todo receber uma unidade de coleta diferente da unidade de exame
	 */
	public function salvar(array $data) {	
		$this->notEmpty(array("age_codigo","coni_codigo","agei_data"), $data);
		
		// Criar as exceções, baseadas nos modelos
		$tbGrad = new Application_Model_GradeDia();
		$tbGram = new Application_Model_GradeMes();
		
		$tbGrad->criarCotaFromModelo($data['coni_codigo'], $data['agei_data']);
		$tbGram->criarCotaFromModelo($data['coni_codigo'], $data['agei_data']);
		
		$data['agei_valor'] = $tbGrad->fetchRow("coni_codigo=".$data['coni_codigo'])->grad_valor;
		
		$this->valoresPadrao($data);
		
		//throw new Zend_Validate_Exception("<pre>".print_r($data,1)."</pre>");
		return parent::salvar($data);		
	}
	
	/**
	 * Valores padrão do insert/update
	 * @param array $data valores do insert
	 * @todo receber uma unidade de coleta diferente da unidade de exame
	 */
	private function valoresPadrao(&$data){		
		if(empty($data['agei_status']))
			$data['agei_status'] = self::AGENDADO;
		
		if(empty($data['usr_codigo'])){ 
			$tbUsr = new Application_Model_Usuarios();
			$data['usr_codigo'] = $tbUsr->getUsrAtual()->usr_codigo; // pode gerar exception
		}
		
		if(empty($data['uni_codigo_coleta']) && empty($data['med_codigo_coleta'])){
			$tbConi = new Application_Model_ConvenioItens();
			$coni = $tbConi->busca($data['coni_codigo']);
			
			// nesta versão, a unidade de coleta é a mesma que fará o exame
			$data['uni_codigo_coleta'] = $coni->uni_codigo;
			$data['med_codigo_coleta'] = $coni->med_codigo;
		}
	}
	
	/**
	 * Recebe um array de dados, e insere cada um deles como um item do agendamento
	 * @param array $arr
	 * @param int $age_codigo código da agenda (pai)
	 */
	public function salvarDoArray($arr, $age_codigo){

                /*VERIFICA SE É UM GRUPO DE EXAMES*/
                $tbConi = new Application_Model_ConvenioItens();
                $tbGruex = new Application_Model_GrupoExame();
                
		foreach($arr as $coni_codigo => $dia){
            list($d,$m,$y) = explode("/",$dia);
    
            $coni = $tbConi->busca($coni_codigo);

            $dados = array(
                "age_codigo" => $age_codigo,
                "agei_data" => "$y-$m-$d",
                "coni_codigo" => $coni_codigo,
                "turno" => $arr['turno']
            );

            $this->salvar($dados);
		}
	}
	
	/**
     * Verifica se o agendamento ainda pode ser cancelado
     * @param Zend_Db_Table_Row_Abstract $item item da agenda
	 * @see Application_Model_Agenda::getHistoricoDeExames()
     * @return bool 
	 */
	public function podeCancelarAgendaExame($item){
		// Regra:
		// Só pode ser cancelado um item no mesmo dia que ele foi feito
		// e se ainda estiver com status = AGENDADO
		list($data,) = explode(" ",$item->age_data_insert);
		
		if($data != date("Y-m-d"))
			return FALSE;
		
		if($item->agei_status != self::AGENDADO)
			return FALSE;
		
		return TRUE;
	}

	/**
	 * Exclusão lógica
	 * @param int $agei_codigo 
	 */
	public function excluir($agei_codigo){
		$this->alterarStatus($agei_codigo, self::CANCELADO);
	}
	
	/**
	 * Altera o status de um item da agenda
	 * @param int $agei_codigo 
	 */
	public function alterarStatus($agei_codigo, $agei_status){
		$agei = $this->find($agei_codigo)->current();
		$agei->agei_status = $agei_status;
		$agei->save();
	}
        
        public function getAgendaItemPorProcedimento($age_codigo=FALSE,$proc_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("agei"=>"agenda_itens"))
                          ->join(array("coni"=>"convenio_itens"),"coni.coni_codigo=agei.coni_codigo","")
                          ->join(array("proc"=>"procedimento"),"proc.proc_codigo=coni.proc_codigo","")
                          ->where("coni.proc_codigo=$proc_codigo")
                          ->where("agei.age_codigo=$age_codigo");
            return $this->fetchRow($where);
                    
        }
        
        public function getBioquimicoResponsavel($age_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->distinct()
                          ->from(array("agei"=>"agenda_itens"),array("usr_codigo_bioquimico"))
                          ->join(array("usr"=>"usuarios"),"usr.usr_codigo=agei.usr_codigo_bioquimico",array("usr_nome","usr_num_conselho"))
                          ->where("age_codigo=$age_codigo");
            return $this->fetchRow($where);
        }
        
        public function getBioquimicosResponsavelAgendamento($age_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->distinct()
                          ->from(array("agebr"=>"agenda_bioquimicos_responsavel"),array("usr_codigo"))
                          ->join(array("usr"=>"usuarios"),"agebr.usr_codigo=usr.usr_codigo",array("usr_nome","usr_num_conselho","cnes_sigla_est"))
                          ->join(array("con"=>"conselho"),"con.con_codigo=usr.con_codigo","con_descricao")
                          ->where("age_codigo=$age_codigo");
            
            return $this->fetchAll($where);
        }
        
}
