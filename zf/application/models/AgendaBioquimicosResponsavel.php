<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_AgendaBioquimicosResponsavel extends Elotech_Db_Table_Abstract {

	protected $_name = 'agenda_bioquimicos_responsavel';
	protected $_primary = 'agebr_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data) {
            try {
                $age_codigo = parent::salvar($data);
            } catch (Exception $exc) {
                throw new Zend_Validate_Exception($exc->getMessage());
            }
	}
        
        public function getBioquimicosResponsavel($age_codigo=FALSE,$usr_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("agebr"=>"agenda_bioquimicos_responsavel"),arraY("agebr_codigo","age_codigo","usr_codigo"));
                if ($age_codigo)            
                    $sql->where("agebr.age_codigo =?",$age_codigo);
                if ($usr_codigo)
                    $sql->where("agebr.usr_codigo =?",$usr_codigo);
            return $this->fetchRow($sql);
			die("aaaaaaaaaa");
        }
        
        public function excluirBioquimicosResponsavel($age_codigo=FALSE){
            $item = $this->fetchAll("age_codigo = $age_codigo");
            if ($item)
                foreach($item as $value) {
                    $value->delete();
                }
            return true;
        }
        
}
