<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Procedimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'procedimento';
    protected $_primary = 'proc_codigo';
    protected $_dependentTables = array();
    protected $_sequence = 'seq_proc_codigo';

    public function salvar(array $data) {
        $this->addRealName(array("proc_nome" => "descriÃ§Ã£o", "proc_sexo_novo" => ""));

        $this->notEmpty(array("proc_nome", "proc_sexo_novo"), $data);

        $data['proc_nome'] = strtoupper($data['proc_nome']);
        $this->emptyToUnset($data);

        return parent::salvar($data);
    }

    public function salvarApelido(array $data) {
        return parent::salvar($data);
    }

    /**
     * Retorna um select com as especialidades vinculadas ao profissional logado
     * Obs.: somente os procedimentos vinculados Ã  especialidade logada.
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
                ->group(array("p.proc_codigo", "a.ate_data"))
                ->order("proc_nome");
        return parent::selectTag($where, "proc_nome", NULL, TRUE, TRUE, NULL, NULL, TRUE);
    }

    /**
     * Buscar os procedimentos
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function buscar($term, $esp_codigo = FALSE) {
        if ($esp_codigo) {
            $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->join(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento = proc.proc_codigo_sus", 
                    "co_procedimento")
                ->join(array("esp" => "especialidade"), "esp.cod_cbo=rpo.co_ocupacao", "")
                ->where("esp.esp_codigo = ?", $esp_codigo);
        } else {
            $where = $this->select(FALSE)
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido", "(proc_codigo)||'' AS co_procedimento"));
        }

        if (is_numeric($term)) {
            $where->where("proc_codigo_sus ilike '$term%'");
        } else {
            $where->where("retira_acentos(proc_nome) ilike retira_acentos('%$term%') OR retira_acentos(proc_apelido) ilike retira_acentos('%$term%')");
        }
        
        $especiais = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->joinLeft(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento=proc.proc_codigo_sus", "co_procedimento")
                ->where("rpo.co_procedimento IS NULL")
                ->order("proc_nome");
        
        if (is_numeric($term)) {
            $especiais->where("proc_codigo_sus ilike '$term%' AND proc_ativo = 'A'");
        } else {
            $especiais->where("(retira_acentos(proc_nome) ilike retira_acentos('%$term%') OR retira_acentos(proc_apelido) ilike retira_acentos('%$term%')) AND proc_ativo = 'A'");
        }
        
        $geral = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where, $especiais), Zend_Db_Select::SQL_UNION);

        $all = $this->fetchAll($geral);
        
        $out = array();
        foreach ($all as $proc) {
            if ($proc->proc_apelido) {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome."(".$proc->proc_apelido.")"),
                    "data" => $proc->toArray()
                );
            } else {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome),
                    "data" => $proc->toArray()
                );
            }
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
    
    public function buscarAtivos($term, $esp_codigo = FALSE) {
        if ($esp_codigo) {
            $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo","procedimento_ab","codigo_ab", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->join(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento=proc.proc_codigo_sus", "co_procedimento")
                ->join(array("esp" => "especialidade"), "esp.cod_cbo=rpo.co_ocupacao", "")
                ->where("esp.esp_codigo = $esp_codigo ");
        } else {
            $where = $this->select(FALSE)
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo","codigo_ab","procedimento_ab", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido", "(proc_codigo)||'' AS co_procedimento"));
        }

        if (is_numeric($term)) {
            $where->where("proc_codigo_sus ilike '$term%' and proc_exame = 'N'");
        } else {
            //$where->where("(retira_acentos(proc_nome) ilike retira_acentos('%$term%') OR retira_acentos(proc_apelido)ilike retira_acentos('%$term%')) AND proc_ativo = 'A'");
            $where->where("retira_acentos(proc_nome) ilike retira_acentos('%$term%') ");
        }
        
        $especiais = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome","procedimento_ab","codigo_ab", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->joinLeft(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento=proc.proc_codigo_sus", "co_procedimento")
                ->where("rpo.co_procedimento IS NULL")
                ->order("proc_nome");
        
        if (is_numeric($term)) {
            $especiais->where("proc_codigo_sus ilike '$term%' and proc_exame = 'N'");
        } else {
            //$especiais->where("(retira_acentos(proc_nome) ilike retira_acentos('%$term%') OR retira_acentos(proc_apelido) ilike retira_acentos('%$term%')) AND proc_ativo = 'A'");
            $especiais->where("retira_acentos(proc_nome) ilike retira_acentos('%$term%') and proc_exame = 'N'");
        }
        
        $geral = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where, $especiais), Zend_Db_Select::SQL_UNION);
        // die($geral);
        $all = $this->fetchAll($geral);

        $out = array();
        foreach ($all as $proc) {
            if ($proc->proc_apelido) {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome."(".$proc->proc_apelido.")"),
                    "data" => $proc->toArray()
                );
            } else {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome."(".$proc->procedimento_ab.")"),
                    "data" => $proc->toArray()
                );
            }
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

    public function buscarProcedimentoNaoSelecionadoNoCombo($term, $esp_codigo = false, $procedimentos = false) {

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

        if ($procedimentos) {
            $where->where("proc.proc_codigo not in ($procedimentos)");
        }
        //echo $where;
        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $proc) {
            if ($proc->proc_apelido) {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome."(".$proc->proc_apelido.")"),
                    "data" => $proc->toArray()
                );
            } else {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome),
                    "data" => $proc->toArray()
                );
            }
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

    public function buscarProcNaoRealizado($term, $esp_codigo = FALSE, $age = FALSE, $usu = FALSE) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_vlsa", "proc_codigo_sus", "proc_apelido"))
                ->order("proc_nome");

        if ($age) {
            $where->where("proc.proc_codigo not in
                            (
                            select pat.proc_codigo
                            from procedimento_atendimento as pat
                            join atendimento as ate
                              on pat.ate_codigo = ate.ate_codigo
                            join agendamento as age
                              on ate.age_codigo = age.age_codigo
                            where age.age_codigo = $age
                            and pat.usr_codigo = $usu)");
        }

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
            if ($proc->proc_apelido) {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome."(".$proc->proc_apelido.")"),
                    "data" => $proc->toArray()
                );
            } else {
                $out [] = array(
                    "id" => $proc->proc_codigo,
                    "label" => trim($proc->proc_nome),
                    "data" => $proc->toArray()
                );
            }
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

    public function buscarExames($term, $esp_codigo = FALSE) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo as exame_codigo", "proc_nome as exame_nome", "proc_codigo_sus as exame_cod_sus", "proc_apelido as exame_apelido"))
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
            $where->where("retira_acentos(proc_nome) ilike retira_acentos('%$term%') AND codigo_ab <> '' OR retira_acentos(proc_apelido) ilike retira_acentos('%$term%')");
        }
        // die($where);
        $all = $this->fetchAll($where);

        $out = array();
        foreach ($all as $proc) {
            if ($proc->exame_apelido) {
                $out [] = array(
                    "id" => $proc->exame_codigo,
                    "label" => trim($proc->exame_nome."(".$proc->exame_apelido.")"),
                    "data" => $proc->toArray()
                );
            } else {
                $out [] = array(
                    "id" => $proc->exame_codigo,
                    "label" => trim($proc->exame_nome),
                    "data" => $proc->toArray()
                );
            }
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

    public function getCboPorCodigoSus($codigoSus) {

        $sql = $this->select(FALSE)
                ->distinct(TRUE)
                ->setIntegrityCheck(FALSE)
                ->from(array("p" => "procedimento"), array(""))
                ->join(array("rpo" => "rl_procedimento_ocupacao"), "rpo.co_procedimento = p.proc_codigo_sus", "")
                ->join(array("e" => "especialidade"), "e.cod_cbo = rpo.co_ocupacao", "e.cod_cbo")
                ->where("p.proc_codigo_sus = '$codigoSus'")
                ->order("e.cod_cbo ASC");

        return $this->fetchAll($sql)->toArray();
    }
    
    public function buscaOutrosProcedimentosColetivos() {
        
        $sql = $this->select(FALSE)
                ->distinct(TRUE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_codigo", "proc_nome", "proc_codigo_sus"))
                ->where("proc.proc_codigo_sus in ('0101010044','0101020082','0101010052','0101010060','0101010079','0101010087','0101020023','0101020040')")
                ->order("proc.proc_nome ASC");
                
        // die($sql);
        return $this->fetchAll($sql);
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
        return $this->fetchAll($sql);
    }

    public function selectTagProcEsp($ate_codigo=FALSE) {
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        if($ate_codigo!='') {
        $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("p" => "procedimento"), array("proc_codigo", "proc_nome"))
                ->join(array("rl" => "rl_procedimento_ocupacao"), "rl.co_procedimento=p.proc_codigo_sus", "")
                ->join(array("e" => "especialidade"), "e.cod_cbo=rl.co_ocupacao", "")
                ->joinLeft(array("pa" => "procedimento_atendimento"), "pa.proc_codigo=p.proc_codigo", "")
                ->joinLeft(array("a" => "atendimento"), "a.ate_codigo=pa.ate_codigo", "ate_data")
                ->where("a.ate_codigo=?", $ate_codigo)
                ->group(array("p.proc_codigo", "a.ate_data"))
                ->order("proc_nome");
         $sel =  $this->fetchRow($where2);      
                 $selecionado = $sel->proc_codigo;
        }
         $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("p" => "procedimento"), array("proc_codigo", "proc_nome"))
                ->join(array("ep" => "rl_procedimento_ocupacao"), "ep.co_procedimento = p.proc_codigo_sus", "")
                ->join(array("esp" => "especialidade"), "esp.cod_cbo = co_ocupacao", "")
                ->where("esp.esp_codigo=?", $usr->esp_codigo)
                ->order("proc_nome");
        //Enfermeiro da estratégia de saúde da família
        if ($usr->esp_codigo == 353) {
            //CONSULTA DE PROFISSIONAIS DE NIVEL SUPERIOR NA ATENÇÃO BÁSICA (EXCETO MÉDICO)
            if($usr->cnes_tp_unid_id=='05') {
                $selecionado = 5439;
            } else {
                $selecionado = 5438;                
            }
        }
        //Médico da estratégia de saúde da família
        if (($usr->esp_codigo == 380 OR $usr->esp_codigo== 1054 OR $usr->esp_codigo== 1086 OR $usr->esp_codigo== 1095)) {
            //CONSULTA MEDICA EM ATENÇAO BASICA
            if($usr->cnes_tp_unid_id=='05') {
                $selecionado = 5442;
            } else {
                $selecionado = 5441;                
            }
        }
        return parent::selectTag($where, "proc_nome", NULL, TRUE, TRUE, NULL, NULL, TRUE, $selecionado);
    }

    public function getCiapComum($tipo=NULL){
        $sql = $this->getDefaultAdapter()->query("
            select ciap.co_ciap,ds_ciap,count(rl.ate_codigo) as total from rl_cds_atend_individual_ciap as rl
            join atendimento as ate on ate.ate_codigo = rl.ate_codigo
            join tb_ciap as ciap on rl.co_ciap = ciap.co_seq_ciap
            group by ds_ciap,ciap.co_ciap
            order by total desc
             limit 10")->fetchAll();
        return $sql;

    }

    public function recuperaProcedimentosAB(){
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT distinct proc.proc_codigo, proc.procedimento_ab,  proc.proc_nome, proc.codigo_ab,  proc.proc_vlsa,  proc.proc_codigo_sus, proc.proc_apelido, rpo.co_procedimento 
                FROM procedimento AS proc 
                    INNER JOIN rl_procedimento_ocupacao AS rpo 
                        ON rpo.co_procedimento = proc.proc_codigo_sus 
                    INNER JOIN especialidade AS esp 
                        ON esp.cod_cbo = rpo.co_ocupacao 
                where proc.codigo_ab <> '' AND procedimento_ab <> ''
            "
        )->fetchAll();
        return $sql;
    }

    public function recuperaProcedimentosOdonto($term){
        $recebeTermo = $term;
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM procedimento WHERE procedimento_odonto_ab <> '' AND proc_nome ilike '%$recebeTermo%' 
            "
        )->fetchAll();

        $all = $sql;
        // echo "<pre>";print_r($all);die();
        // error_reporting(E_ALL);
        $out = array();
        foreach ($all as $proc) {
            // $proc = (object) $proc;
            if ($proc[proc_apelido]) {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]."(".$proc[proc_apelido].")"),
                    "data" => $proc
                );
            } else {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]),
                    "data" => $proc
                );
            }
        }
        // echo "<pre>";print_r($out);die();

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array()
            );
        }

        return $out;
    }

    public function recuperaBeneficioConcedido($term){
        $recebeTermo = $term;
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM procedimento WHERE procedimento_tipo_beneficio = 't' AND proc_nome ilike '%$recebeTermo%' 
            "
        )->fetchAll();

        $all = $sql;
        // echo "<pre>";print_r($all);die();
        // error_reporting(E_ALL);
        $out = array();
        foreach ($all as $proc) {
            // $proc = (object) $proc;
            if ($proc[proc_apelido]) {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]."(".$proc[proc_apelido].")"),
                    "data" => $proc
                );
            } else {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]),
                    "data" => $proc
                );
            }
        }
        // echo "<pre>";print_r($out);die();

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array()
            );
        }

        return $out;
    }

    public function recuperaProcedimentosABOdonto(){
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM PROCEDIMENTO where procedimento_odonto_ab <> ''
            "
        )->fetchAll();

        return $sql;
    }


    public function buscaAcoesRaas($term)
    {
        $recebeTermo = $term;
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM procedimento WHERE proc_nome ilike '%$recebeTermo%' AND proc_codigo_sus ilike '030108%'
            "
        )->fetchAll();

        $all = $sql;
        
        $out = array();
        foreach ($all as $proc) {
            // $proc = (object) $proc;
            if ($proc[proc_apelido]) {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]."(".$proc[proc_apelido].")"),
                    "data" => $proc
                );
            } else {
                $out [] = array(
                    "id" => $proc[proc_codigo],
                    "label" => trim($proc[proc_nome]),
                    "data" => $proc
                );
            }
        }
        // echo "<pre>";print_r($out);die();

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array()
            );
        }

        return $out;
    }

}