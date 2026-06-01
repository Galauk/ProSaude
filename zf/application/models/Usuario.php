<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Usuario extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuario';
    protected $_primary = 'usu_codigo';
   // protected $_dependentTables = array('LeitoGrade');
    protected $_sequence = 'seq_usu_codigo';

    public function salvar(array $data) {
        try {
            $this->emptyToNull($data);
            $pessoa = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar pessoa!".$exc->getMessage());
        }
        return $pessoa;
    }

    public function validaCnsDuplicado($cns=FALSE){
        if ($cns) {
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("usu"=>"usuario"),array("COUNT(usu_cartao_sus) AS qtd_sus"))
                        ->where("usu_cartao_sus =?",$cns);
            return $this->fetchRow($sql);
        } else {
            return 0;
        }
    }

    public function listaCadastroDuplicado($dadosPessoa=FALSE,$nome=FALSE){
        $array_paciente = array();
        $i = 1;
        $quantos_nomes = count($nome)." ";
        foreach($nome as $nome_fragmento){
            /*ESTE SQL VAI FAZER COMPARAÇÃO COM PARTES DO NOME + DATA_NASCIMENTO*/
            $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu" => "usuario"),array("COALESCE(to_char(usu_datanasc,'DD/MM/YYYY'),NULL,'-----') AS datanascimento","nome"=>"usu_nome","inativo"=>"(CASE WHEN usu_ativacao='S' THEN 'Ativo' WHEN usu_ativacao='N' THEN 'Inativo' END)","COALESCE(usu_codigo,NULL,0) AS pep_codigo","COALESCE(usu_mae,NULL,'----') AS pep_mae","pep_sexo"=>"usu_sexo","prontuario"=>"usu_prontuario"));

            if($i == 1){
                $sql->where("retira_acentos(usu_nome) ilike retira_acentos('$nome_fragmento%')");
            }else if($i == rtrim($quantos_nomes)){
                $sql->where("retira_acentos(usu_nome) ilike retira_acentos('%$nome_fragmento')");
            }else{
                $sql->where("retira_acentos(usu_nome) ilike retira_acentos('%$nome_fragmento%')");
            }

            $sql->where("usu_datanasc = '$dadosPessoa[usu_datanasc]'");
            foreach($this->fetchAll($sql)->toArray() as $paciente){
                //echo $paciente[nome];
                $array_paciente[$paciente["pep_codigo"]] = array("nome"=>$paciente["nome"],
                                                            "pep_mae"=>$paciente["pep_mae"],
                                                            "datanascimento"=>$paciente["datanascimento"],
                                                            "inativo"=>$paciente["inativo"],
                                                            "prontuario"=>$paciente["prontuario"]);
            }


             /*ESTE SQL VAI FAZER COMPARAÇÃO COM PARTES DO NOME + NOME_MAE*/
            $sqlMae = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu" => "usuario"),array("COALESCE(to_char(usu_datanasc,'DD/MM/YYYY'),NULL,'-----') AS datanascimento","nome"=>"usu_nome","inativo"=>"(CASE WHEN usu_ativacao='S' THEN 'Ativo' WHEN usu_ativacao='N' THEN 'Inativo' END)","COALESCE(usu_codigo,NULL,0) AS pep_codigo","COALESCE(usu_mae,NULL,'----') AS pep_mae","pep_sexo"=>"usu_sexo","prontuario"=>"usu_prontuario"));


            if($i == 1){
                $sqlMae->where("retira_acentos(usu_nome) ilike retira_acentos('$nome_fragmento%')");
            } else if($i == rtrim($quantos_nomes)) {
                $sqlMae->where("retira_acentos(usu_nome) ilike retira_acentos('%$nome_fragmento')");
            } else {
                $sqlMae->where("retira_acentos(usu_nome) ilike retira_acentos('%$nome_fragmento%')");
            }
            
            $sqlMae->where("retira_acentos(usu_mae) ilike retira_acentos('%$dadosPessoa[usu_mae]%')");

            foreach($this->fetchAll($sqlMae)->toArray() as $paciente){
                //echo $paciente[nome];
                $array_paciente[$paciente["pep_codigo"]] = array("nome"=>$paciente["nome"],
                                                            "pep_mae"=>$paciente["pep_mae"],
                                                            "datanascimento"=>$paciente["datanascimento"],
                                                            "inativo"=>$paciente["inativo"],
                                                            "prontuario"=>$paciente["prontuario"]);
            }
            
            $i++;
        }

        return $array_paciente;
    }

	/**
	 * Retorna os dados para o plugin jquery.buscar.js
	 * @param int $usu_codigo
	 * @return Zend_Db_Table_Row_Abstract
	 * @deprecated retirar na versão 3.20.x
	 */
    public function getDados($usu_codigo) {
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("u" => "usuario"), array("usu_codigo","usu_nome", "usu_mae", "usu_datanasc", "usu_prontuario", "usu_sexo", "usu_end_cidade", "usu_pai", "TO_CHAR(AGE(usu_datanasc), 'YY \"ano(s)\" MM \"mes(es)\" DD \"dia(s)\"' ) as idade"))
        ->where("usu_codigo=?", $usu_codigo);

        return $this->fetchRow($where);
    }

    /**
     * Carrega as informações que serão necessárias nas telas de impressão.
     * @param int $usu_codigo
     * @return stdClass
     */
    public function getDadosToPrint($usu_codigo) {
        $dados = new stdClass();

        $usu = $this->find($usu_codigo)->current();

        $end = array();
        $end[] = $usu->usu_end_rua;
        $end[] = $usu->usu_end_nr;
        $end[] = $usu->usu_end_compl;
        $end[] = $usu->usu_end_bairro;
        $end[] = $usu->usu_end_cidade;
        
        foreach ($end as $k => $item) {
            if (empty($item)) {
                unset($end[$k]);
            }
        }

        $dados->usu_nome = $usu->usu_nome;
        $dados->usu_endereco = implode(", ", $end);
        $dados->usu_datanasc = $usu->usu_datanasc;

        return $dados;
    }

    public function validaDataNormal($dat){
        $data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como referência
        $d = $data[0];
        $m = $data[1];
        $y = $data[2];
        //echo "<pre>";print_r($y);die();
        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        if($y == ""){
            return false;
        } else {
            $res = checkdate($m,$d,$y);
            if ($res == 1){
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Buscar os USU's
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscarTipo($term=FALSE,$tipo_de_busca=FALSE) { 
        //die("teste");
        $tbConf = new Application_Model_Configuracao();
        if ($term){
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->reset(Zend_Db_Select::WHERE)
                            ->from(array("usu" => "usuario"), array("usu_codigo","usu_sexo","usu_fone", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario",  "usu_cartao_sus", "usu_bloqueado", "rac_codigo", "cd_nacionalidade", "dom_codigo","usu_esta_gestante", "TO_CHAR(AGE(usu_datanasc), 'YY \"ano(s)\" MM \"mes(es)\" DD \"dia(s)\"') as idade", ""))
                            ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_telefone","dom_numero","(select usu_nome from usuario u2 where usu_codigo = dom.usu_codigo_responsavel) as usu_nome_resp"))
                            ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                            ->joinLeft(array("te"=>"tb_equipe"),"dom.cod_equipe=te.co_seq_equipe",array("nu_ine","ds_area","no_equipe"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome"))
                            ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=dom.bai_codigo", array("rua_bairro" => "bai_nome"))
                            ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                            ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                            ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                            //->joinLeft(array("ate" => "atendimento"), "usu.usu_codigo=ate.usu_codigo", array("ate_peso","ate_altura"))
                            ->where("usu_ativacao=?", "S")
                            ->where("usu_bloqueado=?", "f")
                            ->limit(15);
                             //die($where);
        }
   
            if($tipo_de_busca == "1"){
                $cond1 = $this->select(FALSE)
                            ->where("usu_nome ilike '%$term%'")
                            ->getPart(Zend_Db_Select::WHERE);
                $cond1 = implode(" ", $cond1);
                $where->where($cond1);
                //die($where);
            }
            else if($tipo_de_busca == "2"){
                $cond1 = $this->select(FALSE)
                            ->where("usu_mae ilike '%$term%'")
                            ->getPart(Zend_Db_Select::WHERE);
                $cond1 = implode(" ", $cond1);
                $where->where($cond1);
            }
            else if($tipo_de_busca == "3"){
                $cond1 = $this->select(FALSE)
                            ->where("usu_prontuario ='$term'")
                            ->getPart(Zend_Db_Select::WHERE);
                $cond1 = implode(" ", $cond1);
                $where->where($cond1);
            }
            else if($tipo_de_busca == "4"){
                $cond1 = $this->select(FALSE)
                            ->where("usu_cartao_sus = '$term'")
                            ->getPart(Zend_Db_Select::WHERE);
                $cond1 = implode(" ", $cond1);
                $where->where($cond1);
            }
            else if($tipo_de_busca == "5"){
                if ($this->validaDataNormal($term) == TRUE) {
                    $cond1 = $this->select(FALSE)
                            ->where("usu_datanasc = '$term'")
                            ->getPart(Zend_Db_Select::WHERE);
                    $cond1 = implode(" ",$cond1);
                    $where->where($cond1);
                    //echo "<pre>";print_r($where);die();

                    // $where2->where("usu_datanasc=?", $term); 
                }
                else die("1");
            }
        $sql = $where;
        //echo "<pre>";print_r($sql);die();

        
            // die($sql);
        
        $all = $this->fetchAll($sql);
        //echo "<pre>";print_r($all);die();


        $out = array();
        foreach ($all as $usu) {
            $data = $usu->toArray();
            $data["equipe"] = $usu->nu_ine." - ".$usu->ds_area." - ".$usu->no_equipe;

            $out [] = array(
                    "id" => $usu->usu_codigo,
                    "label" => $usu->usu_nome,
                    "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("usu_codigo" => "0", "usu_mae" => "", "usu_datanasc" => "", "usu_nome" => "")
            );
        }
        return $out;
    }
    public function buscar($term=FALSE,$tipo_busca=FALSE) {
        $tbConf = new Application_Model_Configuracao();
        if ($term){
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->reset(Zend_Db_Select::WHERE)
                            ->from(array("usu" => "usuario"), array("usu_codigo","usu_sexo","usu_fone", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario",  "usu_cartao_sus", "usu_bloqueado", "rac_codigo", "cd_nacionalidade", "dom_codigo","usu_esta_gestante", "TO_CHAR(AGE(usu_datanasc), 'YY \"ano(s)\" MM \"mes(es)\" DD \"dia(s)\"') as idade", ""))
                            ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_telefone","dom_numero","(select usu_nome from usuario u2 where usu_codigo = dom.usu_codigo_responsavel) as usu_nome_resp"))
                            ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                            ->joinLeft(array("te"=>"tb_equipe"),"dom.cod_equipe=te.co_seq_equipe",array("nu_ine","ds_area","no_equipe"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome"))
                            ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=dom.bai_codigo", array("rua_bairro" => "bai_nome"))
                            ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                            ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                            ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                            // ->joinLeft(array("ate" => "atendimento"), "usu.usu_codigo=ate.usu_codigo", array("ate_peso","ate_altura"))
                            ->where("usu_ativacao=?", "S")
                            ->where("usu_bloqueado=?", "f");
                             // die($where);
            $where2 = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->reset(Zend_Db_Select::WHERE)
                            ->from(array("usu" => "usuario"), array("usu_codigo","usu_sexo","usu_fone", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario", "usu_cartao_sus", "usu_bloqueado", "rac_codigo", "cd_nacionalidade", "dom_codigo","usu_esta_gestante", "TO_CHAR(AGE(usu_datanasc), 'YY \"ano(s)\" MM \"mes(es)\" DD \"dia(s)\"') as idade"))
                            ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_telefone","dom_numero", "(select usu_nome from usuario u2 where usu_codigo = dom.usu_codigo_responsavel) as usu_nome_resp"))
                            ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                            ->joinLeft(array("te"=>"tb_equipe"),"dom.cod_equipe=te.co_seq_equipe",array("nu_ine","ds_area","no_equipe"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome"))
                            ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=dom.bai_codigo", array("rua_bairro" => "bai_nome"))
                            ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                            ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                            ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                            // ->joinLeft(array("ate" => "atendimento"), "usu.usu_codigo=ate.usu_codigo", array("ate_peso","ate_altura"))
                            ->where("usu_ativacao=?", "S")
                            ->where("usu_bloqueado=?", "f")
                            ->limit(15);
        }
        
        // repetição
        // $validator = new Zend_Validate_Regex("/^((([0][1-9]|[12][0-9])\/02\/(19|20)([13579][26]|[02468][048]))|(([0][1-9]|[1][0-9]|[2][0-8])\/02\/(19|20)([02468][12356]|[013579][13579]))|((([0][1-9]|[12][0-9]|30)\/(0[469]|11)|([0][1-9]|[12][0-9]|3[01])\/(0[13578]|1[02]))\/(19|20)[0-9][0-9]))$/");
        if ($this->validaDataNormal($term)) {
            $where->where("usu_datanasc=?", $term);
            $where2->where("usu_datanasc=?", $term);
        } else {
            //if($tipo_busca == ""){
            $cond1 = $this->select(FALSE)
                            ->where("usu_nome ilike '%$term%'")
                            ->orWhere("usu_prontuario = '$term'")
                            //->orWhere("usu_codigo = " . ((int) $term))
                            ->orWhere("usu_cartao_sus = '$term'")
                            ->getPart(Zend_Db_Select::WHERE);

            $cond2 = $this->select(FALSE)
                            ->where("usu_mae ilike '%$term%'")
                            ->getPart(Zend_Db_Select::WHERE);

            $cond1 = implode(" ", $cond1);
            $cond2 = implode(" ", $cond2);
            $where->where($cond1);
            $where2->where($cond2);
            // }
        }

        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->union(array($where, $where2), Zend_Db_Select::SQL_UNION);
            // die($sql);
        
        $all = $this->fetchAll($sql);


        $out = array();
        foreach ($all as $usu) {
            $data = $usu->toArray();
            $data["equipe"] = $usu->nu_ine." - ".$usu->ds_area." - ".$usu->no_equipe;

            $out [] = array(
                    "id" => $usu->usu_codigo,
                    "label" => $usu->usu_nome,
                    "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("usu_codigo" => "0", "usu_mae" => "", "usu_datanasc" => "", "usu_nome" => "")
            );
        }
        return $out;
    }

    //BUSCA PACIENTE RAAS

    public function buscarRas($term=FALSE,$tipo_busca=FALSE) {
        $tbConf = new Application_Model_Configuracao();
        $recebeTermo = $term;
        if ($term){
            $sql = $this->getDefaultAdapter()->query(
                "SELECT usu.usu_codigo, usu.usu_nome, usu.usu_sexo, usu.usu_mae, usu.usu_fone, usu.usu_nome_resp, usu.usu_datanasc, usu.usu_cartao_sus, raca.rac_descricao,raca.rac_codigo, usu.cd_nacionalidade, dom.dom_numero,dom.dom_complemento, dom.dom_telefone, rua.rua_nome, rua.rua_cep,bai.bai_nome, cid.cid_nome,uf.uf_codigo,uf.uf_sigla FROM usuario as usu
                    LEFT JOIN raca as raca on raca.rac_codigo=usu.rac_codigo
                    LEFT JOIN domicilio as dom on dom.dom_codigo=usu.dom_codigo
                    LEFT JOIN rua as rua on rua.rua_codigo=dom.rua_codigo
                    LEFT JOIN bairro as bai on bai.bai_codigo=rua.bai_codigo
                    LEFT JOIN cidade as cid on cid.cid_codigo=bai.cid_codigo
                    LEFT JOIN estado as uf on uf.uf_codigo=cid.uf_codigo
                    WHERE usu.usu_nome ilike '%$recebeTermo%'
                "
            )->fetchAll();
        }

        $out = array();

        foreach ($sql as $usu) {
            $out [] = array(
                    "id" => $usu[usu_codigo],
                    "label" => $usu[usu_nome],
                    "data" => $usu
            );
        }
        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => $usu
            );
        }
        return $out;
    }

    //BUSCA LISTA RAAS 3.0

    public function buscaListaRaas(){
        $tbConf = new Application_Model_Configuracao();
        $recebeTermo = $term;
        if($term){
            $sql = $this->getDefaultAdapter()->query(
                " SELECT raas.ras_val_ini, raas.ras_prontuario, raas.ras_paciente FROM raas
                WHERE raas.ras_paciente ilike '%recebeTermo%'
                "
            )->fetchAll();
        }

        $out = array();

        foreach ($sql as $usu) {
            $out [] = array(
                    "id" => $usu[ras_prontuario],
                    "label" => $usu[ras_paciente],
                    "data" => $usu
            );
        }
        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => $usu
            );
        }
        return $out;

    }

    /**
     * Buscar os USU's até mesmo em obito, especifico relatório
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscarUsuarioRelatorio($term=FALSE) {
        if ($term){
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->reset(Zend_Db_Select::WHERE)
                            ->from(array("usu" => "usuario"), array("usu_codigo", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario",  "usu_cartao_sus", "usu_bloqueado","usu_nome_resp"))
                            ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_numero"))
                            ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                            ->joinLeft("area", "area.area_codigo=psf.psf_area", array("psf_area" => "COALESCE(area_desc::text,'Não definido')"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome"))
                            ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=rua.bai_codigo", array("rua_bairro" => "bai_nome"))
                            ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                            ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                            ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome");
            $where2 = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->reset(Zend_Db_Select::WHERE)
                            ->from(array("usu" => "usuario"), array("usu_codigo", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario", "usu_cartao_sus", "usu_bloqueado","usu_nome_resp"))
                            ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_numero"))
                            ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                            ->joinLeft("area", "area.area_codigo=psf.psf_area", array("psf_area" => "COALESCE(area_desc::text,'Não definido')"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome"))
                            ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=rua.bai_codigo", array("rua_bairro" => "bai_nome"))
                            ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                            ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                            ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                            ->limit(15);
        }

        // repetição
        $validator = new Zend_Validate_Regex("/^((([0][1-9]|[12][0-9])\/02\/(19|20)([13579][26]|[02468][048]))|(([0][1-9]|[1][0-9]|[2][0-8])\/02\/(19|20)([02468][12356]|[013579][13579]))|((([0][1-9]|[12][0-9]|30)\/(0[469]|11)|([0][1-9]|[12][0-9]|3[01])\/(0[13578]|1[02]))\/(19|20)[0-9][0-9]))$/");
        if ($validator->isValid($term)) {
            $where->where("usu_datanasc=?", $term);
            $where2->where("usu_datanasc=?", $term);
        } else {
            $cond1 = $this->select(FALSE)
                          ->where("usu_nome ilike '%$term%'")
                          ->orWhere("usu_prontuario = '$term'")
                          //->orWhere("usu_codigo = " . ((int) $term))
                          ->getPart(Zend_Db_Select::WHERE);

            $cond2 = $this->select(FALSE)
                        ->where("usu_mae ilike '%$term%'")
                        ->getPart(Zend_Db_Select::WHERE);

            $cond1 = implode(" ", $cond1);
            $cond2 = implode(" ", $cond2);
            $where->where($cond1);
            $where2->where($cond2);
        }

        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->union(array($where, $where2), Zend_Db_Select::SQL_UNION);
        $all = $this->fetchAll($sql);

        $out = array();
        foreach ($all as $usu) {
            $data = $usu->toArray();

            $out [] = array(
                    "id" => $usu->usu_codigo,
                    "label" => $usu->usu_nome,
                    "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("usu_codigo" => "0", "usu_mae" => "", "usu_datanasc" => "", "usu_nome" => "")
            );
        }
        return $out;
    }

    /**
     * Retorna os dados do paciente. Usar em cabeçalhos.
     * @param int $usu_codigo
     */
    public function getInfo($usu_codigo) {
        
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("usu" => "usuario"), array("usu_codigo", "usu_prontuario", "usu_nome", "usu_sexo","usu_datanasc", "usu_mae", "usu_fone", "usu_cartao_sus", "usu_ocupacao", "usu_endereco" => "usu_end_rua"))
                        ->joinLeft(array("estc" => "estado_civil"), "estc.estc_codigo=usu.usu_estado_civil", array("estado_civil" => "estc_descricao"))
                        ->joinLeft(array("dom" => "domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_codigo", "dom_numero", "dom_segmento", "dom_complemento","dom_telefone"))
                        ->joinLeft("rua", "rua.rua_codigo=dom.rua_codigo", array("rua_nome", "rua_bairro"))
                        ->joinLeft(array("log" => "tb_ms_tipo_logradouro"), "log.co_tipo_logradouro=rua.co_tipo_logradouro", "ds_tipo_logradouro_abrev")
                        ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=rua.bai_codigo", array("rua_bairro" => "bai_nome"))
                        ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                        ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome","uf_sigla"))
                        ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                        ->joinLeft(array("cbo"=>"tb_ocupacao"),"cbo.co_ocupacao=usu.usu_ocupacao",array("no_ocupacao"))
                        ->joinLeft(array("cbo2"=>"tb_ocupacao"),"cbo2.co_ocupacao=usu.usu_cbo_r",array("no_ocupacao"))
                        ->where("usu.usu_codigo=?", $usu_codigo);
        $usu = $this->fetchRow($where);
        if ($usu->dom_codigo) {
            $out = array();
            $out [] = trim($usu->ds_tipo_logradouro_abrev . ": " . $usu->rua_nome);
            $out [] = $usu->dom_numero;
            $out [] = trim($usu->dom_segmento);
            $out [] = trim($usu->dom_complemento);
            $out [] = trim($usu->rua_bairro);
            $out [] = $usu->cid_nome;

            $this->emptyToUnset($out);
            
            $usu->usu_endereco = ucwords(strtolower(implode(", ", $out))) . "/" . $usu->uf_sigla;
        } else {
            $usu->usu_endereco = "<em>Não cadastrado</em>";
        }
        
        
        // echo"<pre>".print_r($usu,1);die();
        return $usu;
    }

    public function relProntuario($usu_codigo, $data_inicial=FALSE, $data_final=FALSE, $options=FALSE,$limit=FALSE,$esp=FALSE) {
        $dados = new stdClass();
        $dados->usu = $this->getInfo($usu_codigo);
        //die($esp);

        if (!$options || in_array("alertas", $options)) {
            $tbAle = new Application_Model_Alerta();
            $dados->alertas = $tbAle->getItens($usu_codigo);
        }
        if (!$options || in_array("atendimentos", $options)) {
            $tbAte = new Application_Model_Atendimento();
            $dados->atendimentos = $tbAte->getHistoricoDetalhado($usu_codigo, $data_inicial, $data_final,$limit);
            //echo "<pre>".print_r($tbAte->getHistoricoDetalhado($usu_codigo, $data_inicial, $data_final),1);
            //exit();
        }

        if (!$options || in_array("pre-consulta", $options)) {
            $tbPC = new Application_Model_PreConsulta();
            $dados->pre_consultas = $tbPC->getHistorico($usu_codigo, $data_inicial, $data_final, $options['pre-consulta'],$limit);
        }

       
        if (!$options || in_array("exames", $options)) {
            $tbAge = new Application_Model_Agenda();
            $dados->exames = $tbAge->getHistoricoDeExames($usu_codigo, $data_inicial, $data_final);
        }

        if (!$options || in_array("procedimentos", $options)) {
            $tbProc = new Application_Model_ProcedimentoAtendimento();
            $dados->procedimentos = $tbProc->getHistoricoPorPaciente($usu_codigo, $data_inicial, $data_final);
        }

        if (!$options || in_array("medicamentos", $options)) {
            $tbMov = new Application_Model_Movimento();
            $dados->medicamentos = $tbMov->getProntuarioMedicamentosDispensados($usu_codigo, $data_inicial, $data_final,$limit);
        }

        if (!$options || in_array("compra_produtos", $options)) {
            $tbComp = new Application_Model_CompraProduto();
            $dados->compra_produtos = $tbComp->getHistoricoPorUsuario($usu_codigo, $data_inicial, $data_final);
        }

        if (!$options || in_array("agendamento_externo", $options)) {
            $tbAge = new Application_Model_AgendamentoExterno();
            $dados->agendamento_externo = $tbAge->getHistorico($usu_codigo, $data_inicial, $data_final);
        }

        if (!$options || in_array("observacoes", $options)) {
            //die($this->find($usu_codigo)->current()->usu_observacao);
            $dados->observacoes = $this->find($usu_codigo)->current()->usu_observacao;
        }

        if (!$options || in_array("vacinas", $options)) {
            $tbVac = new Application_Model_VacinaUsuario();
            $dados->vacinasAprazadas = $tbVac->getHistorico($usu_codigo, array(Application_Model_VacinaUsuario::APRAZAR), $data_inicial, $data_final);
            $dados->vacinas = $tbVac->getHistorico($usu_codigo, array(
                Application_Model_VacinaUsuario::APLICAR,
                Application_Model_VacinaUsuario::PREENCHER
            ), $data_inicial, $data_final);
        }

        if (!$options || in_array("internacoes", $options)) {
            //die($this->find($usu_codigo)->current()->usu_observacao);
            $tbIo = new Application_Model_InternacaoObservacao();
            //die("teste");
            
            //echo"<pre>";print_r($x);die();
            $dados->internacoes = $tbIo->getHistorico($usu_codigo,$data_inicial, $data_final,$esp);
        }

        if (!$options || in_array("importacoes", $options)) {
            //die($this->find($usu_codigo)->current()->usu_observacao);
            $dados->importacoes = $this->find($usu_codigo)->current()->usu_importacao;
        }

        //ID #105426
        if (!$options || in_array("atividade_coletiva", $options)) {
            $tbAcp = new Application_Model_TbCdsAtivColParticipante();
            $dados->atividade_coletiva = $tbAcp->getDadosPorPaciente($usu_codigo,$data_inicial, $data_final);
        }
        //ID #105426
        // echo "<pre>";print_r($dados);die();
        return $dados;
    }

    /**
     * Traz o histórico de consultas do paciente, para ser usado no modo ficha
     * @param int $usu_codigo
     * @param string $data_inicial
     * @param string $data_final
     * @param int $usr_codigo
     */
    public function relProntuarioFicha($usu_codigo, $data_inicial = FALSE, $data_final = FALSE, $usr_codigo = FALSE, $limit = FALSE) {
        $dados = new stdClass();

        $dados->usu = $this->getInfo($usu_codigo);

        $todasConsultas = (object) $this->relProntuarioFichaBuscar($usu_codigo, $data_inicial, $data_final, $usr_codigo, $limit)->toArray();
        $out = array();

        $tbPat = new Application_Model_ProcedimentoAtendimento();
        $modelFunc = new Application_Model_Funcoes();
        $tbReq = new Application_Model_RequisicaoExame();
        $tbIRec = new Application_Model_ReceitaItens();
        $tbEnc = new Application_Model_Encaminhamento();
        $tbEncExt = new Application_Model_EncaminhamentoExterno();
        $tbPC = new Application_Model_PreConsulta();
        $tbAtei = new Application_Model_AtendimentoInternacao();

        foreach ($todasConsultas as $consulta) {
            $consulta = (object) $consulta;

            $consulta->pre_consulta = $tbPC->getDadosPorAgendamento($consulta->age_codigo);

            $procs = $tbPat->getHistoricoPorAgendamento($consulta->age_codigo);
            $consulta->procedimentos = $modelFunc->rowsetToStr($procs, "proc_nome");

            $exames = $tbReq->getHistorico($consulta->ate_codigo);
            $consulta->exames = $modelFunc->rowsetToStr($exames, "proc_nome");

            $medicamentos = $tbIRec->getHistorico($consulta->ate_codigo);
            $consulta->med = $medicamentos;

            $encamihamentos = $tbEnc->getHistorico($consulta->ate_codigo);
            $consulta->encaminhamentos = $modelFunc->rowsetToStr($encamihamentos, "esp_nome");

            $encamihamentos_externos = $tbEncExt->getHistorico($consulta->ate_codigo);
            $consulta->encamihamentos_externos = $modelFunc->rowsetToStr($encamihamentos_externos, "enc_ext_agendado_para");

            $internacoes = $tbAtei->getDadosInternaObservacao($consulta->ate_codigo);
            $consulta->internacoes = $internacoes;

            $out [] = $consulta;
        }

        $dados->atendimentos = (object) $out;
        // echo "<pre>".print_r($medicamentos,1);die();
        return $dados;
    }

    /**
     * Traz o histórico de consultas do paciente, para ser usado no modo ficha
     * @param int $usu_codigo
     * @param int $data_inicial
     * @param int $data_final
     * @param int $usr_codigo
     * @return Zend_Db_Table_Rowset_Abstract
     */
    private function relProntuarioFichaBuscar($usu_codigo, $data_inicial=FALSE, $data_final=FALSE, $usr_codigo=FALSE, $limit=FALSE) {
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), array("ate_codigo", "ate_data", "ate_horafinal","ate_hora", "ate_reclamacao", "ate_exame_fisico", "ate_diagnostico", "ate_tratamento", "ate_curativo"))
                        ->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", array("age_codigo", "age_data"))
                        ->join(array("usr" => "usuarios"), "usr.usr_codigo=age.med_codigo", "usr_nome")
                        ->join(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
                        ->join(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
                        ->where("age.usu_codigo=?", $usu_codigo)
                        ->order(array("age_data DESC", "ate_hora DESC"));

        if ($data_inicial){
            $where->where("age.age_data >= ?", $data_inicial);
        }

        if ($data_final){
            $where->where("age.age_data <= ?", $data_final);
        }

        if ($usr_codigo){
            $where->where("age.med_codigo=?", $usr_codigo);
        }

        if ($limit){
            $where->limit($limit);
        }

        //die($where);
        return $this->fetchAll($where);
    }

    /**
     * Atualiza todas as tabelas do sistema que estão vinculadas a um paciente duplicado para um único paciente
     * depois remove-os.
     * @param int $correto
     * @param array $duplicados
     * @return array quantos registros foram atualizados e quantos foram removidos por tabela
     */
    public function removerDuplicacoes($correto, $duplicados){
        Zend_Registry::get("logger")->log(array($correto,$duplicados), Zend_Log::INFO);

        $out = array();
        $tbAge = new Application_Model_Agendamento();
        //$tbAgexl = new Application_Model_AgendamentoExameLista();
        $tbAgee = new Application_Model_AgendamentoExterno();
        $tbAIH = new Application_Model_AIH();
        $tbAle = new Application_Model_Alerta();
        $tbApac = new Application_Model_Apac();
        $tbAte = new Application_Model_Atendimento();
        $tbAtf = new Application_Model_AtendimentoFamilia();
        $tbBPA = new Application_Model_BPA();
        $tbCtp = new Application_Model_CotaPaciente();
        $tbDou = new Application_Model_DoencaUsuario();
        $tbEvu = new Application_Model_EventoUsuario();
        $tbEvua = new Application_Model_EventoUsuarioAux();
        $tbGau = new Application_Model_GrupoAtendimentoUsuario();
        $tbHiper = new Application_Model_Hiperdia();
        $tbLie = new Application_Model_ListaEspera();
        $tbMov = new Application_Model_Movimento();
        $tbProc = new Application_Model_ProcedimentosMedico();
        $tbReq = new Application_Model_Requisicao();
        $tbSPN = new Application_Model_SisPreNatal();
        $tbTub = new Application_Model_Tuberculose();
        $tbGest = new Application_Model_UsuarioGestante();
        $tbVac = new Application_Model_VacinaUsuario();
        $tbViaUsu = new Application_Model_ViagemUsuario();
        $tbReqEx = new Application_Model_RequisicaoExame();
        $tbMovBkp = new Application_Model_MovimentoBkp();
        $tbAgenda = new Application_Model_Agenda();
        $tbEci = new Application_Model_EsusCadastroIndividual();
        //$tbFic = new Application_Model_FichaMedidaSocioEducativa();
        //$tbFicM = new Application_Model_FichaMulher();
        $tbLei = new Application_Model_LeitoGrade();
        $tbGruAtv = new Application_Model_GrupoAtividadeParticipante();


        $this->getAdapter()->beginTransaction();

        try{
            $out['age'] = $tbAge->atualizarUsu($duplicados, $correto);
            //$out['agexl'] = $tbAgexl->atualizarUsu($duplicados, $correto);
            $out['agee'] = $tbAgee->atualizarUsu($duplicados, $correto);
            $out['aih'] = $tbAIH->atualizarUsu($duplicados, $correto);
            $out['ale'] = $tbAle->atualizarUsu($duplicados, $correto);
            $out['apac'] = $tbApac->atualizarUsu($duplicados, $correto);
            $out['ate'] = $tbAte->atualizarUsu($duplicados, $correto);
            $out['atf'] = $tbAtf->atualizarUsu($duplicados, $correto);
            $out['bpa'] = $tbBPA->atualizarUsu($duplicados, $correto);
            $out['ctp'] = $tbCtp->atualizarUsu($duplicados, $correto);
            $out['dou'] = $tbDou->atualizarUsu($duplicados, $correto);
            $out['evu'] = $tbEvu->atualizarUsu($duplicados, $correto);
            $out['evua'] = $tbEvua->atualizarUsu($duplicados, $correto);
            $out['gau'] = $tbGau->atualizarUsu($duplicados, $correto);
            $out['hiper'] = $tbHiper->atualizarUsu($duplicados, $correto);
            $out['lie'] = $tbLie->atualizarUsu($duplicados, $correto);
            $out['mov'] = $tbMov->atualizarUsu($duplicados, $correto);
            $out['proc'] = $tbProc->atualizarUsu($duplicados, $correto);
            $out['req'] = $tbReq->atualizarUsu($duplicados, $correto);
            $out['spn'] = $tbSPN->atualizarUsu($duplicados, $correto);
            $out['tub'] = $tbTub->atualizarUsu($duplicados, $correto);
            $out['gest'] = $tbGest->atualizarUsu($duplicados, $correto);
            $out['vac'] = $tbVac->atualizarUsu($duplicados, $correto);
            $out['viausu'] = $tbViaUsu->atualizarUsu($duplicados, $correto);
            $out['req'] = $tbReqEx->atualizarUsu($duplicados, $correto);
            $out['mov_bkp'] = $tbMovBkp->atualizarUsu($duplicados, $correto);
            $out['agenda'] = $tbAgenda->atualizarUsu($duplicados, $correto);
            $out['eci'] = $tbEci->atualizarUsu($duplicados, $correto);
            $out['grpatv'] = $tbGruAtv->atualizarUsu($duplicados, $correto);

            //$out['mov_bkp'] = $tbFic->atualizarUsu($duplicados, $correto);
            //$out['mov_bkp'] = $tbFicM->atualizarUsu($duplicados, $correto);

            $this->getAdapter()->commit();
        } catch (Exception $e){
            //echo "<pre>" . print_r($e->getMessage(), 1)."aaaaa";
            //die();
            $this->getAdapter()->rollBack();
            Zend_Registry::get("logger")->log($e->getMessage(), Zend_Log::INFO);
            return false;
        }

        $removidos = $this->remover($duplicados);

        return array(array_sum($out),$removidos);
    }
    /**
     * Recebe um array de usu_codigo e remove todos
     * @param array $usu_codigo
     * @return int Número de linhas removidas
     */
    public function remover($usu_codigo){
        $where = $this->select()->where("usu_codigo IN (?)", $usu_codigo)->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        return $this->delete($where);
    }

    public function buscarFiltro($term = FALSE, $tipo_busca = FALSE){

        $where2 = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->reset(Zend_Db_Select::WHERE)
                        ->from(array("usu" => "usuario"), array("usu_codigo", "usu_nome", "usu_mae", "usu_pai", "usu_datanasc" => "to_char(usu_datanasc,'DD/MM/YYYY')", "usu_prontuario", "usu_cartao_sus", "usu_bloqueado","usu_nome_resp"))
                        ->joinLeft(array("dom"=>"domicilio"), "dom.dom_codigo=usu.dom_codigo", array("dom_numero"))
                        ->joinLeft("psf","psf.dom_codigo=dom.dom_codigo","")
                        ->joinLeft("area", "area.area_codigo=psf.psf_area", array("psf_area" => "COALESCE(area_desc::text,'Não definido')"))
                        ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo",array("rua_nome","rua_bairro"))
                        ->joinLeft(array("bai" => "bairro"),"bai.bai_codigo=rua.bai_codigo", array("rua_bairro" => "bai_nome"))
                        ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                        ->joinLeft(array("c_dis"=>"cidade"),"c_dis.cid_codigo=dis.cid_codigo",array("cid_nome_dis"=>"cid_nome"))
                        ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo","cid_nome")
                        ->where("usu_ativacao=?", "S")
                        ->where("usu_bloqueado=?", "f")
                        ->limit(15);
                        
        if($tipo_busca == "N"){
            $where2->where("usu_nome ilike '%$term%'");
        } else if($tipo_busca == "M"){
            $where2->where("usu_mae ilike '%$term%'");
        } else if($tipo_busca == "C"){
            $where2->where("usu_cartao_sus = '$term'");
        } else if($tipo_busca == "P"){
            $where2->where("usu_prontuario = $term");
        } else if($tipo_busca == "D"){
            $where2->where("usu_datanasc = '$term'");
        }

        $all = $this->fetchAll($where2);
        $out = array();
        foreach ($all as $usu) {
            $data = $usu->toArray();

            $out [] = array(
                    "id" => $usu->usu_codigo,
                    "label" => $usu->usu_nome,
                    "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("usu_codigo" => "0", "usu_mae" => "", "usu_datanasc" => "", "usu_nome" => "")
            );
        }
        return $out;
    }

    public function getMesesIdade($usu_codigo = FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("usu"=>"usuario"),array("usu_sexo","meses"=>"((DATE_PART('YEAR', AGE(NOW(), usu_datanasc))*12)+DATE_PART('MONTH', AGE(NOW(), usu_datanasc)))"))
                      ->where("usu_codigo=$usu_codigo");
        return $this->fetchRow($where);
    }

    public function confereInsUsuario($usu_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from("usuario",array("COUNT(usuario) AS numRegistro"))
                    ->where("usu_codigo =?",$usu_codigo);
        return $this->fetchRow($sql);
    }

    public function listaDadosUsuario($usu_codigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu"=>'usuario'),array("pessoa"=>"usu_codigo",
                        "usu_foto_nome",
                        "usu_foto",
                        "dom_codigo",
                        "uni_codigo",
                        "pep_bloqueado"=>"usu_bloqueado",
                        "pep_cartao_sus"=>"usu_cartao_sus",
                        "pep_prontuario" => "usu_prontuario",
                        "usu_ciscomcam" => "usu_ciscomcam",
                        "nome" => "usu_nome",
                        "pep_sexo" => "usu_sexo",
                        "datanascimento" => "usu_datanasc",
                        "pep_mae" => "usu_mae",
                        "pep_pai" => "usu_pai",
                        "cid_codigo" => "cid_codigo_nasc",
                        "pep_email" => "usu_email",
                        "pep_celular" => "usu_celular" ,
                        "pep_telefone" => "usu_fone",
                        //"pep_responsavel" =>"",
                        "pep_conjuge" => "usu_conjuge",
                        "pep_obito" => "usu_obito",
                        "pep_data_obito" => "usu_dt_obito",
                        "pais_codigo" => "pais_codigo",
                        "pep_cartorio_nasc" => "usu_cert_cartorio_nasc",
                        "pep_livro_nasc" => "usu_cert_livro_nasc",
                        "pep_folha_nasc" => "usu_cert_lv_fls_nasc",
                        "pep_termo_nasc" => "usu_cert_termo_nasc",
                        "rac_codigo",
                        "co_ocupacao" => "usu_cbo_r",
                        "pep_cnh" => "usu_cnh_numero",
                        "pep_categoria_cnh" => "usu_cnh_categoria",
                        "pep_carteira_trabalho" => "usu_ctps",
                        "pep_carteira_trabalho_serie" => "usu_ctps_serie",
                        "pep_carteira_trabalho_data" => "usu_ctps_dt_emissao",
                        "pep_titulo_eleitor" => "usu_tit_eleitor",
                        "pep_titulo_zona" => "usu_tit_eleitor_zona",
                        "pep_titulo_secao" => "usu_tit_eleitor_secao",
                        "pep_transporte_publico" => "usu_transporte_publico",
                        "pep_frenquencia_escolar" => "usu_freq_escolar",
                        "pep_portaria_naturalizacao"=>"nr_portaria_naturalizacao",
                        "pep_data_naturalizacao" => "dt_naturalizacao",
                        "pep_data_entrada_pais" => "usu_dt_entrada_pais",
                        "pep_bolsa_alimentacao" => "usu_bolsa_alimentacao",
                        "pep_bolsa_familia" => "usu_bolsa_familia",
                        "pep_plano_saude" => "usu_plano_saude",
                        "pep_renda" => "usu_renda_media",
                        "pep_observacao" => "usu_observacao",
                        "pep_ecd_codigo" => "ecd_codigo",
                        "pep_situacao_familiar" => "usu_sit_familiar",
                        "estc_codigo" => "estc_codigo",
                        "cnpj_cpf"=>"usu_cpf",
                        "rg"=>"usu_rg",
                        "orgaoemissor" => "usu_rg_emissor",
                        "dataemissao"=>"usu_rg_dt_emissao",
                        "pispasep" => "usu_pis_pasep",
                        "estadoemissor" => "uf_sigla_rg",
                        "usu_nome_resp",
                        "usu_cbo_r",
                        "usr.usr_codigo",
                        "usr.usr_nome",
                        "cd_nacionalidade",
                        "usu_sit_rua",
                        // "usu_situacao_rua_tempo",
                        "cid_codigo_nasc",
                        "usu_deficiencia",
                        "usu_doenca"
                        ))
                ->joinLeft(array("dom"=>"domicilio"),"usu.dom_codigo=dom.dom_codigo",array("dom_data_cadastro","rua_codigo","dom_numero","dom_segmento","co_tipo_domicilio","dom_telefone","usu_codigo_responsavel","dom_complemento","dom_ponto_referencia","(select usu_nome from usuario where usu_codigo = dom.usu_codigo_responsavel) as usu_nome_responsavel"))
                ->joinLeft(array("rua"=>"rua"),"dom.rua_codigo=rua.rua_codigo",array("rua_codigo","rua_nome","cid_codigo","co_tipo_logradouro","rua_cep"))
                ->joinLeft(array("bai"=>"bairro"),"bai.bai_codigo=dom.bai_codigo",array("rua_bairro"=>"bai_nome","bai_codigo"))
                ->joinLeft(array("cid"=>"cidade"),"cid.cid_codigo=bai.cid_codigo",array("cid_nome"))
                ->joinLeft(array("dis"=>"distrito"),"dis.dis_codigo=bai.dis_codigo","dis_nome")
                ->joinLeft(array("cid2"=>"cidade"),"cid2.cid_codigo=dis.cid_codigo",array("localidade"=>"(cid2.cid_nome || ' - ' || COALESCE(dis.dis_nome,NULL,''))"))
                ->joinLeft(array("cid_nasc" => "cidade"),"usu.cid_codigo_nasc=cid_nasc.cid_codigo",array("cid_nasc_nome"=>"cid_nome"))
                ->joinLeft(array("tl"=>"tb_ms_tipo_logradouro"),"rua.co_tipo_logradouro=tl.co_tipo_logradouro",array("ds_tipo_logradouro"))
                ->joinLeft(array("toc"=>"tb_ocupacao"),"usu.usu_cbo_r=toc.co_ocupacao","no_ocupacao")
                ->joinLeft(array("usr"=>"usuarios"),"usu.usr_codigo=usr.usr_codigo",array("usr_codigo","usr_nome"));
        $sql->where("usu.usu_codigo=?",$usu_codigo);
        //echo "<pre>".$sql;die();
        return $this->fetchAll($sql);
    }

    function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function getPacientesPelaMaeNomeNasc($dados_eir = FALSE, $usu_codigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu"=>"usuario"));

        if ($usu_codigo != 0 && $usu_codigo != "" && $usu_codigo != null) {
            $sql->where("usu_codigo =?",$usu_codigo);
        } else {
            $sql->where("usu_nome =?",$dados_eir->eir_nome)
                ->where("usu_mae =?",$dados_eir->eir_nome_mae);
        }

        return $this->fetchAll($sql);
    }

    public function getProntuarioDuplicado($usu_prontuario = FALSE){
        $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("usu"=>'usuario'),"COUNT(usu_codigo) as num")
                        ->where("usu.usu_prontuario = '$usu_prontuario'");
        return $this->fetchRow($sql);
    }

    public function getQtdUsuariosAtivo(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu"=>"usuario"),array("count(usu_codigo) AS qtdUsuAtivo"))
                    ->where("usu_ativacao =?","S");
        return $this->fetchRow($sql);
    }

    public function getDuplicados(){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("usu"=>"usuario"),array("usu_nome","count(*)"))
                      ->group("usu_nome")
                      ->having("Count(*) > 1");
        return $this->fetchAll($where);
    }

    public function getVinculoComDomicilio($usu_codigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("dom"=>"domicilio"),array("dom_codigo","dom_numero","usu_codigo_responsavel"))
                    ->join(array("usu"=>"usuario"),"usu.dom_codigo=dom.dom_codigo OR usu.usu_codigo=dom.usu_codigo_responsavel","")
                    ->join(array("rua"), "dom.rua_codigo=rua.rua_codigo", array("rua_nome", "rua_cep", "rua_codigo", "co_tipo_logradouro"))
                    ->joinLeft(array("tp_log" => "tb_ms_tipo_logradouro"), "tp_log.co_tipo_logradouro=rua.co_tipo_logradouro","ds_tipo_logradouro")
                    ->joinLeft(array("bai" => "bairro"), "dom.bai_codigo=bai.bai_codigo", array( "bai_nome", "bai_codigo"))
                    ->joinLeft(array("dis" => "distrito"), "dis.dis_codigo=bai.dis_codigo", "dis_nome")
                    ->where("usu.usu_codigo=$usu_codigo");
        return $this->fetchAll($sql);
    }

    public function getAgendaItensPorPaciente($usu_codigo = FALSE, $dataFinal = FALSE, $dataInicial = FALSE, $proc_codigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu" => "usuario"), "")
                    ->joinLeft(array("age" => "agenda"), "age.usu_codigo=usu.usu_codigo", array("age.age_data_insert"))
                    ->joinLeft(array("agei"=>"agenda_itens"), "agei.age_codigo=age.age_codigo", array("agei.agei_valor"))
                    ->joinLeft(array("coni"=>"convenio_itens"), "coni.coni_codigo=agei.coni_codigo", "")
                    ->joinLeft(array("proc"=>"procedimento"), "proc.proc_codigo=coni.proc_codigo", array("proc.proc_nome", "proc.proc_codigo"))
                    ->joinLeft(array("conv"=>"convenio"),"conv.conv_codigo=coni.conv_codigo", "")
                    ->joinLeft(array("med"=>"medico"), "med.med_codigo=conv.med_codigo", array("med.med_nome"))
                    ->where("usu.usu_codigo = ?", $usu_codigo);
        
        if($dataInicial){
            $sql->where("age.age_data_insert >= ?", $dataInicial);
        }
        if($dataFinal){
            $sql->where("age.age_data_insert <= ?", $dataFinal);
        }
        if($proc_codigo){
            $sql->where("proc.proc_codigo =?",$proc_codigo);
        }
        
        //die($sql);
        return $this->fetchAll($sql);
    }

    public function getFaltas($usu_codigo = FALSE, $data_inicial = FALSE, $data_final = FALSE){
        $sql = $this->select(FALSE)
                    //->distinct("usr.usr_nome")
                    ->setIntegrityCheck(FALSE)
                    ->from(array("usu" => "usuario"))
                    ->joinLeft(array("age"=>"agendamento"), "age.usu_codigo=usu.usu_codigo", array("age.age_data","age_observacao"))
                    ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=age.med_codigo", array("usr.usr_nome"))
                    ->where("age.age_atendido='F'")
                    ->where("usu.usu_codigo=?",$usu_codigo);
                
        if($data_inicial){
            $sql->where("age.age_data >= ?", $data_inicial);
        }
        
        if($data_final){
            $sql->where("age.age_data <= ?", $data_final);
        }
        // die($sql);
        return $this->fetchAll($sql);
    }

	public function validaSexoFeminino($id){
        // error_reporting(E_ALL);
        $sql = $this->getDefaultAdapter()->query(" SELECT usu_sexo from usuario WHERE usu_codigo = $id AND usu_sexo = 'F' ")->fetch();
        
        return $sql;
    }

    public function salvarEstaGestante($dadosUsu){
        // echo "<pre>";print_r($dadosUsu);die();
        $sql = $this->getDefaultAdapter()->query("UPDATE usuario SET usu_esta_gestante = '$dadosUsu[usu_esta_gestante]' WHERE usu_codigo = $dadosUsu[usu_codigo]")->fetch();
        return $sql;
    }

    public function checaGestante($id){
        $sql = $this->getDefaultAdapter()->query("SELECT usu_esta_gestante FROM usuario WHERE usu_codigo = $id ")->fetch();
        return $sql;
    }
	
    public function recuperaDadosDaGestacao($id){
        $sql = $this->getDefaultAdapter()->query("
            SELECT tipo_consulta, dum, idade_gestacional, gravidez_planejada, gestas_previas, partos, atendimento_prenatal.risco_gestacao, usu_esta_gestante, data_provavel_parto 
            FROM atendimento_prenatal
            INNER JOIN atendimento ON atendimento.ate_codigo = atendimento_prenatal.ate_codigo
            INNER JOIN usuario ON usuario.usu_codigo = atendimento.usu_codigo 
            WHERE usuario.usu_codigo = $id

            ")->fetch();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function recuperaId($id){
        $sql = $this->getDefaultAdapter()->query("
                SELECT usu_codigo FROM atendimento WHERE ate_codigo = $id
            ")->fetch();
        // echo "<pre>";print_r($sql);die();
        return $sql;
    }

    public function buscaDadosEspeciais($codPaciente){
        if ($codPaciente) {
            $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usu" => "usuario"), array("usu_deficiencia", "usu_tem_diabete", "usu_esta_gestante", "usu_tem_hipertensao", "TO_CHAR(usu_datanasc, 'dd/mm/YYYY') as usu_datanasc", "usu_sexo", "dom_codigo"))
                ->joinLeft(array('dom' => 'domicilio'), 'usu.dom_codigo = dom.dom_codigo', array(""))
                ->where("usu.usu_codigo = ?", $codPaciente);
            
                $retorno = $this->fetchRow($sql)->toArray();
            return $retorno;
        } else {
            return 0;
        }
    }

    public function atualizaTelefone($recebeUsuCodigoTelefone){
        $sql = $this->getDefaultAdapter()->query(

            "UPDATE usuario SET usu_fone = $recebeUsuCodigoTelefone[usu_fone]  where usu_codigo = $recebeUsuCodigoTelefone[usu_codigo]"

        )->fetchAll();

        return $sql;
    }
}
