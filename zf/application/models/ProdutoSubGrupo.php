<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ProdutoSubGrupo extends Elotech_Db_Table_Abstract {

    protected $_name = 'produto_subgrupo';
	protected $_primary = 'pros_codigo';

	/**
	 * Salvar o item, insert ou update
	 * @param array $data chave=>valor
	 * @return int Primary Key
	 */
    public function getSubGrupos(){
                // $where = $this->select(FALSE)
                //                         ->setIntegrityCheck()
                //                         ->from(array("pros"=>"produto_setor"),array("pros_codigo","pros_descricao"))
                //                         ->order(array("pros_codigo DESC"))
                //                         ->limit(15);
                return $this->fetchAll();
//        return $this->fetchAll();
    }
}
