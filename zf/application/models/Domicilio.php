<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Domicilio extends Elotech_Db_Table_Abstract {

    protected $_name = 'domicilio';
    protected $_primary = 'dom_codigo';
    protected $_sequence = 'seq_dom_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($data);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($data);
    }

    public function getQtdCodDomicilioDuplicado($dadosDomicilio) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), "dom_codigo")
                ->join(array("rua"), "dom.rua_codigo=rua.rua_codigo")
                ->where("dom.rua_codigo =?", $dadosDomicilio["rua_codigo"])
                ->where("dom.dom_numero =?", $dadosDomicilio["dom_numero"])
                ->where("dom.co_tipo_domicilio =?", $dadosDomicilio["co_tipo_domicilio"])
                ->where("dom.bai_codigo=?", $dadosDomicilio["bai_codigo"]);

        if ($dadosDomicilio["usu_codigo_responsavel"])
            $sql->where("dom.usu_codigo_responsavel=?", $dadosDomicilio["usu_codigo_responsavel"]);
        return $this->fetchRow($sql);
    }

    public function salvarDomicilio($dadosDomicilio) {
        // echo "<pre>";print_r($dadosDomicilio);die();
        $this->emptyToUnset($dadosDomicilio, FALSE);
        try {
            return parent::salvar($dadosDomicilio);
        } catch (Exception $exc) {
            //throw new Zend_Validate_Exception($exc->getMessage());
            throw new Zend_Validate_Exception("Falha ao cadastrar domicilio: " . $exc->getMessage());
        }
    }

    private function verificaSeJaExiste($rua_codigo = FALSE, $dom_numero = FALSE, $dom_complemento = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), "count(*) as qtde")
                ->where("dom_numero = $dom_numero")
                ->where("rua_codigo = $rua_codigo")
                ->where("dom_complemento = $dom_complemento");
        die($sql);
        $query = $this->fetchRow($sql);
        if ($query->qrde > 1) {
            return FALSE;
        } else {
            return true;
        }
    }

    public function buscarNumerosDeDomicilioPorEndereco($rua_codigo, $rua_cep, $rua_bairro, $dom_numero, $co_tipo_logradouro, $cid_codigo, $rua_nome, $usu_codigo_responsavel) {
        
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("dom" => "domicilio"), array("dom_numero", "dom_codigo"))
                ->joinLeft(array("bai" => "bairro"), "dom.bai_codigo=bai.bai_codigo", array("rua_bairro" => "bai_nome", "bai_codigo"))
                ->join(array("rua"), "dom.rua_codigo=rua.rua_codigo", array("rua_nome", "rua_cep", "rua_codigo", "co_tipo_logradouro"))
                ->joinLeft(array("tp_log" => "tb_ms_tipo_logradouro"), "tp_log.co_tipo_logradouro=rua.co_tipo_logradouro")
                ->joinLeft(array("usu" => "usuario"), "dom.usu_codigo_responsavel=usu.usu_codigo", array("usu_nome", "usu_codigo"))
                ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                ->joinLeft(array("c_dis" => "cidade"), "c_dis.cid_codigo=dis.cid_codigo", array("cid_nome_dis" => "cid_nome","cid_codigo_dis" => "cid_codigo"))
                ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=bai.cid_codigo", array("cid_nome","cid_codigo"))
                ->order(array("dom_numero", "rua_nome", "rua_bairro"))
                ->limit(0);

        if ($rua_codigo) {
            $where->where("rua.rua_codigo = $rua_codigo");
        }

        if (empty($rua_codigo) && $rua_nome) {
            $where->where("rua_nome ilike '%$rua_nome%'");
        }

        if ($rua_cep) {
            $where->where("replace(replace( rua_cep, '.', ''),'-','') = '$rua_cep' ");
        }
        if ($rua_bairro) {
            $where->where("bai_nome ilike '%$rua_bairro%'");
        }
        if ($dom_numero != "") {
            if ($dom_numero == "S/N")
                $dom_numero = 0;
            $where->where("dom_numero = $dom_numero");
        }

        if ($co_tipo_logradouro) {
            $where->where("rua.co_tipo_logradouro = '$co_tipo_logradouro'");
        }

//            if($cid_codigo){
//                $where->where("cid.cid_codigo=$cid_codigo");
//            }

        if ($usu_codigo_responsavel) {
            $where->where("usu_codigo_responsavel=$usu_codigo_responsavel");
        }
        return $this->fetchAll($where);
    }

    public function buscaDomicilio($term) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("d" => "domicilio"), array("dom_codigo", "dom_numero"))
                ->join(array("r" => "rua"), "d.rua_codigo=r.rua_codigo", array("rua_nome", "rua_cep", "rua_bairro"))
                ->where("r.rua_nome ilike retira_acentos('%$term%')")
                ->orwhere("d.dom_numero::varchar ilike '%$term%'")
                ->order("dom_numero")
                ->order("rua_nome");
        $all = $this->fetchAll($sql);
        $out = array();
        foreach ($all as $usu) {
            $data = $usu->toArray();
            $out [] = array(
                "id" => $usu->dom_codigo,
                "label" => $usu->rua_nome,
                "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("dom_codigo" => "0", "dom_numero" => "", "rua_nome" => "", "rua_cep" => "", "rua_bairro" => "")
            );
        }
        return $out;
    }

    /**
     * Atualiza todas os domicilios, alterando sua rua.
     * Método usado para tirar a duplicação de ruas
     * @see Application_Model_Rua::removerDuplicacoes()
     * @param array|int $de
     * @param int $para 
     * @return int Número de linhas atualizadas
     */
    public function atualizarRua($de, $para) {
        $de = (array) $de;

        $data = array("rua_codigo" => $para);
        $where = $this->select()->where("rua_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        return $this->update($data, $where);
    }

    public function getEnderecoPorUsuario($usu_codigo = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"))
                ->join("rua", "rua.rua_codigo=dom.rua_codigo")
                ->join(array("usu" => "usuario"), "usu.dom_codigo=dom.dom_codigo", "")
                ->where("usu.usu_codigo=$usu_codigo");

        // die($where);

        return $this->fetchRow($where);
    }

    public function getDomicilioPsf($dom_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), array("usr_codigo", "uni_codigo", "cod_equipe"))
                ->join(array("tcd" => "tb_cds_domicilio_resposta"), "tcd.co_cds_cad_domiciliar=dom.dom_codigo")
                ->join(array("tp" => "tb_pergunta"), "tcd.co_pergunta=tp.co_seq_pergunta", "tp_pergunta")
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=dom.uni_codigo", array("uni_desc"))
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=dom.usr_codigo", array("usr_nome"))
                ->joinLeft(array("te" => "tb_equipe"), "te.co_seq_equipe=dom.cod_equipe", array("no_equipe"))
                ->where("dom_codigo=$dom_codigo");
        return $this->fetchAll($where);
    }

    public function getHeaderCadDomiciliar($dom_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), array("usr_codigo", "uni_codigo", "cod_equipe"))
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=dom.uni_codigo", array("uni_desc"))
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=dom.usr_codigo", array("usr_nome"))
                ->joinLeft(array("te" => "tb_equipe"), "te.co_seq_equipe=dom.cod_equipe", array("no_equipe"))
                ->where("dom_codigo=$dom_codigo");
        //die($where);
        return $this->fetchRow($where);
    }

    public function getQtdMoradores($dom_codigo = FALSE) {
        $sql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), array("COUNT(dom.dom_codigo) AS qtdMorador"))
                ->join(array("usu" => "usuario"), "dom.dom_codigo=usu.dom_codigo", array(""))
                ->where("dom.dom_codigo=$dom_codigo");
        return $this->fetchRow($sql);
    }

    public function verificaVinculo($rua_codigo, $dom_numero, $codigoResponsavelFamiliar, $dom_complemento) {
        // die($codigoResponsavelFamiliar);
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"), "count(*) as qtde");
        
                if($dom_numero == 0){
                    $x = 999999;
                }else{
                    $x = $dom_numero;
                }
                
                //pesquisa somente o resposnsavel, com os parametros do loogradouro e numero null
                if(!$rua_codigo && !$dom_numero && !$dom_complemento && $codigoResponsavelFamiliar){
                    $where->where("usu_codigo_responsavel= $codigoResponsavelFamiliar");
                }
                //pesquisa com todos os parametros não null
                if($rua_codigo && ($dom_numero || $dom_numero == 0 ) && $dom_complemento && $codigoResponsavelFamiliar){
                    $where->where("(rua_codigo = $rua_codigo and dom_numero = $x and dom_complemento = '$dom_complemento') or usu_codigo_responsavel= $codigoResponsavelFamiliar");
                }
                //pesquisa com todos os parametros não null menos complemento
                if($rua_codigo && ($dom_numero || $dom_numero == 0 ) && !$dom_complemento && $codigoResponsavelFamiliar){
                    $where->where("(rua_codigo = $rua_codigo and dom_numero = $x and dom_complemento is null) or usu_codigo_responsavel= $codigoResponsavelFamiliar");
                }
                //pesquisa so o logradouro com os dados do resposnsavel null com complemento
                if($rua_codigo && ($dom_numero || $dom_numero == 0 )  && $dom_complemento && !$codigoResponsavelFamiliar ){
                    $where->where("rua_codigo = $rua_codigo and dom_numero = $x and dom_complemento = '$dom_complemento'");
                }
                //pesquisa so o logradouro com os dados do resposnsavel null sem complemento
                if($rua_codigo && ($dom_numero || $dom_numero == 0 )&& !$dom_complemento &&!$codigoResponsavelFamiliar ){
                    $where->where("rua_codigo = $rua_codigo and dom_numero = $x  and dom_complemento is null");
                }
                
                // die($where);

        return $this->fetchRow($where);
    }

    public function getDomicilio($dom_codigo = FALSE) {
        // $sql = $this->select(FALSE)
        //         ->setIntegrityCheck(FALSE)
        //         ->from(array("dom" => "domicilio"))
        //         ->join("rua", "rua.rua_codigo=dom.rua_codigo", array("rua_nome" => "(tpl.ds_tipo_logradouro || ' ' || rua.rua_nome)", "rua_cep"))
        //         ->join(array("tpl" => "tb_ms_tipo_logradouro"), "tpl.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro")
        //         ->join(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", array("bai_codigo", "bai_nome"))
        //         ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=bai.cid_codigo", array("cid_nome"))
        //         ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
        //         ->joinLeft(array("cid2" => "cidade"), "cid2.cid_codigo=dis.cid_codigo", array("localidade" => "(cid2.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"))
        //         ->joinLeft(array("usu" => "usuario"), "usu.usu_codigo=dom.tcadf_prontuario_familiar", "usu_nome")
        //         ->where("dom.dom_codigo=$dom_codigo");

        // die($sql);
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("dom" => "domicilio"))
                ->join("rua", "rua.rua_codigo=dom.rua_codigo", array("rua_nome" => "(tpl.ds_tipo_logradouro || ' ' || rua.rua_nome)", "rua_cep"))
                ->join(array("tpl" => "tb_ms_tipo_logradouro"), "tpl.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro")
                ->join(array("bai" => "bairro"), "bai.bai_codigo=rua.bai_codigo", array("bai_codigo", "bai_nome"))
                ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=bai.cid_codigo", array("cid_nome"))
                ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                ->joinLeft(array("cid2" => "cidade"), "cid2.cid_codigo=dis.cid_codigo", array("localidade" => "(cid2.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"))
                ->joinLeft(array("usu" => "usuario"), "usu.usu_codigo=dom.usu_codigo_responsavel", "usu_nome")
                ->where("dom.dom_codigo=$dom_codigo");

                // die($sql);


        return $this->fetchRow($sql);

    }
    
    public function removeResponsavel($usu_codigo=FALSE){
            $data = array(
                    'usu_codigo_responsavel'=> null
                );
            $where = $this->select()->where("usu_codigo_responsavel=?", $usu_codigo)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];  
            return $this->update($data, $where);
    }
    
    public function deletaComplementoDoDomicilio($dom_codigo){        
        $data = array(
                    'dom_complemento'=> null
                );
            $where = $this->select()->where("dom_codigo=?", $dom_codigo)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];  
            return $this->update($data, $where);
        
    }
    public function getPacientePorArea($data_inicial=FALSE,$data_final=FALSE,$usr_codigo=FALSE,$ine=FALSE){        
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("vw" => "vw_rel_domicilio"));
                if ($data_inicial) {
                    $where->where("dom_data_cadastro >= '{$data_inicial}'");
                }
                if ($data_final) {
                    $where->where("dom_data_cadastro <= '{$data_final}'");
                }
                if ($usr_codigo) {
                    $where->where("usr_codigo = {$usr_codigo}");
                }
                if ($ine){
                    $where->where("ine = '{$ine}'");
                }
//        die($where);
        return $this->fetchAll($where);
        
    }

}
