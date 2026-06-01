<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Plantao extends Elotech_Db_Table_Abstract {

    protected $_name = 'escala_plantao';
    protected $_primary = 'escpla_codigo';
    protected $_sequence = 'seq_escpla_codigo';
    protected $_referenceMap = array(
        'Medico' => array(
            'columns' => 'med_codigo',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        ),
        'Unidade' => array(
            'columns' => 'uni_codigo',
            'refTableClass' => 'Unidade',
            'refColumns' => 'uni_codigo'
        ),
        'UsrCad' => array(
            'columns' => 'usr_codigo_cad',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        ),
        'UsrAlt' => array(
            'columns' => 'usr_codigo_alt',
            'refTableClass' => 'Usuarios',
            'refColumns' => 'usr_codigo'
        )
    );

    public function salvar(array $data) {
        $tbUsr = new Application_Model_Usuarios();
        if (!empty($data['escpla_codigo'])) {
            $data['dt_atualizacao'] = date("Y-m-d H:i:s");
            $data['usr_codigo_alt'] = $tbUsr->getUsrAtual()->usr_codigo;
        } else {
            $data['dt_cadastro'] = date("Y-m-d H:i:s");
            $data['usr_codigo_cad'] = $tbUsr->getUsrAtual()->usr_codigo;
        }

        if(!$data['escpla_codigo']){
            $this->notEmpty(array("uni_codigo","escpla_data","escpla_hora_inicio","escpla_hora_fim"), $data);
        }

        $this->emptyToUnset($data);

        try {
            $escpla_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o plantão: ".$exc->getMessage());
        }
        return $escpla_codigo;
    }

    public function salvarPlantao($data){
        try {
            $escpla_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o plantão: ".$exc->getMessage());
        }
        return $escpla_codigo;
    }

    static public function cancelarPlantaoAtual() {
        $_p = new Zend_Session_Namespace("escala");
        $_p->unsetAll();
    }

    public function iniciar($escpla_codigo) {
        $_p = new Zend_Session_Namespace("escala");
    }

    public function finalizar($escpla_codigo=FALSE) {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
    }

    public function calculaDataFinal(&$data_inicial, $fimDoMes=FALSE) {
        if ($fimDoMes) {
                list($y, $m, $d) = explode("-", $data_inicial);
                $mk = mktime(0, 0, 0, $m, $d, $y);
                return "$y-$m-" . date("t", $mk);
        }

        $tbConf = new Application_Model_Configuracao();
        $dias = $tbConf->getConfig('AGENDA_MOSTRAR_N_OPCOES');
        $dtRetro = $tbConf->getConfig('AGENDA_EXAME_DT_RETROATIVA');

        list($y, $m, $d) = explode("-", $data_inicial);

        if(empty($dtRetro)) {
            if ((int) "$y$m$d" < (int) date("Ymd")) {
                    $data_inicial = date("Y-m-d");
                    list($y, $m, $d) = explode("-", $data_inicial);
            }
        }

        //exit;
        $mk = mktime(0, 0, 0, $m, $d + $dias - 1, $y);
        return date("Y-m-d", $mk);
    }

    public function getVagas($data_inicial, $data_final, $med_codigo) {
        $tbFun = new Application_Model_Funcoes();
        $where = $this->select()
                   ->setIntegrityCheck(FALSE)
                   ->from(array("escpla"=>"escala_plantao"),array("escpla_data", "escpla_hora_inicio", "escpla_hora_fim"))
                   ->where("med_codigo=?",$med_codigo)
                   ->where("escpla_data>='$data_inicial'")
                   ->where("escpla_data<='$data_final'");
        $dados = $this->fetchAll($where)->toArray();
        // Cria um array de datas entre as data inicial e a data final
        $arrDatas = $tbFun->datasToArray($data_inicial, $data_final);
        // Função que pega o número de vagas e joga pra data em que será realizado no plantão
        $datasResult = array();
        foreach($arrDatas as $data){
            $atendeQueDia = $tbFun->diaSemana($data);
            $datasResult[$data] = $atendeQueDia;
        }
        return $datasResult;
    }

/**
     * Exclusão Lógica;
     * @param type $conv_codigo
     * @return type
     */
    public function excluir($escpla_codigo) {
            $item = $this->fetchRow("escpla_codigo=$escpla_codigo");
             if ($item)
                 $item->delete();
            return true;
    }

    public function verificaSeTemAgendamento($coni_codigo=FALSE,$data=FALSE,$hora_inicio=FALSE,$hora_fim=FALSE){
        $sql = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("escpla"=>"escala_plantao"),"count(escpla_codigo) as quantidade")
                    ->where("med_codigo=?",$coni_codigo)
                    ->where("escpla_data=?",$data)
                    ->where("escpla_hora_inicio=?",$hora_inicio)
                    ->where("escpla_hora_fim=?",$hora_fim);
        // die($sql);
        return $this->fetchRow($sql);
    }

}
