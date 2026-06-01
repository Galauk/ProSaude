<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Logon extends Elotech_Db_Table_Abstract {

    protected $_name = 'logon';
    protected $_primary = 'id';

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    public function getDadosPeloUsuario($usr_codigo){
        $where = $this->select(FALSE)
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("l"=>"logon"))
                ->join(array("u"=>"unidade"),"u.uni_codigo=l.uni_codigo")
                ->where("id_login=$usr_codigo"); 
       //die($where);
       //return $where;
       return $this->fetchRow($where);             
   
                
    }




    // criar método para ver quanto tempo faltar
	
	// criar método para adicionar mais tempo

}
