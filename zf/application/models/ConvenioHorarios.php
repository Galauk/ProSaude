<?php
 
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ConvenioHorarios extends Elotech_Db_Table_Abstract {

    protected $_name = 'convenio_horarios';
    protected $_primary = 'hora_codigo';
    
   	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
       
        $this->notEmpty(array("coni_codigo","hora_inicial","hora_final"), $data);
        $this->emptyToUnset($data);
      // echo "<pre>".  print_r($data,1);die();
	//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function salvarConvHorAgendamentoEstDeSaude($data){
        return parent::salvar($data);
    }


    /**
	 * Exclui um item do convênio
	 * @param int $coni_codigo
	 */
	public function excluir($coni_codigo) {
         
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
        
        public function getHorarios($coni_codigo,$condiAgeCod){
            $where = $this->select()
                          ->setIntegrityCheck(FALSE)
                          ->from(array("conh"=>"convenio_horarios"),array("hora_inicial","hora_final"))
                          ->where("coni_codigo=?",$coni_codigo)
                          ->where("condi_age_codigo =?",$condiAgeCod)
                          ->order("hora_inicial");
            return $this->fetchAll($where);
        }
        
        public function getHorariosArray($coni_codigo){
            $where = $this->select()
                          ->setIntegrityCheck(FALSE)
                          ->from(array("conh"=>"convenio_horarios"),array("to_char(hora_inicial,'hh:mm') as hora_inicial","hora_final"))
                          ->where("coni_codigo=?",$coni_codigo);
            return $this->fetchAll($where)->toarray();;
            $ret = $this->fetchAll($where)->toarray();
            $i = 1;
            foreach ($ret as  $ind =>$value) {
                $array['hora_inicial'.$i] = $value['hora_inicial'];
                $array['hora_final'.$i] = $value['hora_final'];
                $i++;
            }
        }
        
        public function getHorariosEstabelecimentoDeSaudeArray($coni_codigo){
            // Primeiro pega os dias e o código 
            $sqlDias = $this->select(FALSE)
                            ->distinct()
                            ->setIntegrityCheck(FALSE)
                            ->from(array("condi"=>"convenio_dias_semana_agendamento"),array("condi_age_dia AS dia"))
                            ->join(array("hora"=>"convenio_horarios"),"condi.condi_age_codigo=hora.condi_age_codigo",array())
                            ->where("condi.coni_codigo =?",$coni_codigo);
            $array = $this->fetchAll($sqlDias)->toArray();
            foreach ($array as $item=>$value) {
                $dadosHorario[$value["dia"]] = $value["dia"]; 
            }
            foreach ($dadosHorario as $item){
                $sqlHorarios = $this->select(FALSE)
                                    ->setIntegrityCheck(FALSE)
                                    ->from(array("hora"=>"convenio_horarios"),array("hora_inicial","hora_final"))
                                    ->join(array("condi"=>"convenio_dias_semana_agendamento"),"hora.condi_age_codigo=condi.condi_age_codigo",array(""))
                                    ->where("hora.coni_codigo =?",$coni_codigo)
                                    ->where("condi.condi_age_dia = $item")
                                    ->order("hora.hora_inicial");
                $contHorarios = $this->fetchAll($sqlHorarios)->toArray();
                $dadosHorario[$item] = $contHorarios; 
            }
            //Ordena um array pelas chaves em ordem inversa
            return $dadosHorario;
        }
}