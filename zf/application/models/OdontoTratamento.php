<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_OdontoTratamento extends Elotech_Db_Table_Abstract {
    
    protected $_name = 'odonto_tratamento';
    protected $_primary = 'odo_trat_codigo';
    protected $_dependentTables = array();

    // Pega o último tratamento
    public function getCodigoTratamentoAtual($usu_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odo_trat"=>"odonto_tratamento"),array("odo_trat.odo_trat_codigo","odo_trat_dtinicial","odo_trat_dtfinal","odo_trat_status"))
                    ->join(array("ate"=>"atendimento"), "odo_trat.ate_codigo_origem=ate.ate_codigo","")
                    ->where("odo_trat_status = 'A'")
                    ->where("ate.usu_codigo =?",$usu_codigo)
                    ->limit(1)
                    ->order("odo_trat_codigo DESC");
        return $this->fetchRow($sql);
    }
    
    // Confere se o tratamento ainda possui atendimento a ser realizado
    public function getQtdAtendimentoFaltanteTratamento($tratCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odp"=>"odonto_procedimentos"),array("count(odp.odo_proc_codigo) as qtd_atendimento"))
                    ->join(array("odpc"=>"odonto_procedimentos_controle"),"odp.odo_pcon_codigo=odpc.odo_pcon_codigo","")
                    ->join(array("odt"=>"odonto_tratamento"),"odpc.odo_trat_codigo = odt.odo_trat_codigo","")
                    ->join(array("ate"=>"atendimento"),"odt.ate_codigo_origem=ate.ate_codigo","")
                    ->where("odp.odo_proc_status = 'F'")
                    ->where("odt.odo_trat_codigo =?",$tratCodigo);
        return $this->fetchRow($sql);
    }
    
    // Salva um novo tratamento ou edita de acordo com o agendamento
    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha so salvar tratamento: ".$exc->getMessage());
        }
    }
    
    // Lista os tratamentos de acordo com on código do usuário
    public function listaTratamentosRealizados($usu_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odt"=>"odonto_tratamento"),array("odt.odo_trat_codigo","odt.odo_trat_dtinicial","odt.odo_trat_dtfinal"))
                    ->join(array("ate"=>"atendimento"), "odt.ate_codigo_origem=ate.ate_codigo","")
                    ->where("odt.odo_trat_status = 'F'")
                    ->where("ate.usu_codigo =?",$usu_codigo)
                    ->order("odt.odo_trat_codigo DESC");
        return $this->fetchAll($sql);
    }
    
    public function getTratamentoAberto($usu_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odo_trat"=>"odonto_tratamento"),array("odo_trat.odo_trat_codigo","odo_trat_dtinicial","odo_trat_dtfinal","odo_trat_status"))
                    ->join(array("age"=>"agendamento"), "odo_trat.age_codigo=age.age_codigo","")
                    ->where("odo_trat_status = 'A'")
                    ->where("age.usu_codigo =?",$usu_codigo)
                    ->limit(1)
                    ->order("odo_trat_codigo DESC");
        return $this->fetchRow($sql);
    }
    
    public function excluirPorAtendimento($ate_codigo=FALSE){
        $item = $this->fetchRow("ate_codigo_origem=$ate_codigo");
        try{
            if ($item) { $item->delete(); }
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir tratamento: ".$ex->getMessage());
        }
        return true;
    }

    /*
    
    // Pega código do tratamento
    public function getCodigoTratamento($usuCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odt"=>"odonto_tratamento"),array("odo_trat_codigo"))
                    ->join(array("age"=>"agendamento"),"odt.age_codigo=age.age_codigo","")
                    ->where("usu_codigo =?",$usuCodigo);
       return $this->fetchRow($sql);
    }
    
    // Confere se a tratamento em andamento para o usuario
    public function confereTratamento($usuCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odt"=>"odonto_tratamento"),"count(odo_trat_codigo)")
                    ->join(array("age"=>"agendamento"),"odt.age_codigo=age.age_codigo","")
                    ->where("odo_trat_status = 'A'")
                    ->where("usu_codigo =?",$usuCodigo);
        //die($sql);
        return $this->fetchRow($sql);
    }
    
     * 
     */
}
    
?>
