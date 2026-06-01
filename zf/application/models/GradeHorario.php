<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_GradeHorario extends Elotech_Db_Table_Abstract {

    protected $_name = 'grade_horario';
    protected $_primary = 'grah_codigo';

    public function salvar(array $data) {
		$this->notEmpty(array("coni_codigo","grah_dia","grah_hora"), $data);
        return parent::salvar($data);
    }
	
	
    public function getHorarioCancelado($horario=FALSE,$coni_codigo=FALSE,$data=FALSE){
           $sql = $this->select()
                       ->setIntegrityCheck(FALSE)
                       ->from(array("grah"=>"grade_horario"),array("count(grah_codigo) as quantidade","grah_motivo"))
                       ->where("coni_codigo=?",$coni_codigo)
                       ->where("grah_dia=?",$data)
                       ->where("grah_hora=?",$horario)
                     ->group("grah_motivo");
           return $this->fetchRow($sql);
    }
    
    public function getHorarios($data=FALSE,$coni_codigo=FALSE,$grah_codigos=FALSE){
           $sql = $this->select()
                       ->setIntegrityCheck(FALSE)
                       ->from(array("grah"=>"grade_horario"))
                       ->join(array("mof"=>"motivos_faltas"),"mof.mof_codigo=grah.mof_codigo","mof_descricao")
                       ->where("coni_codigo=?",$coni_codigo)
                       ->where("grah_dia=?",$data)
                       ->order("grah_hora");
           if($grah_codigos)
               $sql->where ("grah_codigo not in ($grah_codigos)");
           
           //die($sql);
           return $this->fetchAll($sql);
    }
    
    public function deleteHorarios($grah_codigo=FALSE){
        $item = $this->fetchRow("grah_codigo=$grah_codigo");
        if ($item) {
            $item->delete();
        }
    }

  
}
