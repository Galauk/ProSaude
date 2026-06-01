<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Geladeira extends Elotech_Db_Table_Abstract {

	protected $_name = 'geladeira';
	protected $_primary = 'gel_codigo';
	/**
	 * Salvar o item, insert ou update
	 * @param array $data Array, chave=>valor
	 * @return int Primary Key
	 */
	public function salvar(array $data) {
            //echo "<pre>".print_r($data,1);exit;
            $tbUsr = new Application_Model_Usuarios();
            $this->addRealName(array(
    		"set_codigo" => "Setor",
    		"get_marca" => "Descrição"
            ));
            $data["gel_data_cadastro"] = "NOW()";
            $data["usr_codigo"] = $tbUsr->getUsrAtual()->usr_codigo;
            
            $this->notEmpty(array("set_codigo"), $data);
            $this->emptyToUnset($data);
            return parent::salvar($data);
	}

	/**
	 * Busca todas as geladeiras	
	 * @return Zend_Db_Table_Row_Abstract 
	 */
	public function getGeladeiras() {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gel" => "geladeira"), array("gel_codigo", "gel_marca", "gel_minima", "gel_maxima", "gel_patrimonio"))
				->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=gel.usr_codigo", array("usr_codigo", "usr_nome"))
				->join(array("set" => "setor"), "gel.set_codigo=set.set_codigo", array("set_nome","set_codigo"))
                                ->order("gel_codigo DESC");

		
               return $this->fetchAll($where);
	}
        /**
	 * Busca os dados de uma geladeira	
         * @param int $gel_codigo Código da geladeira
	 * @return Zend_Db_Table_Row_Abstract 
	 */
        public function getGeladeira($gel_codigo) {              
              $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gel" => "geladeira"), array("gel_codigo", "gel_marca", "gel_minima", "gel_maxima", "gel_patrimonio"))				
				->join(array("set" => "setor"), "gel.set_codigo=set.set_codigo", array("set_nome","set_codigo"))                            
                                ->where("gel.gel_codigo=?", $gel_codigo);
             
              return $this->fetchRow($where);
	}
        
            /**
	 * Exclui uma geladeira	
         * @param int $gel_codigo Código da geladeira
	 * @return Zend_Db_Table_Row_Abstract 
	 */
        public function excluir($gel_codigo=FALSE) {
		$item = $this->fetchRow("gel_codigo=$gel_codigo");
		if ($item) {
			$item->delete();
		}
	}
        /**
	 * Busca geladeira	
         * @param int $dados Dados poder ser o nome do setor ou a descricai da geladeira
	 * @return Zend_Db_Table_Row_Abstract 
	 */
        public function pesquisar($dados=FALSE, $limit=FALSE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gel" => "geladeira"), array("gel_codigo", "gel_marca", "gel_minima", "gel_maxima", "gel_patrimonio"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=gel.usr_codigo", array("usr_codigo", "usr_nome"))
				->join(array("set" => "setor"), "gel.set_codigo=set.set_codigo", array("set_nome","set_codigo"));			
		if (is_string($dados))
			$where->where("set_nome ilike '%$dados%' or gel_marca ilike '%$dados%'");
		if ($limit) {
			$where->limit(15);
		}

		return $this->fetchAll($where);
	}
        /**
	 * Busca geladeira por setor	
         * @param int $set_codigo Código do setor
	 * @return Zend_Db_Table_Row_Abstract 
	 */
        public function getGeladeiraPorSetor($set_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gel" => "geladeira"), array("gel.gel_codigo", "gel_marca", "gel_minima", "gel_maxima", "gel_patrimonio"))				
				->join(array("set" => "setor"), "gel.set_codigo=set.set_codigo", array("set_nome","set_codigo"))
                                ->joinLeft(array("temp" => "temperatura_geladeira"),"gel.gel_codigo = temp.gel_codigo",array("temp_codigo","temp_minima","temp_maxima","temp_momento","temp_data",""))
                                ->where("set.set_codigo=?", $set_codigo);
                
               //die($where);

		return $this->fetchAll($where);
	}
          /**
	 * Busca geladeira por setor	
         * @param int $set_codigo Código do setor
         * @param int $periodo Código de periodo 0 = 
	 * @return Zend_Db_Table_Row_Abstract 
	 */
        public function getGeladeiraPorPeriodo($set_codigo,$periodo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gel" => "geladeira"), array("gel_codigo", "gel_marca", "gel_minima", "gel_maxima", "gel_patrimonio"))				
				->join(array("set" => "setor"), "gel.set_codigo=set.set_codigo", array("set_nome","set_codigo"))			
                                ->where("set.set_codigo=?", $set_codigo);
                
               // die($where);

		return $this->fetchAll($where);
	}
        
        
}
