<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_UsuariosPermissoes extends Elotech_Db_Table_Abstract {

	protected $_name = 'usuarios_permissoes';
	protected $_primary = 'perus_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data) {
		throw new Zend_Validate_Exception("Este método ainda não possui validações", 1000);
		return parent::salvar($data);
	}

	/**
	 * Traz as permissões do usuário para uma determinada URL
	 * @param int $usr_codigo
	 * @param string $url 
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getPermissoes($url, $usr_codigo=FALSE) {
		if (!$usr_codigo) {
			$tbUsr = new Application_Model_Usuarios();
			try {
				$usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
			} catch (Zend_Exception $e) {

				unset($e); // sonar;
				return FALSE;
			}
		}

		$perm = array(
			"acessar" => "perm_set",
			"inserir" => "nivel_i",
			"editar" => "nivel_a",
			"deletar" => "nivel_d",
			"listar" => "nivel_l",
			"buscar" => "nivel_b"
		);

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("perm" => "permissoes"), "")
				->join(array("perus" => "usuarios_permissoes"), "perus.perm_codigo=perm.perm_codigo", $perm)
				->where("perus.usr_codigo=?", $usr_codigo)
				->where("perm.perm_programa IN (?)", (array) $url);
                
                //die($where);
		return $this->fetchRow($where);
	}

}