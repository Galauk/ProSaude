<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_PrescricaoEnfermagem extends Elotech_Db_Table_Abstract {

    protected $_name = 'prescricao_enfermagem';
    protected $_primary = 'pres_codigo';
  

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }

    public function buscar($lei_codigo=FALSE, $usu_codigo=FALSE){
		if(!$usu_codigo && !$lei_codigo)
			throw new Zend_Validate_Exception( "É preciso informar o código do leito, ou o código do paciente" );
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("lei"=>"leito"))
				->joinLeft(array("pac"=>"paciente_leito"),"pac.lei_codigo=lei.lei_codigo","usu_codigo");
		
		if($usu_codigo)
			$where->where("usu_codigo=?",$usu_codigo);
		
		if($lei_codigo)
			$where->where("lei.lei_codigo=?",$lei_codigo);
		
		return $this->fetchRow($where);
	}
        
    public function buscarLeitoLivre($qua_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("lei"=>"leito"))
                      ->where("lei_codigo not in (select pl.lei_codigo 
                                                    from paciente_leito pl
                                                    join internacao_observacao io
                                                      on io.io_codigo = pl.io_codigo
                                                   where io_situacao_internacao = 2)")
                      ->where("qua_codigo=?",$qua_codigo);
        return $this->fetchRow($where);
    }

}
