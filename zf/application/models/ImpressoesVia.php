<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ImpressoesVia extends Elotech_Db_Table_Abstract {

    protected $_name = 'print';// nome da tabela do banco
    protected $_primary = 'prt_codigo'; // pk da tabela
    protected $_dependentTables = array();

    public function salvar(array $data) {// esse método é para tratar a ação sendo ela incluir ou alterar
    	
    	$this->emptyToUnset($data);
        //echo "<pre>".print_r($data);exit();
        return parent::salvar($data);// ele retorna para a classe extendida com o "parent" os dados para dentro do "PAI" ele executar a query
    }
      
    public function getVia($usu_codigo){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("prt" => "print"))
                        ->join(array("uni" => "unidade"),"uni.uni_codigo = prt.uni_codigo")
                        ->where("usu_codigo=?",$usu_codigo)
                        ->order('prt_data desc')
                        ->limit('1');
        $io = $this->fetchRow($where);
        return $io;
    }
       

}
