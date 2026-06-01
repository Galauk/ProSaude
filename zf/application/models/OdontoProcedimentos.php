<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_OdontoProcedimentos extends Elotech_Db_Table_Abstract {
    
    protected $_name = "odonto_procedimentos";
    protected $_primary = "odo_proc_codigo";
    protected $_dependentTables = array();
    
    // Lista procedimento a ser realizados
    public function listaProcedimentos($tratCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odp"=>"odonto_procedimentos"),array("odo_proc_codigo","odo_proc_dentenum","odo_proc_denteface","odo_proc_denteanot","odo_proc_dtprogramada"))
                    ->join(array("proc"=>"procedimento"),"odp.proc_codigo=proc.proc_codigo",array("proc.proc_nome"))
                    ->join(array("odpc"=>"odonto_procedimentos_controle"),"odp.odo_pcon_codigo=odpc.odo_pcon_codigo","")
                    ->where("odpc.odo_trat_codigo =?",$tratCodigo)
                    ->where("odp.odo_proc_status = FALSE");
        return $this->fetchAll($sql);
    }
    
    // Pega os dados de um procedimento em especifico
    public function getProcedimento($procCodigo = FALSE) {
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odp"=>"odonto_procedimentos"),array("odo_proc_dentenum","odo_proc_denteface","odo_proc_denteanot","proc_codigo"))
                    ->join(array("proc"=>"procedimento"),"odp.proc_codigo=proc.proc_codigo",array("proc.proc_nome"))
                    ->where("odp.odo_proc_codigo =?",$procCodigo);
        //die($sql);
        return $this->fetchRow($sql);
    }
    
    public function getProcedimentosOdontologicos(){
        $tbUsr = new Application_Model_Usuarios();
        $esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
        
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("proc"=>"procedimento"),array("proc_codigo","proc_nome","proc_codigo_sus"))
                    ->join(array("rl"=>"rl_procedimento_ocupacao"), "rl.co_procedimento=proc.proc_codigo_sus","")
                    ->join(array("esp"=>"especialidade"),"esp.cod_cbo=rl.co_ocupacao","")
                    ->where("esp_codigo=$esp_codigo")
                    ->order("proc_nome");
        return $this->fetchAll($sql);
    }
    
    // Lista apenas o último procedimento inserido
    public function getUltimoProcedimento($tratCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odp"=>"odonto_procedimentos"),array("odp.odo_proc_codigo"))
                    ->join(array("odpc"=>"odonto_procedimentos_controle"),"odp.odo_pcon_codigo = odpc.odo_pcon_codigo","")
                    ->join(array("odt"=>"odonto_tratamento"),"odpc.odo_trat_codigo=odt.odo_trat_codigo","")
                    ->where("odt.odo_trat_codigo =?",$tratCodigo)
                    ->order("odp.odo_proc_codigo DESC")
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function buscaProcedimentosOdontologicos($term = false){
        $tbUsr = new Application_Model_Usuarios();
        $esp_codigo = $tbUsr->getUsrAtual()->esp_codigo;
        
        $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("proc"=>"procedimento"),array("proc_codigo","proc_nome","proc_codigo_sus"))
                    ->join(array("rl"=>"rl_procedimento_ocupacao"), "rl.co_procedimento=proc.proc_codigo_sus","")
                    ->join(array("esp"=>"especialidade"),"esp.cod_cbo=rl.co_ocupacao","")
                    ->where("esp_codigo=$esp_codigo")
                    ->where("proc_nome ilike '%$term%' OR proc_codigo_sus ilike '%$term%'")
                    ->order("proc_nome");
        
        $out = array();
        $all = $this->fetchAll($where);
        foreach ($all as $item) {
                $data = $item->toArray();
                $out [] = array(
                        "id" => $item->proc_codigo,
                        "label" => $item->proc_nome,
                        "data" => $data
                );
        }

        if (!count($out)) {
                $out [] = array(
                        "id" => 0,
                        "label" => "Nenhum item encontrado",
                        "data" => array("proc_codigo" => "0", "proc_nome" => "")
                );
        }
        
       return $out;
        
    }
    
    /*
    
    // Salva e atualiza o procedimento
    public function salvar($data){
        return parent::salvar($data);
    }
    */
    
    public function getTotalTratamentosPorProcOdonto($data_ini=false,$data_fim=false,$ine=false,$proc_codigo=false){
        
                $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ot" => "odonto_tratamento"), array("count(*) total"))
                        ->joinLeft(array("opc" => "odonto_procedimentos_controle"), "opc.odo_trat_codigo=ot.odo_trat_codigo", "")
                        ->joinLeft(array("opr" => "odonto_procedimentos_realizados"), "opc.odo_pcon_codigo=opr.odo_pcon_codigo", "")
                        ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo = opc.ate_codigo", "")
                        ->joinLeft(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                        ->joinLeft(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","");
                        //->where("ot.odo_trat_status='A'");
        
                        if($data_ini){
                            $where->where("ate_data >= ?",$data_ini);
                        }
                        if($data_fim){
                            $where->where("ate_data <= ?",$data_fim);
                        }
                        if($ine){
                            $where->where("nu_ine = ?",$ine);
                        }
                        if($proc_codigo){
                            $where->where("opr.proc_codigo = ?",$proc_codigo);
                        }
                        
                die($where);
                return $this->fetchRow($where);
            }
    
}

?>
