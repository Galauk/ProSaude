<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_AgendamentoExterno extends Elotech_Db_Table_Abstract {

    protected $_name = 'agendamento_externo';
    protected $_primary = 'agee_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {

        $nomes = array(
            "med_codigo_prestador" => "unidade de atendimento",
            "proc_codigo" => "agravo",
            "med_codigo" => "médico de destino",
            "usr_codigo_solicitante" => "médico solicitante",
            "med_codigo_solicitante" => "médico solicitante",
            "agee_data" => "data do agendamento",
            "agee_hora" => "hora do agendamento",
            "agee_num_reg" => "número de registro"
        );
        $this->addRealName($nomes);

        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $data['usr_codigo'] = $usr->usr_codigo;
        $data['uni_codigo'] = $usr->uni_codigo;
        $data['agee_data_cad'] = date("Y/m/d");
        $data['agee_hora_cad'] = date("H:i");

        //echo "<pre>".print_r($data,1);exit;

        $this->filterFloat(array("agee_valor"), $data);
        $this->filterDigits(array("agee_situacao", "usu_codigo", "usr_codigo", "usr_codigo_solicitante", "med_codigo", "med_codigo_prestador", "proc_codigo", "esp_codigo"), $data);
        $this->emptyToNull($data);


        if ($data['interno']) {
            $med_ou_usr_solicitante = "usr_codigo_solicitante";
        } else {
            $med_ou_usr_solicitante = "med_codigo_solicitante";
            $data['med_codigo_solicitante'] = $data['usr_codigo_solicitante'];
            unset($data['usr_codigo_solicitante']);
        }

        unset($data['interno']);

        $this->notEmpty(array("usu_codigo", "usr_codigo", "med_codigo_prestador", $med_ou_usr_solicitante), $data); // ,"agee_data","agee_hora"

        if (!in_array($data['agee_situacao'], array(1, 3))) { // se não for "espera" ou "cancelado", precisa informar data e hora
            $this->notEmpty(array("agee_data", "agee_hora"), $data);
        }

        return parent::salvar($data);
    }

    public function getHistorico($usu_codigo = FALSE, $data_inicial = FALSE, $data_final = FALSE) {
        $where = $this->select(FAlSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agee" => "agendamento_externo"), array("agee_codigo", "agee_data", "agee_data_cad", "agee_observacao"))
                ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=agee.esp_codigo", "esp_nome")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=agee.usr_codigo_solicitante", array("usr_solicitante" => "usr_nome"))
                ->joinLeft(array("med3" => "medico"), "med3.med_codigo=agee.med_codigo_solicitante", array("med_solicitante" => "med_nome"))
                ->joinLeft(array("med" => "medico"), "med.med_codigo=agee.med_codigo", array("med_destino" => "med_nome"))
                ->joinLeft(array("med2" => "medico"), "med2.med_codigo=agee.med_codigo_prestador", array("med_prestador" => "med_nome"))
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=agee.uni_codigo", array("uni_desc"))
                ->where("usu_codigo=?", $usu_codigo)
                ->order(array("agee_data DESC", "agee_hora DESC"));
        if ($data_inicial) {
            $where->where("agee_data >='$data_inicial'");
        }
        if ($data_final) {
            $where->where("agee_data <=' $data_final'");
        }
        return $this->fetchAll($where);
    }

    public function buscar($agee_codigo) {
        $where = $this->select(FAlSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("agee" => "agendamento_externo"))
                ->join(array("usu" => "usuario"), "usu.usu_codigo=agee.usu_codigo", array("usu_nome", "usu_cartao_sus", "usu_datanasc", "usu_end_rua"))
                ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=agee.esp_codigo", "esp_nome")
                ->joinLeft(array("proc" => "procedimento"), "proc.proc_codigo=agee.proc_codigo", "proc_nome")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=agee.usr_codigo_solicitante", "usr_nome")
                ->joinLeft(array("med3" => "medico"), "med3.med_codigo=agee.med_codigo_solicitante", "med_nome")
                ->joinLeft(array("med" => "medico"), "med.med_codigo=agee.med_codigo", array("med_destino" => "med_nome"))
                ->join(array("med2" => "medico"), "med2.med_codigo=agee.med_codigo_prestador", array("med_prestador" => "med_nome"))
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=agee.uni_codigo", array("uni_desc"))
                ->where("agee_codigo=?", $agee_codigo)
                ->order(array("agee_data DESC", "agee_hora DESC"));

        // die($where);
        return $this->fetchRow($where);
    }

    public function getDados($agee_codigo) {
        return $this->fetchRow("agee_codigo=$agee_codigo");
    }

    public function imprimir($agee_codigo) {
        $agee = $this->buscar($agee_codigo);
        // dados do paciente
        // echo "<pre>";print_r(error_reporting(E_ALL));die();
        $tbUsu = new Application_Model_Usuario();
        $usu = $tbUsu->find($agee->usu_codigo)->current();

        // echo "<pre>";print_r($usu);die();

        $dados = (object) array_merge($agee->toArray(), $usu->toArray());
        $dados->codigo = $agee->agee_codigo;

        $end = array();
        $end [] = $usu->usu_end_rua;
        $end [] = $usu->usu_end_nr;
        $end [] = $usu->usu_end_compl;
        $end [] = $usu->usu_end_bairro;
        $end [] = $usu->usu_end_cidade;
        foreach ($end as $k => $item) {
            if (empty($item))
                unset($end[$k]);
        }

        $dados->usu_nome = $usu->usu_nome;
        $dados->usu_endereco = implode(", ", $end);
        //$dados->usu_rua = $usu->usu_end_rua;
        $usuEnd = $tbUsu->getInfo($usu->usu_codigo);
        $dados->rua_nome = $usuEnd->rua_nome;
        $dados->rua_bairro = $usuEnd->rua_bairro;
        $dados->rua_numero = $usuEnd->dom_numero;

        // dados da unidade
        $tbUni = new Application_Model_Unidade();
        $uni = $tbUni->find($agee->uni_codigo)->current();

        $dados->uni_desc = $uni->uni_desc;
        $dados->uni_endereco = $uni->uni_endereco;

        // dados da secretaria
        $tbSec = new Application_Model_Secretaria();
        $sec = $tbSec->fetchRow();

        $dados->secretaria = $sec->nome_secretaria;
        $dados->nome_cidade = $sec->nome_cidade;
        // die("passou");
        return $dados;
    }

    public function excluir($agee_codigo) {
        $agee = $this->find($agee_codigo);

        if ($agee)
            $agee->current()->delete();

        return TRUE;
    }

    public function relatorioSintetico($dados) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ae" => "agendamento_externo"), array("distinct(ae.med_codigo_prestador)", "(select count(med_codigo_prestador) from agendamento_externo where med_codigo_prestador = ae.med_codigo_prestador
                    and esp_codigo = $dados[esp_codigo]) as conta"))
                ->join(array("m" => "medico"), "m.med_codigo = ae.med_codigo_prestador", array("med_nome"))
                ->join(array("e" => "especialidade"), "e.esp_codigo = ae.esp_codigo", array(""));

        if ($dados[data_inicial] != "") {
            $where->where("agee_data >= $dados[data_inicial]");
        }
        if ($dados[data_final] != "") {
            $where->where("agee_data <= $dados[data_final]");
        }
        if ($dados[med_codigo_prestador] != "") {
            $where->where("med_codigo_prestador=$dados[med_codigo_prestador]");
        }

        return $this->fetchAll($where);
    }

    public function relatorioAnalitico($dados) {
        echo "<pre>" . print_r($dados, 1);
        die();
    }

    public function atualizaProcedimentoAgendamentoExterno($codigoProcNovo, $codigoProcAnterior) {
        $data = array("proc_codigo" => $codigoProcNovo);
        $where = $this->select()->where("proc_codigo = $codigoProcAnterior")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        //echo"<pre>".print_r($where,1); echo"<pre>".print_r($data,1);die();
        return $this->update($data, $where);
    }
    
    public function listaProcedimentoPorCodigo($codigoProcedimento) {
        $sql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("ae" => "agendamento_externo"), array("ae.proc_codigo"))
                ->where("ae.proc_codigo = $codigoProcedimento");
      //  die($sql);
        return $this->fetchAll($sql);
    }

    public function recuperarTodosOsAgendamenosExternos($uni_codigo){
        $recebeUniCodigo = intval($uni_codigo);
        $sql = $this->getDefaultAdapter()->query(
            "
            SELECT age_ext.agee_codigo,cen.cr_entregue,age_ext.agee_codigo, usu.usu_nome, usu.usu_mae, usu.usu_datanasc, uni.uni_desc, proc.proc_nome, dom.dom_telefone, usu.usu_cartao_sus, age_ext.agee_situacao, esp.esp_nome, age_ext.nivel, usu.usu_fone  
                    FROM agendamento_externo AS age_ext
                INNER JOIN usuario AS usu
                    ON usu.usu_codigo = age_ext.usu_codigo
                INNER JOIN unidade AS uni
                    ON uni.uni_codigo = age_ext.uni_codigo
                INNER JOIN procedimento AS proc
                    ON proc.proc_codigo = age_ext.proc_codigo
                LEFT JOIN centro_de_regulacao as cen
                    ON cen.agendamento_externo_codigo = age_ext.agee_codigo
                LEFT JOIN domicilio as dom 
                    ON dom.dom_codigo = usu.dom_codigo
                LEFT JOIN especialidade as esp
                    ON age_ext.esp_codigo = esp.esp_codigo    
                WHERE uni.uni_codigo = $recebeUniCodigo order by age_ext.agee_codigo desc

            "
        )->fetchAll();
        return $sql;
    }

    public function recuperarTodosOsAgendamenosExternosRegulador($uni_codigo){
        $recebeUniCodigo = intval($uni_codigo);
        $sql = $this->getDefaultAdapter()->query(
            "
            SELECT age_ext.agee_codigo,cen.cr_entregue,age_ext.agee_codigo, usu.usu_nome, usu.usu_mae, usu.usu_datanasc, uni.uni_desc, proc.proc_nome, dom.dom_telefone, usu.usu_cartao_sus, age_ext.agee_situacao, esp.esp_nome, age_ext.nivel, usu.usu_fone  
                    FROM agendamento_externo AS age_ext
                INNER JOIN usuario AS usu
                    ON usu.usu_codigo = age_ext.usu_codigo
                INNER JOIN unidade AS uni
                    ON uni.uni_codigo = age_ext.uni_codigo
                INNER JOIN procedimento AS proc
                    ON proc.proc_codigo = age_ext.proc_codigo
                LEFT JOIN centro_de_regulacao as cen
                    ON cen.agendamento_externo_codigo = age_ext.agee_codigo
                LEFT JOIN domicilio as dom 
                    ON dom.dom_codigo = usu.dom_codigo
                LEFT JOIN especialidade as esp
                    ON age_ext.esp_codigo = esp.esp_codigo    
                order by age_ext.agee_codigo desc

            "
        )->fetchAll();
        return $sql;
    }

}
