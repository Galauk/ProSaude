<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ConvenioDiasSemanaAgendamento extends Elotech_Db_Table_Abstract {

    protected $_name = 'convenio_dias_semana_agendamento';
    protected $_primary = 'condi_age_codigo';

    public function salvar(array $data) {
		//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function excluir($coni_codigo) {
           //die($coni_codigo);
           $item = $this->fetchAll("coni_codigo=$coni_codigo");
           if ($item) {
                   foreach ($item as $i)
                    try{
                       $i->delete();
                    } catch (Zend_Validate_Exception $exc) {
                        die($exc->getMessage());
                    }
           }
           return true;
   }
   
   public function listaDadosPordia($coni_codigo){
       $sql = $this->select(FALSE)
                   ->setIntegrityCheck(FALSE)
                   ->from(array("condi"=>"convenio_dias_semana_agendamento"))
                   ->join(array("coni"=>"convenio_itens"),"condi.coni_codigo=coni.coni_codigo",array("coni_cota_mes"))
                   ->where("condi.coni_codigo =?",$coni_codigo);
       $res = $this->fetchAll($sql)->toArray();
       foreach ($res as $ind => $value) {
           $dadosDia[$value["condi_age_dia"]] = array(
                "condi_age_codigo" => $value["condi_age_codigo"],
                "coni_codigo" => $value["coni_codigo"],
                "condi_age_cota_dia" => $value["condi_age_cota_dia"],
                "condi_age_intervalo" => $value["condi_age_intervalo"],
                "condi_age_encaixe" => $value["condi_age_encaixe"],
                "coni_cota_mes" => $value["coni_cota_mes"]
           );
       }
       return $dadosDia;
   }
   
   public function getDadosDia($coniCodigo,$atendeQueDia){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("condi"=>"convenio_dias_semana_agendamento"),array("condi_age_codigo","condi_age_encaixe"))
                    ->where("condi.coni_codigo =?",$coniCodigo)
                    ->where("condi.condi_age_dia =?",$atendeQueDia);
        return $this->fetchRow($sql);
   }
   
    public function getIntervalos($coni_codigo=FALSE,$condiAgeCod=FALSE){
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("condi"=>"convenio_dias_semana_agendamento"),"condi_age_intervalo")
                    ->where("condi_age_codigo=?",$condiAgeCod);
        return $this->fetchRow($where);
    }
    
    public function getDadosVagaDia($coni_codigo,$data,$atendeQueDia){
        // Pega a quantidade de agendamento pelo Coni Codigo e o dia
        $sqlQtdDeAge = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("age"=>"agendamento"),"count(age_codigo)")
                            ->where("coni_codigo =?",$coni_codigo)
                            ->where("age_data =?",$data);
        // Pega a cota do dia e une com a consulta de cima retornando tudo em uma só
        $sql = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->from(array("conv"=>"convenio_dias_semana_agendamento"),"condi_age_cota_dia AS coni_cota_dia")
                              ->columns(array(
                                  "agendado_dia" => new Zend_Db_Expr('('.$sqlQtdDeAge->assemble().')')
                              ))
                             ->where("coni_codigo =?",$coni_codigo)
                             ->where("condi_age_dia =?",$atendeQueDia);
        return $this->fetchRow($sql);
    }

}
