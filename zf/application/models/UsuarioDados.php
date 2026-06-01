<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_UsuarioDados extends Elotech_Db_Table_Abstract
{

    protected $_name = 'usuario_dados';
    protected $_primary = 'usd_codigo';

    public function salvar(array $data)
    {
        try {
            $this->emptyToNull($data);
            $dados_usuario = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar dados da pessoa!" . $exc->getMessage());
        }
        return $dados_usuario;
    }

    public function buscaDadosUsuario($usu_codigo){
	    $sql = $this->select(FALSE)
		    ->setIntegrityCheck(FALSE)
		    ->from(array("usd" => "usuario_dados"))
		    ->where("usd.usu_codigo=?", $usu_codigo);
	    //die($sql);
	    return $this->fetchRow($sql);
    }

}
