<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Retorno extends Elotech_Db_Table_Abstract {

    protected $_name = 'retorno';
    protected $_primary = 'ret_codigo';

    public function salvar(array $data) {
        return parent::salvar($data);
    }
    
    public function getDadosPre($age_codigo=FALSE,$pc_codigo=FALSE,$gambi=FALSE,$noGambi=FALSE){
        $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("pc"=>"pre_consulta"))
                    ->join(array("ret"=>"retorno"),"ret.pc_codigo=pc.pc_codigo")
                    ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo")
                    ->order("pc.pc_codigo desc");
        if($pc_codigo){
            $where->where("pc.pc_codigo=?",$pc_codigo);
        }
        if ($gambi && !$noGambi){
            if($age_codigo){
                $where->where("age_atendido = 'I'");
                $where->where("pc.age_codigo=?",$age_codigo);
            }
          
        }else{
             if($age_codigo){
                $where->where("age_atendido = 'I' or age_atendido = 'E'");
                $where->where("pc.age_codigo=?",$age_codigo);
            }
        }      
//    die($where);
        return $this->fetchRow($where);
        
    }

}
