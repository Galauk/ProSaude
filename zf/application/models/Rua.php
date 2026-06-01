<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Rua extends Elotech_Db_Table_Abstract {

    protected $_name = 'rua';
    protected $_primary = 'rua_codigo';
    protected $_sequence = 'seq_rua_codigo';

    public function salvar(array $data) {
        $this->emptyToUnset($data);
        $rua_codigo = parent::salvar($data);
        return $rua_codigo; // não pode salvar especialidades;
    }

    /**
     * Buscar as ruas
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscar($term = FALSE) {
        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from("rua", array("rua_codigo", "(ds_tipo_logradouro || ' ' || rua_nome) as rua_nome", "rua_cep"))
                    ->join(array("tp_log" => "tb_ms_tipo_logradouro"), "tp_log.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro")
                    ->where("retira_acentos((ds_tipo_logradouro || ' ' || rua_nome )) ilike retira_acentos('%$term%')", "S")
                    ->join(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", array("bai_codigo", "bai_nome"))
                    ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                    ->joinLeft(array("c_dis" => "cidade"), "c_dis.cid_codigo=dis.cid_codigo", array("cid_nome_dis" => "cid_nome"))
                    ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=bai.cid_codigo", "cid_nome")
                    ->order(array("rua_nome"))
                    ->limit(0);

        // die($where);
        $all = $this->fetchAll($where);
        $out = array();
        foreach ($all as $item) {
            $data = $item->toArray();
            $out [] = array(
                "id" => $item->rua_codigo,
                "label" => $item->rua_nome,
                "data" => array("rua_codigo" => $item->rua_codigo,
                    "rua_cep" => $item->rua_cep,
                    "bai_codigo" => $item->bai_codigo,
                    "bai_nome" => ($item->bai_nome ? $item->bai_nome : "Não possui"),
                    "cid_nome" => ($item->cid_nome ? ($item->cid_nome) : ($item->cid_nome_dis == "" || $item->cid_nome_dis == null ? "Nenhum vinculo de cidade" : $item->cid_nome_dis)),
                    "dis_nome" => ($item->dis_nome ? $item->dis_nome : "Não possui"))
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => "",
                "label" => "Nenhum item encontrado",
                "data" => array("rua_codigo" => "", "rua_nome" => "")
            );
        }

        return $out;
    }

    /**
     * Atualiza todos os domícilios que estão em uma rua duplicada para uma única rua,
     * depois remove-as.
     * @param int $correto
     * @param array $duplicados 
     * @return array quantos registros foram atualizados e quantos foram removidos
     */
    public function removerDuplicacoes($correto, $duplicados) {
        $tbDom = new Application_Model_Domicilio();
        $atualizados = $tbDom->atualizarRua($duplicados, $correto);
        $removidos = $this->remover($duplicados);

        return array($atualizados, $removidos);
    }

    /**
     * Recebe um array de rua_codigo e remove todos
     * @param array $rua_codigo
     * @return int Número de linhas removidas
     */
    public function remover($rua_codigo) {
        $where = $this->select()->where("rua_codigo IN (?)", $rua_codigo)->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        return $this->delete($where);
    }

    public function getQtdCodRuaDuplicada($dadosRua) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("rua", array("rua_codigo"))
                ->where("rua_bairro =?", $dadosRua["rua_bairro"])
                ->where("cid_codigo =?", $dadosRua["cid_codigo"])
                ->where("rua_nome =?", $dadosRua["rua_nome"]);
        //die($sql);
        return $this->fetchRow($sql);
    }

    public function salvarRua($data) {
        // echo "<pre>";print_r($data);die();
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            //throw new Zend_Validate_Exception($exc->getMessage());
            throw new Zend_Validate_Exception("Falha ao realizar o cadastro de rua: " . $exc->getMessage());
        }
    }

    public function getDadosCidadeEstado($codIbge) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("cid" => "cidade"), array("cid_codigo", "uf_sigla"))
                ->where("cid_codigo_ibge = '$codIbge'");
        //die ($sql);
        return $this->fetchRow($sql);
    }

    public function buscarRua($term) {
        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from("rua", array("rua_codigo", "rua_nome", "rua_cep", "rua_bairro"))
                    ->where("retira_acentos(rua_nome) ilike retira_acentos('%$term%')", "S")
                    ->order(array("rua_nome"))
                    ->limit(0);
        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $item) {
            $out [] = array(
                "id" => $item->rua_codigo,
                "label" => trim($item->rua_nome),
                "data" => array("rua_codigo" => $item->rua_codigo, "rua_cep_hidden" => $item->rua_cep, "rua_cep" => $item->rua_cep, "rua_bairro" => $item->rua_bairro, "rua_nome" => $item->rua_nome)
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("rua_codigo" => "", "rua_cep_hidden" => "", "rua_cep" => "", "rua_bairro" => "", "rua_nome" => "")
            );
        }
        return $out;
    }

    public function buscarCep($term) {
        $term = str_replace(".", "", $term);
        $term = str_replace("-", "", $term);
        if ($term)
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from("rua", array("rua_codigo", "rua_nome", "rua_cep", "rua_bairro"))
                    ->where("replace(replace( rua_cep, '.', ''),'-','') ILIKE ('%$term%') ")
                    ->order(array("rua_nome"))
                    ->limit(0);

        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $item) {
            $out [] = array(
                "id" => $item->rua_codigo,
                "label" => trim($item->rua_nome),
                "data" => array("rua_codigo" => $item->rua_codigo, "rua_cep_hidden" => $item->rua_cep, "rua_cep" => $item->rua_cep, "rua_bairro" => $item->rua_bairro, "rua_nome" => $item->rua_nome)
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("rua_codigo" => "", "rua_cep_hidden" => "", "rua_cep" => "", "rua_bairro" => "", "rua_nome" => "")
            );
        }
        return $out;
    }

    public function getRua($id = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("rua")
                ->joinLeft(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", array("bai_codigo", "bai_nome"))
                ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=bai.cid_codigo", "cid_nome")
                ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", array("dis_nome"))
                ->joinLeft(array("cid_dis" => "cidade"), "cid_dis.cid_codigo=dis.cid_codigo", array("localidade" => "(cid_dis.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"))
                ->join(array("tp_log" => "tb_ms_tipo_logradouro"), "tp_log.co_tipo_logradouro=rua.co_tipo_logradouro", array("co_tipo_logradouro", "ds_tipo_logradouro"))
                ->where("rua_codigo=$id");

        //die($where);
        return $this->fetchRow($where);
    }

    public function getRuas() {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("rua")
                ->join(array("tp_log" => "tb_ms_tipo_logradouro"), "tp_log.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro")
                ->joinLeft(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", "bai_nome")
                ->joinLeft(array("cid" => "cidade"), "bai.cid_codigo=cid.cid_codigo", "cid_nome")
                ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                ->joinLeft(array("cid_dis" => "cidade"), "cid_dis.cid_codigo=dis.cid_codigo", array("localidade" => "(cid_dis.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"))
                ->limit(15)
                ->order("rua_codigo desc");

        return $this->fetchAll($where);
    }

    public function pesquisar($dados, $limit = FALSE) {
        
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("rua" => "rua"))
                ->join(array("tpl" => "tb_ms_tipo_logradouro"), "tpl.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro")
                ->joinLeft(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", "bai_nome")
                ->joinLeft(array("cid" => "cidade"), "bai.cid_codigo=cid.cid_codigo", "cid.cid_nome")
                ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                ->joinLeft(array("cid_dis" => "cidade"), "cid_dis.cid_codigo=dis.cid_codigo", array("localidade" => "(cid_dis.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"));
        
        if ($dados) {
            $where->where("rua_nome ilike '%$dados%' or rua_cep ilike '%$dados%' or cid.cid_nome ilike '%$dados%'");
        }

        if ($limit) {
            $where->limit(15);
        }
        
        return $this->fetchAll($where);
    }

    public function excluir($rua_codigo) {
        $item = $this->fetchRow("rua_codigo=$rua_codigo");
        if ($item)
            $item->delete();

        return true;
    }

}
