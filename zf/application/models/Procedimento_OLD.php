<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Procedimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'procedimento';
    protected $_primary = 'proc_codigo';
    protected $_dependentTables = array();
    protected $_sequence = 'seq_proc_codigo';

    public function salvar(array $data) {
        $this->addRealName(array("proc_nome" => "descrição", "proc_sexo_novo" => ""));

        $this->notEmpty(array("proc_nome", "proc_sexo_novo"), $data);

        $data['proc_nome'] = strtoupper($data['proc_nome']);
        $this->emptyToUnset($data);

        return parent::salvar($data);
    }

    public function salvarApelido($data = false) {
        return parent::salvar($data);
    }

    /**
     * Retorna um select com as especialidades vinculadas ao profissional logado
     * Obs.: somente os procedimentos vinculados à especialidade logada.
     * @return string <select>
     */
    public function selectTag() {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("p" => "procedimento"), array("proc_codigo", "proc_nome"))
                ->join(array("rl" => "rl_procedimento_ocupacao"), "rl.co_procedimento=p.proc_codigo_sus", "")
                ->join(array("e" => "especialidade"), "e.cod_cbo=rl.co_ocupacao", "")
                ->joinLeft(array("pa" => "procedimento_atendimento"), "pa.proc_codigo=p.proc_codigo", "")
                ->joinLeft(array("a" => "atendimento"), "a.ate_codigo=pa.ate_codigo", "ate_data")
                ->where("e.esp_codigo=?", $usr->esp_codigo)
                ->order("proc_nome");
                // die($where);
        return parent::selectTag($where, "proc_nome", NULL, TRUE, TRUE, NULL, NULL, TRUE);
    }

    public function selectTagProcEsp() {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("p" => "procedimento"), array("proc_codigo", "proc_nome"))
                ->join(array("ep" => "especialidade_procedimento"), "ep.proc_codigo = p.proc_codigo", "")
                ->where("ep.esp_codigo=?", $usr->esp_codigo)
                ->order("proc_nome");
        $selecionado = 0;
        //Enfermeiro da estratégia de saúde da família
        if ($usr->esp_codigo == 353) {
            //CONSULTA DE PROFISSIONAIS DE NIVEL SUPERIOR NA ATENÇÃO BÁSICA (EXCETO MÉDICO)
            $selecionado = 5438;
        }
        //Médico da estratégia de saúde da família
        if ($usr->esp_codigo == 380) {
            //CONSULTA MEDICA EM ATENÇAO BASICA
            $selecionado = 5441;
        }

        return parent::selectTag($where, "proc_nome", NULL, TRUE, TRUE, NULL, NULL, TRUE, $selecionado);
    }

    /**
     * Buscar os procedimentos
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscar($term, $esp_codigo = FALSE) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->order("proc_nome");

        if ($esp_codigo) {
            $where->where("esp.esp_codigo = ?", $esp_codigo)
                    ->join(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento=proc.proc_codigo_sus", "")
                    ->join(array("esp" => "especialidade"), "esp.cod_cbo=rpo.co_ocupacao", "");
        } else {
            $where->distinct();
        }

        if (is_numeric($term)) {
            $where->where("proc_codigo_sus ilike '$term%'");
        } else {
            $where->where("retira_acentos(proc_nome) ilike retira_acentos('%$term%') OR retira_acentos(proc_apelido) ilike retira_acentos('%$term%')");
        }

        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $proc) {
            $out [] = array(
                "id" => $proc->proc_codigo,
                "label" => trim($proc->proc_nome),
                "data" => $proc->toArray()
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array()
            );
        }

        return $out;
    }

    /**
     * Transforma um Zend_Db_Table_Rowset_Abstract em uma string, concatenando os procedimentos com virgula
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     * @return string
     */
    public function procToStr($rowset) {
        $out = array();
        foreach ($rowset as $proc)
            $out [] = $proc->proc_nome;

        return implode(", ", $out);
    }

    /**
     *
     * @return type
     */
    public function getItensCadastrados() {
        return $this->fetchAll("proc_cadastrado_manualmente='t'", "proc_nome DESC", 15);
    }

    public function excluir($proc_codigo) {
        $item = $this->fetchRow("proc_codigo=$proc_codigo");
        if ($item)
            $item->delete();

        return true;
    }

    public function editar($proc_codigo) {
        return $this->fetchRow("proc_codigo=$proc_codigo");
    }

    public function pesquisar($dados) {
        $where = $this->select(true);
        if (is_string($dados))
            $where->where("proc_nome ilike '%$dados%'");
        if (is_double($dados))
            $where->where("proc_valor = ?", (double) $dados);
        if (is_int($dados))
            $where->where("proc_codigo = ?", (int) $dados);

        $where->where("proc_cadastrado_manualmente='t'");

        return $this->fetchAll($where);
    }

    /**
     *
     * @return type
     */
    public function getProcedimentoPeloCodigoSus($proc_codigo_sus) {

        return $this->fetchRow("proc_codigo_sus = '$proc_codigo_sus'");
    }

    public function getProcedimentosComApelidos() {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("procedimento")
                ->where("proc_apelido is not null");
        return $this->fetchAll($where);
    }

    public function buscarProcedimentosComApelidos($term = FALSE) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"))
                ->where("proc_nome ilike '%$term%' OR proc_apelido ilike '%$term%'")
                ->where("proc_apelido is not null");
        return $this->fetchAll($where);
    }

    public function listaProcedimentosDuplicados() {
        $sql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome"))
                ->where("proc_codigo_sus IN (SELECT proc_codigo_sus FROM procedimento where proc_codigo_sus is not null GROUP BY proc_codigo_sus HAVING count(*) > 1)");


        //die($sql);
        return  $this->fetchAll($sql);
    }

}
