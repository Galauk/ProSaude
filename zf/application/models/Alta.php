<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Alta extends Elotech_Db_Table_Abstract {

    protected $_name = 'internacao_alta';
    protected $_primary = 'alt_codigo';   
    protected $_dependentTables = array();

  
    public function salvar(array $data) {// esse método é para tratar a ação sendo ela incluir ou alterar
	
    	$this->addRealName(array(
    		"alt_codigo" => "Codigo da alta",
                "alt_alta" => "Alta",
                "alt_obito" => "Obito",
                "alt_observacao" => "Observaçao",
    	)); // isso serve para deixar bonitinho quando der erro por falta de um campo. De: 'O campo pe_descricao deve ser preenchido' para "O campo descrição deve ser preenchido"

    	$this->emptyToUnset($data);
    	$this->minLength(array("io_observacao" => 3),$data, array("io_observacao"=>true));// tudo eu passo como array nas validações
        //echo "<pre>".print_r($data);exit();
        return parent::salvar($data);// ele retorna para a classe extendida com o "parent" os dados para dentro do "PAI" ele executar a query
    }
    
    public function getItens() {		
            return $this->fetchAll();
    }


}
