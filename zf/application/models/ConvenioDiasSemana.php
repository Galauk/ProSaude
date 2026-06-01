<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ConvenioDiasSemana extends Elotech_Db_Table_Abstract {
 
    protected $_name = 'convenio_dias_semana';
    protected $_primary = 'condi_codigo';
    
   	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
       
        $this->notEmpty(array("coni_codigo","condi_dia"), $data);
        $this->emptyToUnset($data);
      // echo "<pre>".  print_r($data,1);die();
	//throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function getDiasDeAtendimento($coni_codigo=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("condi"=>"convenio_dias_semana_agendamento"),"condi_age_dia AS condi_dia")
                      ->where("coni_codigo=?",$coni_codigo);
		      //->limit(1);
        $dados = $this->fetchAll($where)->toArray();
        $dias = array();
        foreach($dados as $itemDia){
            array_push($dias,$itemDia["condi_dia"]);
        }
        // Método de Funções para transformar a data em dia
        $tbFun = new Application_Model_Funcoes();
        // Listando os dia em que 
        $where2 = $this->select()
                       ->distinct() 
                       ->setIntegrityCheck(FALSE)
                       ->from(array("gra_d"=>"grade_dia"),"grad_dia")
                       ->where("coni_codigo=?",$coni_codigo);
        $dadosGradeDia = $this->fetchAll($where2);
        foreach ($dadosGradeDia as $item) {
            $dia = $tbFun->diaSemana($item->grad_dia);
            if (!in_array($dia,$dias)) {
                //Alimentando dados dia porque se tiver mais de uma vez o dia ele não deixa inserir
                array_push($dias,$dia);
            }
        }
        return $dias;
    }
    public function getDiasDeAtendimentoArray($coni_codigo=FALSE){
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("condi"=>"convenio_dias_semana"),array("condi_dia","condi_codigo"))
                      ->where("coni_codigo=?",$coni_codigo);
        $ret = $this->fetchAll($where)->toarray();
        foreach ($ret as  $ind =>$value) {
            $array[$value['condi_codigo']] = $value['condi_dia'];
        }
        return $array;
    }
    /**
    * Exclui um item do convênio
    * @param int $coni_codigo
    */
   public function excluir($coni_codigo) {
           $item = $this->fetchAll("coni_codigo=$coni_codigo");
           if ($item) {
                   foreach ($item as $i)
                    $i->delete();
           }
           return true;
   }

}
