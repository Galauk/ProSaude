<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_GradePeriodo extends Elotech_Db_Table_Abstract {

    protected $_name = 'grade_periodo';
	protected $_primary = 'grap_codigo';

    public function salvar(array $data) {
	$this->notEmpty(array("coni_codigo","grap_dia","grap_hora_inicial","grap_hora_final"), $data);
        return parent::salvar($data);
    }
	
	
    public function getHorariosDia($coni_codigo=FALSE,$data=FALSE,$condiAgeCod=FALSE){
            //die("aabbba");
            $where = $this->select()
                             ->setIntegrityCheck(FALSE)
                             ->from(array("grap"=>"grade_periodo"),array("grap_hora_inicial as hora_inicial","grap_hora_final as hora_final"))
                             ->where("coni_codigo=?",$coni_codigo)
                             ->where("grap_dia=?",$data);
            $horarios = $this->fetchAll($where);
            if(count($horarios) < 1){
                if ($condiAgeCod) {
                    $tbConH = new Application_Model_ConvenioHorarios();
                    $horarios = $tbConH->getHorarios($coni_codigo,$condiAgeCod);
                } else {
                    $horarios = NULL;
                }
            }
            return $horarios;
    }
    
    public function getPeriodosGrade($coni_codigo = FALSE,$data = FALSE){
        $grap = $this->fetchAll("coni_codigo=$coni_codigo AND grap_dia='$data'");
        return $grap;
    }
    
    public function excluir($coni_codigo=FALSE, $data=FALSE){
        /*$item = $this->fetchAll("coni_codigo=$coni_codigo AND grap_dia='$data'");
        if($item) {    
            foreach ($item as $value) {
                echo $value->grap_codigo."<br />";
                /*if(!(in_array($value, $dados_grap_codigo))){
                    $value->delete();
                }
            }
            return true;
        }*/
        $item = $this->fetchAll("coni_codigo=$coni_codigo AND grap_dia='$data'");
        if($item) { 
            foreach ($item as $value) {
                $value->delete();
            }
        }
    }

  
}
