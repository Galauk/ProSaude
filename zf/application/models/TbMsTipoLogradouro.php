<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbMsTipoLogradouro extends Elotech_Db_Table_Abstract {

	protected $_name = 'tb_ms_tipo_logradouro';
	protected $_primary = 'co_tipo_logradouro';
        
       public function getTiposLogradouro() {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("l" => "tb_ms_tipo_logradouro"), array("co_tipo_logradouro", "ds_tipo_logradouro"))
                            ->order("ds_tipo_logradouro");

            return $this->fetchAll($where);
	}
        
        public function buscar($term=FALSE){
            if(empty($term))
                return false;
            
            $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("tl" => "tb_ms_tipo_logradouro"), array("co_tipo_logradouro","ds_tipo_logradouro"))
				->where("retira_acentos(ds_tipo_logradouro) ilike retira_acentos('$term%')")
				->order("ds_tipo_logradouro");

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $cid) {                     
			$out [] = array(
				"id" => $cid->co_tipo_logradouro,
				"label" => trim($cid->ds_tipo_logradouro),
				"data" => $cid->toArray()
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}

		return $out;
        }
        
       
}
