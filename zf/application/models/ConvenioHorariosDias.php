<?php
 
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ConvenioHorariosDias extends Elotech_Db_Table_Abstract {

    protected $_name = 'convenio_horarios_dias';
    protected $_primary = 'hor_dia_codigo';
    
    public function salvar(array $data) {
        return parent::salvar($data);
    }
    
    public function excluir($condi_age_codigo=FALSE,$coni_codigo=FALSE){
        $item = $this->fetchAll("condi_age_codigo=$condi_age_codigo AND coni_codigo=$coni_codigo");
        if($item){
            foreach($item as $value) {
                $value->delete();
            }
        }
    }
    
    public function confereSeHorarioExiste($horario){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dia"=>"convenio_horarios_dias"),array("COUNT(hor_dia_codigo) AS qtdHorario"))
                    ->where("hora =?",$horario);
        return $this->fetchRow($sql);
    }
    
}