<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Medico extends Elotech_Db_Table_Abstract {

	protected $_name = 'medico';
	protected $_primary = 'med_codigo';
        protected $_sequence = 'seq_med_codigo';


        /**
	 * Prestador de serviço
	 */
	const LABORATORIO = "L";
	const MEDICO = "M";
	const HOSPITAL = "H";

	public function salvar(array $data) {
            return false; // não pode salvar medico;
	}
        
        public function salvarPrestadorDeServico(array $data) {
            try{
                return parent::salvar($data);
            } catch (Exception $exc) {
                throw new Zend_Validate_Exception("Falha ao cadastrar Prestado de Serviço: ".$exc->getMessage());
            }
        }

	/**
	 * Buscar os médicos externos
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE,$prestador=array('M')) {
		if ($term)
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("med" => "medico"), array("med_codigo", "med_nome"))
					->where("prestador_servico IN (?)", $prestador)
					->where("retira_acentos(med_nome) ilike retira_acentos('%$term%')", "S")
					->order(array("med_nome"))
					->limit(15);
		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $usu) {
			$data = array();
			foreach ($usu as $key => $value) {			
				foreach($prestador as $prest)
					$data [$key."_".$prest] = $value;
			}

			$out [] = array(
				"id" => $usu->med_codigo,
				"label" => $usu->med_nome,
				"data" => $data
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("med_codigo" => "0", "med_nome" => "")
			);
		}

		return $out;
	}
    
    public function buscarDestino(){
        
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM medico WHERE prestador_servico = 'L' OR prestador_servico = 'H'

            "
        )->fetchAll();

        return $sql;
    }

    public function getInfoMedico($med_codigo=FALSE){
        if(!$med_codigo)
            return false;
            
        return $this->fetchRow ("med_codigo=$med_codigo");
    }
    
    public function getQtdPrestadorAtivosCnes(){ 
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("med" => "medico"), array("COUNT(med_codigo) AS qtd_prest"))
                    ->where("cnes_ativo = 'S'");
        return $this->fetchRow($sql);
    }
    
    public function getPrestadorPorCnes($cnes){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("med" => "medico"), array("med_codigo", "med_nome"))
                    ->where("med_cnes =?",$cnes);
        return $this->fetchRow($sql);
    }
    
    public function buscaPrestadorPorNome($nome_fantasia=FALSE,$nome_fantasia_quebrado=FALSE,$razao_social=FALSE) {
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("med" => "medico"), array("med_codigo", "med_nome"));
        foreach ($nome_fantasia_quebrado as $nome) {
            $nomes_provavel .= $nome." ";
            $sql->orWhere("med.med_nome ilike retira_acentos('%".trim($nomes_provavel)."%')");        
        }
        return $this->fetchAll($sql);
    }
    
    public function buscaPrestadorParaBpa(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("med" => "medico"), array("med_codigo", "med_nome"))
                    ->where("prestador_servico in ('L','H')")
                    ->order('med_nome ASC');
        return $this->fetchAll($sql);
    }

    public function recuperaPrestadorTipoL(){
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT med.med_codigo,med_nome FROM convenio AS conv 
                    INNER JOIN medico AS med 
                        ON med.med_codigo = conv.med_codigo
            "
        )->fetchAll();
        return $sql;
    }

    public function gerarRelatorioRecepcaoDeExames($recebeCodigoLocal, $recebeDataInicial, $recebeDataFinal){
        // die($recebeDataFinal);
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT med_nome,proc_nome,count(agei.coni_codigo),sum(coni_valor) AS valor from convenio AS conv
                    INNER JOIN convenio_itens AS coni 
                        ON coni.conv_codigo=conv.conv_codigo
                    INNER JOIN agenda_itens AS agei 
                        ON agei.coni_codigo=coni.coni_codigo
                    INNER JOIN procedimento AS proc 
                        ON proc.proc_codigo=coni.proc_codigo
                    INNER JOIN medico AS med 
                        ON med.med_codigo=conv.med_codigo
                where agei_data>='$recebeDataInicial' and agei_data <= '$recebeDataFinal' AND med.med_codigo = $recebeCodigoLocal
                    group by med_nome,proc_nome,conv.conv_codigo,coni_valor
                        order by proc_nome
            "
        )->fetchAll();

        return $sql;
    }

}
