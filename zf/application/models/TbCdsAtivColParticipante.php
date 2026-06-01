<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsAtivColParticipante extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_ativ_col_participante';
    protected $_primary = 'co_cds_ativ_col_participnt';
    protected $_sequence = 'seq_co_cds_ativ_col_participnt';
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbp" => "tb_cds_ativ_col_participante"))
                ->join(array("usu"=>"usuario"),"tbp.usu_codigo=usu.usu_codigo",array("usu_nome"))
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
        return $this->fetchAll($sql);
    }
    
    //ID #105426
    public function getDadosPorPaciente($usu_codigo=FALSE,$data_inicial=FALSE,$data_final=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tcacp" => "tb_cds_ativ_col_participante"),"")
                ->join(array("tcfac"=>"tb_cds_ficha_ativ_col"),"tcfac.co_cds_ficha_ativ_col=tcacp.co_cds_ficha_ativ_col",array("data_atividade" =>"to_char(dt_ativ_col, 'DD/MM/YYYY')", "hora_inicio"=>"to_char(hr_inicio, 'HH24:MI')", "hora_fim"=>"to_char(hr_fim, 'HH24:MI')"))
                ->join(array("tctac"=>"tb_cds_tipo_ativ_col"),"tctac.co_cds_tipo_ativ_col=tcfac.tp_cds_ativ_col",array("no_cds_tipo_ativ_col"))
                ->join(array("usrs"=>"usuarios"),"usrs.usr_codigo=tcfac.usr_codigo",array("usr_nome"))
                ->where("tcacp.usu_codigo =?",$usu_codigo);
        
        if ($data_inicial){
           $sql->where("dt_ativ_col >= ?", $data_inicial);
        }   
        if ($data_final){
           $sql->where("dt_ativ_col <= ?", $data_final);
        }
        
        return $this->fetchAll($sql);
    }
    //ID #105426
    
    public function salvar($dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Participantes da atividade: ".$ex->getMessage());
        }
    }
    
    public function excluir($codFicha=FALSE){
        $item = $this->fetchAll("co_cds_ficha_ativ_col=$codFicha");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }


}
