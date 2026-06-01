<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PacienteLeito extends Elotech_Db_Table_Abstract {

    protected $_name = 'paciente_leito';// nome da tabela do banco
    protected $_primary = 'pac_leito'; // pk da tabela
    protected $_dependentTables = array();

    public function salvar(array $data) {// esse método é para tratar a ação sendo ela incluir ou alterar
    	$this->addRealName(array(
    		"pac_leito" => "codigo",
                "usu_codigo" => "codigo_usu",
                "lei_codigo" => "codigo_leito"
    	)); // isso serve para deixar bonitinho quando der erro por falta de um campo. De: 'O campo pe_descricao deve ser preenchido' para "O campo descrição deve ser preenchido"
    	

    	$this->emptyToUnset($data);

        return parent::salvar($data);// ele retorna para a classe extendida com o "parent" os dados para dentro do "PAI" ele executar a query
    }
    
    public function excluir($io_codigo) {
        return $this->delete("io_codigo=$io_codigo");
    }
    
    public function getLeitoInternado($io_codigo=FALSE){
        $where = $this->select(FALSE)
                     ->setIntegrityCheck(FALSE)
                     ->from(array("pl"=>"paciente_leito"))
                     ->where("io_codigo=$io_codigo");
        return $this->fetchRow($where);
    }
    
    
 
    

}
