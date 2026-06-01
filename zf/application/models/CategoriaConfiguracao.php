<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_CategoriaConfiguracao extends Elotech_Db_Table_Abstract {

	protected $_name = 'categoria_configuracao';
	protected $_primary = 'cac_codigo';

	const STRING = 1;
	const BOOL = 2;
	const INT = 3;
	const DATA = 4;

	

	/**
	 * Busca uma configuração no banco de dados
	 * @param string $chave
	 * @return mixed 
	 */
	public function getCategorias(){
            return $this->fetchAll();
        }

}
