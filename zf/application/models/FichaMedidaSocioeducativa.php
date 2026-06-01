<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_FichaMedidaSocioEducativa extends Elotech_Db_Table_Abstract {

    protected $_name = 'ficha_medida_socioeducativa';
    protected $_primary = 'fims_codigo';

    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	

    public function excluir($fims_codigo=FALSE) {
            $item = $this->fetchRow("fims_codigo=$fims_codigo");
            if ($item) {
                    $item->delete();
            }
    }
    
     public function getDadosFicha($usuCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("fim"=>"ficha_medida_socioeducativa"))
                    ->where("fim.usu_codigo =?",$usuCodigo);
        return $this->fetchRow($sql);
    }
    
    public function getDadosBasicoFicha($usuCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("fim"=>"ficha_medida_socioeducativa"))
                    ->join(array("tpf"=>"tipo_ficha"),"fim.tipo_ficha=tpf.tpf_codigo",array("tpf_descricao"))
                    ->where("fim.usu_codigo =?",$usuCodigo);
        return $this->fetchRow($sql);
    }
    
    public function atualizarUsu($de, $para){
		$de = (array)$de;
		
		$data = array("usu_codigo" => $para);
		$where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
		$where = $where[0];
		
		Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
		
		return $this->update($data, $where);
	}
    
}
 