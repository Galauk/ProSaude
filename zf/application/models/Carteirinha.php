<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Carteirinha extends Elotech_Db_Table_Abstract {

    protected $_name = 'carteirinha';
    protected $_primary = 'car_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
	/**
	 * Retorna o produto e as doses de cada vacina da carteirinha
	 * @return Zend_Db_Table_Rowset_Abstract 
	 */
	public function carregarCarteirinha(){
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		if(empty($usr->set_codigo)){
			throw new Zend_Validate_Exception("É preciso informar o campo setor no login");
		}
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("car"=>"carteirinha"))
				->join(array("pro"=>"produto"),"pro.pro_codigo=car.pro_codigo","pro_nome")
				->order("pro_nome");
		
                //die($where);
		return $this->fetchAll($where);
	}
    
    public function carregarCarteirinhaCampanha($id){
		$tbUsr = new Application_Model_Usuarios();
		$usr = $tbUsr->getUsrAtual();
		
		if(empty($usr->set_codigo)){
			throw new Zend_Validate_Exception("É preciso informar o campo setor no login");
		}
		
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("car"=>"carteirinha"))
                ->join(array("pro"=>"produto"),"pro.pro_codigo=car.pro_codigo", array("pro_nome", "pro_codigo"))
                ->join(array("cvv"=>"campanha_vacinacao_vacinas"), "cvv.vacina_codigo = pro.pro_codigo", "vacina_codigo")
                ->join(array("cv"=>"campanha_vacinacao"), "cvv.campanha_codigo = cv.cam_vac_codigo", "descricao")
                ->joinLeft(array("sal"=>"saldo"), "cvv.vacina_codigo = sal.pro_codigo", array("sal_qtde", "sal_lote"))
                ->where("cv.cam_vac_codigo=$id")
                // ->where("sal.sal_qtde > 0")
				->order("pro_nome");
		
                // die($where);
		return $this->fetchAll($where);
	}

	/**
	 *  Retorna um array com os pro_codigo que formam a carteirinha
	 *  @return array
	 */
	public function getProdutosDaCarteirinha(){
		$car = $this->carregarCarteirinha();
		$out = array();
		foreach($car as $vacina){
			$out []= $vacina->pro_codigo;
		}
		return $out;
    }
    
	public function getVacinasCarteirinha(){
		$car = $this->carregarCarteirinha();
		$out = array();
		foreach($car as $vacina){
			$out []= array("pro_codigo"=>$vacina->pro_codigo, "pro_nome"=>$vacina->pro_nome);
		}
		return $out;
	}
}
