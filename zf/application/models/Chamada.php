<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Chamada extends Elotech_Db_Table_Abstract {

	protected $_name = 'chamada';
	protected $_primary = 'cha_codigo';

	public function salvar(array $data,$age_codigo) {
        $data['cha_data'] = 'now()';
        return parent::salvar($data);       
	}
     /*   public function update(array $data,$age_codigo) {
            return $this->update($data, "age_codigo = $age_codigo");
      }*/
    public function getChamadaPorAgendamento($age_codigo=FALSE){
        $where = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cha"=>"chamada"),array("cha_codigo"))                          
                        ->where("age_codigo =?",$age_codigo);                     
        //die($where);

        return $this->fetchRow($where);
    }

	public function encerrarChamada($age_codigo=FALSE,$chamar=FALSE){
            
        if($chamar){
            $status = "C";
        } else {
            $status = "F";
        }
        
        $data = array(
            'cha_status'=> $status,
            'cha_data' => 'now()'
        );
        
        // echo "<pre>"; print_r($data); exit;
        // $where = $this->select()->where("age_codigo = ?", $age_codigo);
        // echo "<pre>"; print_r($where); exit;
        // $where = $where[0];
        // echo "<pre>".print_r($data,1);
        //echo "<pre>".print_r($where,1);exit;
        // echo "<pre>"; print_r($this->fetchRow("age_codigo = $age_codigo")->toArray()); die;
        $this->update($data, "age_codigo = $age_codigo");

        return $this->fetchRow("age_codigo = ".$age_codigo);
    }
    
    public function buscarChamadas($uni_codigo=FALSE){
        $where = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cha"=>"chamada"),array("age_codigo","cha_status","cha_codigo"))
                        ->join(array("age"=>"agendamento"),"age.age_codigo=cha.age_codigo","age_paciente")
                        ->join(array("log"=>"logon"),"log.id_login=cha.usr_codigo","")
                        ->join(array("set"=>"setor"),"set.set_codigo=log.cod_setor","set_nome")
                        ->joinLeft(array("pre"=>"pre_consulta"),"pre.age_codigo=age.age_codigo",array("cor"=>"(CASE WHEN pc_clas_risco=1 THEN 'red' WHEN pc_clas_risco=2 THEN 'GoldenRod' WHEN pc_clas_risco=3 THEN 'yellow' WHEN pc_clas_risco=4 THEN 'green' WHEN pc_clas_risco=5 THEN 'blue' END)"))
                        ->where("age.uni_codigo =?",$uni_codigo)
                        ->order("cha_status")
                        ->order("cha.cha_codigo DESC")
                        ->limit(6);
        //    die($where);
        return $this->fetchAll($where);
    }

    public function buscarProximo(){
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("cha"=>"chamada"),array("age_codigo","cha_status","cha_codigo"))
                ->join(array("age"=>"agendamento"),"age.age_codigo=cha.age_codigo","age_paciente")
                ->join(array("log"=>"logon"),"log.id_login=cha.usr_codigo","")
                ->join(array("set"=>"setor"),"set.set_codigo=log.cod_setor","set_nome")
                ->joinLeft(array("pre"=>"pre_consulta"),"pre.age_codigo=age.age_codigo",array("cor"=>"(CASE WHEN pc_clas_risco=1 THEN 'red' WHEN pc_clas_risco=2 THEN 'GoldenRod' WHEN pc_clas_risco=3 THEN 'yellow' WHEN pc_clas_risco=4 THEN 'green' WHEN pc_clas_risco=5 THEN 'blue' END)"))
                ->where("age(now(), cha_data) < cast(('00:59' || ' minute') as interval)")
                ->order("cha_status")
                ->order("cha.cha_codigo DESC")
                ->limit(1);
    //die($where);
        return $this->fetchRow($where);
    }

    public function buscarChamados(){
        $where = $this->select()
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("cha"=>"chamada"),array("age_codigo","cha_status","cha_codigo"))
                ->join(array("age"=>"agendamento"),"age.age_codigo=cha.age_codigo","age_paciente")
                ->join(array("log"=>"logon"),"log.id_login=cha.usr_codigo","")
                ->join(array("set"=>"setor"),"set.set_codigo=log.cod_setor","set_nome")
                ->joinLeft(array("pre"=>"pre_consulta"),"pre.age_codigo=age.age_codigo",array("cor"=>"(CASE WHEN pc_clas_risco=1 THEN 'red' WHEN pc_clas_risco=2 THEN 'GoldenRod' WHEN pc_clas_risco=3 THEN 'yellow' WHEN pc_clas_risco=4 THEN 'green' WHEN pc_clas_risco=5 THEN 'blue'  END)"))
                ->where("age(now(), cha_data) < cast(('00:59' || ' minute') as interval)")
                ->order("cha_status")
                ->order("cha.cha_codigo DESC")                    
                ->limit(7);
        //die($where);
        return $this->fetchAll($where);
    }

    public function ler($usu_nome){           
        $voice = file_get_contents('http://translate.google.com/translate_tts?tl=pt-br&q=' . urlencode("teste") . '');         
        return $voice;
    }
}