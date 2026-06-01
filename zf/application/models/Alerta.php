<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Alerta extends Elotech_Db_Table_Abstract {

    protected $_name = 'alerta';
    protected $_primary = 'ale_codigo';
    protected $_sequence = 'alerta_ale_codigo_seq';
    protected $_dependentTables = array();

    public function salvar(array $data) {
        
        $tbAge = new Application_Model_Agendamento();
        $age = $tbAge->usuEmAberto();

        $this->addRealName(array("ale_desc" => "descrição"));

        $this->notEmpty(array("ale_desc"), $data);

        if (is_null($data['usr_codigo']) || empty($data['usr_codigo']))
            $data['usr_codigo'] = $age->med_codigo;

        if (is_null($data['usu_codigo']) || empty($data['usu_codigo']))
            $data['usu_codigo'] = $age->usu_codigo;

        if (is_null($data['ale_data']) || empty($data['ale_data']))
            $data['ale_data'] = date("Y-m-d H:i:s");
            
        $this->emptyToUnset($data);
        return parent::salvar($data);
    }

    public function getItens($usu_codigo=FALSE) {
		if(!$usu_codigo){
			$age = Application_Model_Agendamento::usuEmAberto();
			$usu_codigo = $age->usu_codigo;
		}

        return $this->fetchAll("usu_codigo=" . $usu_codigo, "ale_data DESC");
    }

    /**
     * Exclui um alerta
     * O método verifica se o paciente faz parte do atendimento atual
     * @param int $ale_codigo 
     */
    public function excluir($ale_codigo) {

        $item = $this->fetchRow("ale_codigo=$ale_codigo");
        if ($item)
            $item->delete();

        return true;
    }

}
