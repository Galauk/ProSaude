<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PostoEnfermagem extends Elotech_Db_Table_Abstract {

    protected $_name = 'posto_enfermagem';// nome da tabela do banco
    protected $_primary = 'pe_codigo'; // pk da tabela
    protected $_dependentTables = array();

    public function salvar(array $data) {// esse método é para tratar a ação sendo ela incluir ou alterar
		
        
    	$this->addRealName(array(
    		"pe_descricao" => "descrição",
    		"pe_observacao" => "observação"
    	)); // isso serve para deixar bonitinho quando der erro por falta de um campo. De: 'O campo pe_descricao deve ser preenchido' para "O campo descrição deve ser preenchido"

    	$this->valoresPadrao($data);
    	
    	if(empty($data['pe_codigo'])){
    		$this->notEmpty(array("pe_descricao","ate_codigo"),$data);
    		$data['pe_status'] = "A";
    	}
    	
    	$this->emptyToUnset($data);
    	$this->minLength(array("pe_descricao"=>"10","pe_observacao" => 3),$data, array("pe_observacao"=>true));// tudo eu passo como array nas validações

    	// verifica se há procedimentos
    	if(!empty($data['proc_codigo'])){
    		
    		$tbPat = new Application_Model_ProcedimentoAtendimento();
    		$dados = array(
    			"pe_codigo" => $data['pe_codigo'],
    			"proc_codigo" => $data['proc_codigo']
    		);
    		$tbPat->salvar($dados);
                
    		unset($data['proc_codigo']);
    	}
    	
    	
        return parent::salvar($data);// ele retorna para a classe extendida com o "parent" os dados para dentro do "PAI" ele executar a query
    }
    
    private function valoresPadrao(&$data){
    	if(empty($data['ate_codigo']) || !isset($data['ate_codigo'])){
    		$tbAte = new Application_Model_Atendimento();
    		$ate = $tbAte->temAtendimento();
    		$data['ate_codigo'] = $ate->ate_codigo;
    	}
    }
    
    public function buscarAtual(){
    	$tbAte = new Application_Model_Atendimento();
    	$ate = $tbAte->temAtendimento(); // descobre o atendimento que está na Session. 
    	return $this->fetchRow("ate_codigo=".$ate->ate_codigo); // descobre o codigo do atendimento queestá na Session
    }
    
    public function buscar($pe_codigo=FALSE){
    	if(!$pe_codigo)
    		return $this->buscarAtual();
    		
    	return $this->fetchRow("pe_codigo = $pe_codigo");
    }
    
    public function getLista($uni_codigo=FALSE,$posto_enfermagem=FALSE){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pe"=>"posto_enfermagem"))
				->join(array("ate"=>"atendimento"),"ate.ate_codigo=pe.ate_codigo","")
				->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo")
				->join(array("usu"=>"usuario"),"usu.usu_codigo=ate.usu_codigo",array("usu_codigo","usu_nome","usu_datanasc","usu_mae","usu_end_cidade"))
				->where("pe_status=?","A")
                                ->where("age.uni_codigo=?",$uni_codigo)
				->where("age.age_atendido=?","A")
				->order("pe_codigo");
                
                
                
		return $this->fetchAll($where);
    }
    
    public function finalizar($pe_codigo){
		$pe = $this->fetchRow("pe_codigo=" . $pe_codigo);
		$pe->pe_status = "E";
		return $pe->save();		    	
    }
}
