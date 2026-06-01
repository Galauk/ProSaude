<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Unidade extends Elotech_Db_Table_Abstract{

    protected $_name            = 'unidade';
    protected $_primary         = 'uni_codigo';
    protected $_sequence        = 'seq_uni_codigo';
    protected $_dependentTables = array('Agendamento');

    public function salvar(array $data){
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            print_r($exc->getMessage());
            print_r("Falha ao cadastrar a unidade: " . $exc->getMessage());
        }
    }

    public function verificaSeJáExiste($cnes = false){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("qtd" => "count(*)", "uni_codigo"))
            ->where("uni_cnes = '$cnes'")
            ->group("uni_codigo");

        return $this->fetchRow($where);
    }

    /**
     * Buscar as unidade
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscar($term = false){

        if ($term) {
            $where = $this->select(false)
                ->setIntegrityCheck(false)
                ->from(array("uni" => "unidade"), array("uni_codigo", "uni_desc"))
                ->where("retira_acentos(uni_desc) ilike retira_acentos('%$term%')", "S")
                ->where("cnes_ativo != 'I'")
                ->order(array("uni_desc"))
                ->limit(15);
        }
        // die($where);
        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $uni) {
            $out[] = array(
                "id"    => $uni->uni_codigo,
                "label" => $uni->uni_desc,
                "data"  => $uni->toArray(),
            );
        }

        if (!count($out)) {
            $out[] = array(
                "id"    => 0,
                "label" => "Nenhum item encontrado",
                "data"  => array("uni_codigo" => "0", "uni_desc" => ""),
            );
        }

        return $out;
    }


    public function buscarRaas($term = false){

        if ($term) {
            $where = $this->select(false)
                ->setIntegrityCheck(false)
                ->from(array("uni" => "unidade"), array("uni_codigo", "uni_desc", "uni_cnes"))
                ->where("retira_acentos(uni_desc) ilike retira_acentos('%$term%')", "S")
                ->where("cnes_ativo != 'I'")
                ->order(array("uni_desc"))
                ->limit(15);
        }
        //die($where);
        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $uni) {
            $out[] = array(
                "id"    => $uni->uni_codigo,
                "label" => $uni->uni_desc,
                "data"  => $uni->toArray(),
            );
        }

        if (!count($out)) {
            $out[] = array(
                "id"    => 0,
                "label" => "Nenhum item encontrado",
                "data"  => array("uni_codigo" => "0", "uni_desc" => ""),
            );
        }

        return $out;
    }

    public function buscarCidadeDaUnidade($uni_codigo){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"))
            ->join(array("cid" => "cidade"), "uni.uni_codigo_ibge=cid.cid_codigo_ibge")
            ->where("uni_codigo = $uni_codigo")
            ->where("cnes_ativo != 'I'");
        return $this->fetchAll($where);
    }

    public function selectTag($where, $texto, $value = NULL, $first = NULL, $tag = true, $name = NULL, $id = NULL, $foco = false, $selected = 0, $action = false){
        $where = $this->select($value)->where("cnes_ativo != 'I'")->order("uni_desc");
        return parent::selectTag($where, "uni_desc", null, $first, true, null, null, null, $value);
    }

    /**
     * Retorna os laboratórios, unidades ou hospitais que tenham o $term no nome.
     * @param type $term
     * @return stdClass
     * @author Anderson Bernini
     */
    public function buscarLocais($term){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni_codigo", "uni_desc", "prestador_servico" => "('U')", "categoria" => "('Unidade')", "uni_cnes")) // prestador_servico: U
            ->where("cnes_ativo != 'I'")
            ->where("uni_desc ilike '%$term%'")
            ->order(array("uni_desc"));

        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $med) {
            $out[] = array(
                "id"    => $med->uni_codigo,
                "label" => $med->uni_desc,
                "data"  => $med->toArray(),
            );
        }

        if (!count($out)) {
            $out[] = array(
                "id"    => 0,
                "label" => "Nenhum item encontrado",
                "data"  => array("categoria" => "Nenhum item encontrado"),
            );
        }

        return $out;
    }

    public function getQtdUnidadesAtivasCnes(){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("COUNT(uni_codigo) AS qtd_uni"))
            ->where("cnes_ativo != 'I'");
        return $this->fetchRow($where);
    }

    public function getUnidadePorCnes($cnes){

        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo", "uni.uni_desc"))
            ->where("uni.cnes_ativo != 'I'")
            ->where("uni.uni_cnes = $cnes");

        //die($where);
        return $this->fetchRow($where);
    }

    public function buscaUnidadePorNome($nome_fantasia = false, $nome_fantasia_quebrado = false, $razao_social = false){
        $sql = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni_codigo", "uni_desc"));
        foreach ($nome_fantasia_quebrado as $nome) {
            $nomes_provavel .= $nome . " ";
            $sql->orWhere("uni.uni_desc ilike retira_acentos('%" . trim($nomes_provavel) . "%')");
        }
        return $this->fetchAll($sql);
    }

    public function getUnidade($uni_codigo = false){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni_codigo", "uni_desc", "uni_cnpj", 'cnes_tp_unid_id', 'uni_cnes'))
            ->where("cnes_ativo != 'I'");

        if ($uni_codigo) {
            $where->where("uni_codigo=$uni_codigo");
        }

        //die($where);
        return $this->fetchAll($where);
    }

    public function getCodUnidade(){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni_codigo"))
            ->where("cnes_ativo != 'I' or cnes_ativo is null");
        return $this->fetchAll($where);
    }

    public function importaEstabelecimentos($data){
        //error_reporting(E_ALL);
        if (empty($data)) {
            return false;
        }

        // echo "<pre>";
        // print_r($data); die();

        try {
            $complexidade = $data["complexidade"];
            unset($data["complexidade"]);
            $equipes = $data["equipes"];
            unset($data["equipes"]);

            $uni_valida = $this->verificaSeJáExiste($data["uni_cnes"]);
            
            /*print_r($uni_valida);
            die();*/

            if($uni_valida != NULL ){
                if ($uni_valida->qtd > 0) {
                    $data["uni_codigo"] = $uni_valida->uni_codigo;
                    $data["cnes_ativo"] = "A";
                }
            }

            $uni_codigo = $this->salvar($data);
            $this->salvaDependenciasUnidade($uni_codigo, $complexidade, $equipes);

        } catch (Exception $exc) {
            print_r("Erro ao importar unidades: <br>" . $exc->getMessage());
        }
        return true;

    }

    private function salvaDependenciasUnidade($uni_codigo = false, $complexidade = false, $equipes = false){
        $tbUnc = new Application_Model_UnidadeComplexidade();
        $tbEqp = new Application_Model_TbEquipe();
    
        foreach ($complexidade as $comp) {
            
            $data_comp = array(
                "co_complexidade" => $comp["co_complexidade"],
                "uni_codigo" => $uni_codigo
            );
            
            $unc_valida = $tbUnc->verificaSeJáExiste($comp["co_complexidade"], $uni_codigo);
            
            if($unc_valida != NULL){
                if ($unc_valida->qtd > 0) {
                    $data_comp["unc_codigo"] = $unc_valida->unc_codigo;
                    $data_comp["unc_ativo"]  = "A";
                }
            }

            $tbUnc->salvar($data_comp);
            
        }

        foreach ($equipes as $eqp) {
            // echo "<pre>";
            // print_r($eqp);
            // die();
            $data_eqp = array(
                "tp_equipe"  => $eqp["tp_equipe"],
                "no_equipe"  => $eqp["no_equipe"],
                "nu_ine"     => $eqp["nu_ine"],
                "ds_area"    => $eqp["ds_area"],
                "uni_codigo" => $uni_codigo,
                "co_unidade_saude" => $uni_codigo
            );

            $co_valida = $tbEqp->verificaSeJáExiste($eqp["nu_ine"], $uni_codigo);
            
            if($co_valida != NULL){
                if ($co_valida->qtd > 0) {
                    $data_eqp["co_seq_equipe"] = $co_valida->co_seq_equipe;
                    $data_eqp["st_ativo"] = "1";
                } else {
                    $data_eqp["st_ativo"] = "1";
                }
            } else  {
                $data_eqp["st_ativo"] = "1";
            }

            $tbEqp->salvar($data_eqp);
        }
        return true;
    }

    public function atualizaStatusGeral(){
        $where = $this->select()->getPart(Zend_Db_Table_Select::WHERE);
        
        //$where = $where[0];
        $data  = array('cnes_ativo' => 'A');
        return $this->update($data, $where);
    }

    public function unidadeSemCnes(){
        $sql = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"))
            ->where("uni_cnes is null")
            ->where("cnes_ativo != 'I' or cnes_ativo is null");
        return $this->fetchAll($sql);
    }

    public function getUnidades(){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"))
            ->where("cnes_ativo = 'A'")
            ->order("uni_desc");
        return $this->fetchAll($where);
    }

    public function getTodasUnidadesOrdenadoPorCnesAtivo(){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"))
            ->order("cnes_ativo")
            ->order("uni_desc");

        return $this->fetchAll($where);

    }

    public function getUnidadesAtendExportEsus($uni_codigo = false){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("eai" => "esus_atendimento_individual"), "eai.eai_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where->where("uni_codigo=$uni_codigo");
        }

        $where1 = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("eav" => "esus_atividade_coletiva"), "eav.eav_uni_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where1->where("uni_codigo=$uni_codigo");
        }

        $where2 = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("eci" => "esus_cadastro_individual"), "eci.eci_usr_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where2->where("uni_codigo=$uni_codigo");
        }

        $where3 = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("efp" => "esus_ficha_procedimento"), "efp.efp_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where3->where("uni_codigo=$uni_codigo");
        }

        $where4 = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("eo" => "esus_odonto"), "eo.eo_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where4->where("uni_codigo=$uni_codigo");
        }

        $where5 = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), array("uni.uni_codigo as uni_codigo", "uni.uni_desc as uni_desc"))
            ->join(array("esv" => "esus_visita_domiciliar"), "esv.esv_cnes::integer = uni.uni_cnes", "");
        if ($uni_codigo) {
            $where5->where("uni_codigo=$uni_codigo");
        }

        $whereUnion = $this->select()
            ->union(array($where, $where, $where1, $where2, $where3, $where4, $where5))
            ->group(array("uni_codigo", "uni_desc"))
            ->order("uni_desc");
        // die($whereUnion);
        return $this->fetchAll($whereUnion);

    }

    public function getDados($uni_codigo){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("uni" => "unidade"), "*")
            ->where("uni_codigo= ?", $uni_codigo);

        // $sql = $this->getDefaultAdapter()

        return $this->fetchRow($where);
    }

}