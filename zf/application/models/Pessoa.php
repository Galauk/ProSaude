<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Pessoa extends Elotech_Db_Table_Abstract {
    
    protected $_name = 'pessoa';
    protected $_schema = 'aise';
    protected $_primary = 'pessoa';
    protected $_sequence = 'aise.s90pessoa';
    
    // Efetua a inserção de pessoa no banco do AISE
    public function salvar(array $data) {
        
        try {
            $pessoa = parent::salvar($data);
            //$a = 1;
        } catch (Exception $exc) {
            //throw new Zend_Validate_Exception($exc->getMessage());
            throw new Zend_Validate_Exception("Falha ao cadastrar pessoa!".$exc->getMessage()); 
        }
        return $pessoa;        
    }
    
    public function listaCadastroDuplicado($dadosPessoa=FALSE,$nome=FALSE){
        $array_paciente = array();
        $i = 1;
        $quantos_nomes = count($nome)." ";
        foreach($nome as $nome_fragmento){
             /*ESTE SQL VAI FAZER COMPARAÇÃO COM PARTES DO NOME + DATA_NASCIMENTO*/
             $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("pes" => "aise.pessoa"),array("COALESCE(to_char(datanascimento,'DD/MM/YYYY'),NULL,'-----') AS datanascimento","pes.pessoa","pes.nome","(CASE WHEN pes.inativo = 'S' THEN 'INATIVO' WHEN pes.inativo = 'N' THEN 'ATIVO' END) as inativo"))
                    ->joinLeft(array("pep"=>"pessoa_paciente"),"pes.pessoa=pep.pessoa",array("COALESCE(pep_codigo,NULL,0) AS pep_codigo","COALESCE(pep_mae,NULL,'----') AS pep_mae","pep.pep_sexo"));
             
             if($i == 1){
                 $sql->where("pes.nome ilike '$nome_fragmento%'");
             }else if($i == rtrim($quantos_nomes)){
                 $sql->where("pes.nome ilike '%$nome_fragmento'");
             }else{
                 $sql->where("pes.nome ilike '%$nome_fragmento%'");
             }
             $sql->where("datanascimento = '$dadosPessoa[datanascimento]'");
             foreach($this->fetchAll($sql)->toArray() as $paciente){
                 //echo $paciente[nome];
                 $array_paciente[$paciente["pessoa"]] = array("nome"=>$paciente["nome"],
                                                              "pep_mae"=>$paciente["pep_mae"],
                                                              "datanascimento"=>$paciente["datanascimento"],
                                                              "inativo"=>$paciente["inativo"],
                                                              "prontuario"=>$paciente["pep_codigo"]);
             }
             
             
             /*ESTE SQL VAI FAZER COMPARAÇÃO COM PARTES DO NOME + NOME_MAE*/
             $sqlMae = $this->select(FALSE)
                     ->setIntegrityCheck(FALSE)
                     ->from(array("pes" => "aise.pessoa"),array("pes.pessoa","pes.nome","(CASE WHEN pes.inativo = 'S' THEN 'INATIVO' WHEN pes.inativo = 'N' THEN 'ATIVO' END) as inativo","COALESCE(to_char(datanascimento,'DD/MM/YYYY'),NULL,'-----') AS datanascimento"))
                     ->joinLeft(array("pep"=>"pessoa_paciente"),"pes.pessoa=pep.pessoa",array("COALESCE(pep_codigo,NULL,0) AS pep_codigo","COALESCE(pep_mae,NULL,'----') AS pep_mae","pep.pep_sexo"));
             
             
             if($i == 1){
                 $sqlMae->where("pes.nome ilike '$nome_fragmento%'");
             }else if($i == rtrim($quantos_nomes)){
                 $sqlMae->where("pes.nome ilike '%$nome_fragmento'");
             }else{
                 $sqlMae->where("pes.nome ilike '%$nome_fragmento%'");
             }
             $sqlMae->where("pep_mae ilike '%$dadosPessoa[pep_mae]%'");
             
            foreach($this->fetchAll($sqlMae)->toArray() as $paciente){
                 //echo $paciente[nome];
                 $array_paciente[$paciente["pessoa"]] = array("nome"=>$paciente["nome"],
                                                              "pep_mae"=>$paciente["pep_mae"],
                                                              "datanascimento"=>$paciente["datanascimento"],
                                                              "inativo"=>$paciente["inativo"],
                                                              "prontuario"=>$paciente["pep_codigo"]);
             }
             $i++;
        }
       
        return $array_paciente;
    }
    
    // Confere se a sequence do código de pessoa foi inserida ou não
    public function getQtdDeCadastroDuplicado($dadosPessoa=FALSE,$nome=FALSE){
        
    }
    
    public function listaDadosPessoa($pessoa){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("pes"=>'aise.pessoa'))
                    ->join(array("pep"=>"pessoa_paciente"),"pes.pessoa=pep.pessoa")
                    ->joinLeft(array("dom"=>"domicilio"),"pep.dom_codigo=dom.dom_codigo")
                    ->joinLeft(array("rua"),"dom.rua_codigo=rua.rua_codigo")
                    ->where("pes.pessoa =?",$pessoa);
        return $this->fetchAll($sql);
    }
    
    // Confere se a sequence do código de pessoa foi inserida ou não
    public function confereInsPessoa($codPes){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from("aise.pessoa",array("COUNT(pessoa) AS numRegistro"))
                    ->where("pessoa =?",$codPes);
        return $this->fetchRow($sql);
    }
        /**
	 * Buscar os Pessoa
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE) {
		/*if ($term)
			$busca = "nome ilike retira_acentos('%$term%')";*/
            
                $busca = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from("aise.pessoa")
                            ->where("nome ilike retira_acentos('%$term%')")
                            ->where("pessoa not in (select pessoa from pessoa_paciente)");
                $all = $this->fetchAll($busca, "nome");

		$out = array();
		foreach ($all as $pessoa) {
                        $data = $pessoa->toArray();
			$out [] = array(
				"id" => $pessoa->pessoa,
				"label" => trim($pessoa->nome),
				"data" => $data
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("usu_codigo" => "0", "usu_mae" => "", "datanascimento" => "", "cnpj_cpf" => "")
			);
		}
		return $out;
	}


}


