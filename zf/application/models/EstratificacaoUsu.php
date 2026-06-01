<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_EstratificacaoUsu extends Elotech_Db_Table_Abstract {

    protected $_name = 'estratificacao_usu';
	protected $_primary = 'id_estusu';
    protected $_dependentTables = array();

    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($data);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($data);
    }


    public function recuperaScoreUsuario($usu_codigo){
        
        $recebeUsuCodigo = $usu_codigo;

        $sql = $this->getDefaultAdapter()->query(

            "SELECT est_nomeficha, usu.usu_nome, lista.nivelalto, lista.nivelmedio, lista.nivelbaixo, estra_lista.est_score, to_char(lista.dt_cadastro, 'dd/mm/yyyy') from estratificacao_usu 
                    as estra_lista
                INNER JOIN estratificacao_lista AS lista
                    ON estra_lista.est_listaid = lista.id_estlista

                INNER JOIN usuario as usu
		            ON usu.usu_codigo = estra_lista.est_usu_codigo

                WHERE estra_lista.est_usu_codigo = $recebeUsuCodigo
            "

        )->fetchAll();
        
        return $sql;
    }

}
