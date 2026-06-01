<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ProcedimentoAtendimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'procedimento_atendimento';
    protected $_primary = 'pat_codigo';
    protected $_sequence = 'seq_pat_codigo';
    protected $_dependentTables = array();

    const ATENDIMENTO = "ate_codigo";
    const POSTO_ENFERMAGEM = "pe_codigo";
    const PRE_CONSULTA = "pc_codigo";
    
    /*-----------------------------------------------------------------------/
     * OBS: Este método também é utilizado para salvar os seguintes modúlos: 
     * Atendimento Simplificado 
     * ----------------------------------------------------------------------*/
    public function salvar($data, $obs = FALSE, $json = FALSE) {
//die("asdfasdf");
        $this->addRealName(array("proc_codigo" => "procedimento"));
        $this->filterDigits(array("proc_codigo", "ate_codigo", "pe_codigo", "cd10_codigo"), $data);

        if (empty($data['pe_codigo']) && !$json) { // atendimento?
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimento();
            if (!$obs) {
                if (!$ate) {
                    $tbPre = new Application_Model_PreConsulta();
                    $pre = $tbPre->getUltima();
                    if ($pre) {
                        $data['pc_codigo'] = $pre->pc_codigo;
                    }
                } else {
                    $data['ate_codigo'] = $ate->ate_codigo;
                }
            }
        }
        $tbUsr = new Application_Model_Usuarios();
        $data['usr_codigo'] = $tbUsr->getUsrAtual()->usr_codigo;
        $this->emptyToUnset($data);
        $this->maiorQueZero(array("proc_codigo", "cd10_codigo"), $data, array("cd10_codigo" => true));
        $this->peloMenosUm(array("pc_codigo", "ate_codigo", "pe_codigo", "si_codigo"), $data);
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o Procedimento".$exc->getMessage());
        }
    }
    
    public function salvarProcedimentosAtendimento($data){
        try {        
               if($data["cd10_codigo"] == "null"){
                   unset($data['cd10_codigo']);
               }

            return parent::salvar($data);
        } catch (Exception $exc) {
            
            throw new Zend_Validate_Exception("Falha ao cadastrar o Procedimento".$exc->getMessage());
        }
    }

    /**
     * Traz todos os procedimemtos realizados em um paciente
     * @param int $usu_codigo
     * @param string $data_inicial Opcional. Proceimento realizados a partir esta data (inclusive)
     * @param string $data_final Opcional. Proceimento realizados até esta data (inclusive)
     */
    public function getHistoricoPorPaciente($usu_codigo, $data_inicial = FALSE, $data_final = FALSE) {
        $sqlAte = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pa" => "procedimento_atendimento"), "pat_codigo")
                ->joinLeft(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pa.ate_codigo", array("ate_codigo", "ate_hora", "ate_diagnostico"))
                ->joinLeft(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "age_data")
                ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=age.med_codigo", "usr_nome")
                ->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
                ->where("ate.usu_codigo=?", $usu_codigo);

        if ($data_inicial) {
            $sqlAte->where("age.age_data >= ?", $data_inicial);
        }

        if ($data_final) {
            $sqlAte->where("age.age_data <= ?", $data_final);
        }

        $sqlPre = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pa" => "procedimento_atendimento"), "pat_codigo")
                ->join(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pa.ate_codigo", array("ate_codigo", "ate_hora", "ate_diagnostico"))
                ->joinLeft(array("pre" => "pre_consulta"), "pre.pc_codigo=pa.pc_codigo", "")
                ->joinLeft(array("age" => "agendamento"), "age.age_codigo=pre.age_codigo", "age_data")
                ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=age.med_codigo", "usr_nome")
                ->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
                ->where("age.usu_codigo=?", $usu_codigo);
        if ($data_inicial)
            $sqlPre->where("age.age_data >= ?", $data_inicial);

        if ($data_final)
            $sqlPre->where("age.age_data <= ?", $data_final);

//die($where);
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($sqlAte, $sqlPre), Zend_Db_Select::SQL_UNION)
                ->order(array("age_data DESC", "ate_hora DESC"));


        return $this->fetchAll($where);
    }

    public function getHistoricoInternacao($io_codigo, $limit = false) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ati" => "atendimento_internacao"), "ati.io_codigo")
                ->joinLeft(array("ate" => "atendimento"), "ati.ate_codigo = ate.ate_codigo", "ate.ate_codigo")
                ->join(array("pat" => "procedimento_atendimento"), "ate.ate_codigo = pat.ate_codigo", "pat.pat_codigo")
                ->joinLeft(array("p" => "procedimento"), "p.proc_codigo=pat.proc_codigo", "proc_nome")
                ->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pat.cd10_codigo", "cd10_descricao")
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=pat.usr_codigo", "usr_nome")
                ->where("ati.io_codigo=?", $io_codigo)
                ->order(array("ate.ate_data", "proc_nome"));
        /* if($limit){
          $where->limit($limit);
          } */
        return $this->fetchAll($where);
    }
    
    // FUNÇÃO #96579
    // Busca os atendimentos pelo ate_codigo
    
    public function getAtendimentoPorAteCodigo($ate_codigo = false) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pa" => "procedimento_atendimento"))
                ->join(array("proc" => "procedimento"), "pa.proc_codigo=proc.proc_codigo" , array("proc.proc_codigo_sus","proc.proc_nome"))
                ->where("pa.ate_codigo=?", $ate_codigo);
                // die($where);
        return $this->fetchAll($where);
    }
    
    // FUNÇÃO #96579

    public function getHistoricoPorAgendamento($age_codigo) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("age" => "agendamento"), "")
                ->join(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
                ->join(array("esp" => "especialidade"), "esp.esp_codigo=age.esp_codigo", "esp_nome")
                ->joinLeft(array("ate" => "atendimento"), "ate.age_codigo=age.age_codigo", "")
                ->joinLeft(array("pc" => "pre_consulta"), "pc.age_codigo=age.age_codigo", "")
                ->joinLeft(array("pe" => "posto_enfermagem"), "pe.ate_codigo=ate.ate_codigo", "")
                ->joinLeft(array("pat" => "procedimento_atendimento"), "pat.ate_codigo=ate.ate_codigo OR pat.pc_codigo=pc.pc_codigo OR pat.pe_codigo=pe.pe_codigo", "")
                ->joinLeft(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
                ->where("age.age_codigo=?", $age_codigo)
                ->group(array("proc_nome", "uni_desc", "esp_nome"))
                ->order("proc_nome");

        return $this->fetchAll($where);
    }

    public function getHistoricoGeral() {
        $tbAte = new Application_Model_Atendimento();
        $ate = $tbAte->temAtendimento();
        if ($ate) {
            return $this->getHistorico($ate->age_codigo, self::ATENDIMENTO);
        } else {
            return $this->getHistorico(FALSE, self::PRE_CONSULTA);
        }
    }

    public function getHistorico($age_codigo=FALSE) {
        $tbAte = new Application_Model_Atendimento();

        //Não é 100% de certeza que não quebrou nada. Verificar utilização da variavel $age_codigo.
        //die($age_codigo."a");
        if (!$age_codigo) {            
            $age = Application_Model_Agendamento::usuEmAberto();
            $age_codigo = $age->age_codigo;
        }
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pa" => "procedimento_atendimento"), "pa.pat_codigo")
                ->join(array("p" => "procedimento"), "p.proc_codigo=pa.proc_codigo", "proc_nome")
                ->joinLeft(array("c" => "cid10"), "c.cd10_codigo=pa.cd10_codigo", "cd10_descricao")
                ->joinLeft(array("pc" => "pre_consulta"), "pc.pc_codigo=pa.pc_codigo", "")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pa.ate_codigo", "")
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=pa.usr_codigo", "usr_nome")
                ->where("ate.age_codigo=$age_codigo OR pc.age_codigo=$age_codigo");
        //die($where);

        return $this->fetchAll($where);
    }

    public function getHistoricoPostoEnfermagem() {
        
    }

    /**
     * Retorna as informações de um procedimento realizado
     * Busca nas tabelas PC, PE e ATE
     * @param int $pat_codigo
     * @return Zend_Db_Table_Row_Abstract
     */
    public function buscar($pat_codigo) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pat" => "procedimento_atendimento"), "pat_codigo")
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=pat.usr_codigo", "usr_nome")
                ->join(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
                ->joinLeft(array("cd10" => "cid10"), "cd10.cd10_codigo=pat.cd10_codigo")
                ->joinLeft(array("pe" => "posto_enfermagem"), "pe.pe_codigo=pat.pe_codigo", "")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pe.ate_codigo OR ate.ate_codigo=pat.ate_codigo", array("ate_codigo", "ate_data"))
                ->joinLeft(array("pc" => "pre_consulta"), "pc.pc_codigo=pat.pc_codigo", "")
                ->joinLeft(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo OR age.age_codigo=pc.age_codigo", "")
                ->joinLeft(array("esp" => "especialidade"), "esp.esp_codigo=pc.esp_codigo OR esp.esp_codigo=pe.esp_codigo OR esp.esp_codigo=age.esp_codigo", "esp_nome")
                ->joinLeft(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", "usu_nome")
                ->where("pat.pat_codigo=?", $pat_codigo);
        //die($where);
        return $this->fetchRow($where);
    }

    public function excluir($pat_codigo) {
        return $this->delete("pat_codigo=$pat_codigo");
    }
    
    public function excluirProcedimentosAtendimento($ate_codigo=FALSE){
        $item = $this->fetchAll("ate_codigo=$ate_codigo");
        if ($item) {
            foreach ($item as $value) {
                $this->delete("pat_codigo=$value->pat_codigo");
            }
        }    
        return true;
    }
    
    public function verificaSeRealizou($dados){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("pat"=>"procedimento_atendimento"),"count(*) as qtde")
                      ->where("proc_codigo=$dados[proc_codigo]");
        
        if($dados[pc_codigo])
            $where->where("pc_codigo=$dados[pc_codigo]");
        
        $row = $this->fetchRow($where)->qtde;
        if($row >= 1){
            return false;
        }else{
            return true;
        }
    }

}
