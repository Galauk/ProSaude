<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PessoaPaciente extends Elotech_Db_Table_Abstract {
    protected $_name = 'pessoa_paciente';
    protected $_primary = 'pep_codigo';
    
    public function salvar(array $data) {
        $this->notEmpty(array("pessoa"), $data);  
        try {
            $pessoa = parent::salvar($data);
        } catch (Exception $exc) {
            //throw new Zend_Validate_Exception($exc->getMessage());
            throw new Zend_Validate_Exception("Falha ao cadastrar o Paciente".$exc->getMessage());
        }
        return $pessoa;        
    }
    
    public function confereInsPesPaciente($codPes){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("pep"=>"pessoa_paciente"),array("pep_codigo"))
                    ->where("pessoa =?",$codPes);
        return $this->fetchRow($sql);
    }
    
     /**
	 * Buscar os Pessoa
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE) {
		if ($term)
			$busca = "nome ilike retira_acentos('%$term%')";

		$all = $this->fetchAll($busca, "nome");

		$out = array();
		foreach ($all as $pessoa) {
			$out [] = array(
				"id" => $pessoa->pessoa,
				"label" => trim($pessoa->nome),
				"data" => array("pessoa" => $pessoa->pessoa,"nome" => $pessoa->nome)
			);
		}

		/*if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("pessoa" => "")
			);
		}
*/
		return $out;
	}
        public function sequencia(){
            return $this->getDefaultAdapter()->query("select nextval(aise.s90pessoa)");
        }
        
        public function listaDadosPessoa($pessoa=FALSE,$pessoa_paciente=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("pes"=>'aise.pessoa'))
                        ->joinLeft(array("pep"=>'pessoa_paciente'),"pep.pessoa=pes.pessoa")
                        ->joinLeft(array("dom"=>"domicilio"),"pep.dom_codigo=dom.dom_codigo")
                        ->joinLeft(array("rua"),"dom.rua_codigo=rua.rua_codigo")
                        ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=pep.cid_codigo","cid_nome")
                        ->joinLeft(array("tp_log"=>"tb_ms_tipo_logradouro"),"tp_log.co_tipo_logradouro=rua.co_tipo_logradouro")
                        ->where("pep.pep_codigo =?",$pessoa);
            //if($pessoa_paciente == 1){
            //    $sql->where("pep.pep_codigo =?",$pessoa);
            //}  else {
            //    $sql->where("pes.pessoa =?",$pessoa);
            //}
            //die($sql);   
            return $this->fetchAll($sql);
        }
        
    public function getProntuarioDuplicado($pep_prontuario=FALSE){
        $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("pep"=>'pessoa_paciente'),"COUNT(pep_prontuario) as num")
                        ->where("pep.pep_prontuario $pep_prontuario'");
       // die($sql);
        return $this->fetchRow($sql);
    }
 

}
