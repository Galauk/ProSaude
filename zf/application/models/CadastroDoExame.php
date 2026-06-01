<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_CadastroDoExame extends Elotech_Db_Table_Abstract {

    protected $_name = 'cadastrodoexame';
	protected $_primary = 'cad_exame';

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
	public function getListaColetados($usu_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE){
		if(!$usu_codigo)
			$usu_codigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("c"=>"cadastrodoexame"),array("cad_exame"))
				->join(array("usr"=>"usuarios"),"usr.usr_codigo=c.med_codigo","usr_nome")
				->join(array("med"=>"medico"),"med.med_codigo=c.labm_codigo",array("lab_nome"=>"med_nome"))
				->join(array("i"=>"itensdoexame"),"i.cad_exame=c.cad_exame",array("itx_codigo","itx_status"))
				->join(array("proc"=>"procedimento"),"proc.proc_codigo=i.proc_codigo",array("proc_codigo", "proc_nome"))
				->join(array("mlz"=>"materialdeanalise"),"mlz.itx_codigo=i.itx_codigo","mlz_datadacoleta")
				->where("c.usu_codigo=?",$usu_codigo)
				->order("cad_previsaoentrega");
		
		if($data_inicial)
			$where->where ("mlz_datadacoleta >= ?", $data_inicial);
		
		if($data_final)
			$where->where ("mlz_datadacoleta <= ?", $data_final);
		die($where);
		return $this->fetchAll($where);
	}

}
