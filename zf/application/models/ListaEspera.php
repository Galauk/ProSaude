<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ListaEspera extends Elotech_Db_Table_Abstract {

    protected $_name = 'lista_espera';
    protected $_primary = 'lie_codigo';
    protected $_dependentTables = array();

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert)
	 */
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }

    public function getDadosLista($tipo){
      $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("le" => "listaespera"), array("to_char(dt_entrada,'dd/mm/YYYY') as data_entrada", "to_char(atendido_data_agenda,'dd/mm/YYYY') as data_agendada", "status_espera", "id_nivelurgencia"))
                      ->join(array("tle" => "tipo_listaespera"), "tle.tle_codigo = le.tle_codigo", "tle.tle_nome")
                      ->join(array("usu" => "usuario"), "usu.usu_codigo=le.usu_codigo", "usu.usu_nome")
                      ->join(array("pe" => "procedimento"), "pe.proc_codigo=le.necessidade_sforma", "pe.proc_nome")
                      ->join(array("m" => "medico"), "m.med_codigo=le.med_codigo_solicitante", "m.med_nome")
                      ->where("le.tle_codigo=?", $tipo)
                      ->where("le.status_espera is null or le.status_espera = 'A'")
                      ->order("le.dt_entrada");

      //die($where->__toString());
      return $this->fetchAll($where);
    }
}
