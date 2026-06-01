<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Veiculo extends Elotech_Db_Table_Abstract {

    protected $_name = 'veiculo';
    protected $_primary = 'vei_codigo';   

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
        $this->valoresPadrao($data);
         
        if($data[vei_placa]){
            $data[vei_placa] = strtoupper($data[vei_placa]);
        }
        $this->notEmpty(array("vei_descricao","vei_capacidade"), $data);
        $this->emptyToUnset($data);
      // echo "<pre>".  print_r($data,1);die();
        return parent::salvar($data);
    }
    
    /**
    * Valores padrão do insert/update
    * @param array $data valores do insert
    */
   private function valoresPadrao(&$data) {
           if (empty($data['for_codigo'])) {
                   $data['for_codigo'] = NULL;
           }
           if (empty($data['veis_codigo'])) {
                   $data['veis_codigo'] = NULL;
           }
           if (empty($data['veit_codigo'])) {
                   $data['veit_codigo'] = NULL;
           }                
           if (empty($data['veit_codigo'])) {
                   $data['veit_codigo'] = NULL;
           }
           if (empty($data['veic_codigo'])) {
                   $data['veic_codigo'] = NULL;
           }
           if (empty($data['vei_data_aquisicao'])) {			
                   $data['vei_data_aquisicao'] = date("Y-m-d");
           }
   }
     /**
     * Retorna todos os veiculos cadastrados	
     
     * @return Zend_Db_Table_Row_Abstract 
     */
   public function getVeiculos() {
      $where = $this->select(FALSE)
              ->setIntegrityCheck(FALSE)
              ->from(array("vei"=>"veiculo"),array("vei.vei_codigo","vei.vei_descricao","vei.vei_placa","vei.vei_capacidade","(SELECT count(via.via_codigo) FROM viagem via WHERE via.vei_codigo=vei.vei_codigo) AS quantviagem"))
              ->joinLeft(array("veie"=>"veiculo_especie"),"vei.veie_codigo = veie.veie_codigo" ,"veie_descricao")
              ->joinLeft(array("veic"=>"veiculo_combustivel"), "vei.veic_codigo=veic.veic_codigo","veic_descricao")
              ->joinLeft(array("veis"=>"veiculo_situacao"), "vei.veis_codigo=veis.veis_codigo");
          // die($where);
      return $this->fetchAll($where);
                
               
   }
     /**
     * Retorna veiculo cadastrado	
     * @param int $vei_codigo Código da veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
   public function getVeiculo($vei_codigo = FALSE) {
      $where = $this->select(FALSE)
              ->setIntegrityCheck(FALSE)
              ->from(array("vei"=>"veiculo"))
              ->join(array("veie"=>"veiculo_especie"),"vei.veie_codigo = veie.veie_codigo" ,"veie_descricao")
              ->join(array("veic"=>"veiculo_combustivel"), "vei.veic_codigo=veic.veic_codigo","veic_descricao")
              ->join(array("veis"=>"veiculo_situacao"), "vei.veis_codigo=veis.veis_codigo")
              ->joinLeft(array("forn"=>"fornecedor"),"forn.for_codigo=vei.for_codigo","for_nome");
      if($vei_codigo){
          $where->where("vei_codigo=?",$vei_codigo);
      }
    // die($where);
      return $this->fetchRow($where);
   }   
    /**
     * Exclui um veiculo	
     * @param int $vei_codigo Código da veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function excluir($vei_codigo=FALSE) {
            $item = $this->fetchRow("vei_codigo=$vei_codigo");
            if ($item) {
                $item->delete();
            }
    }
    /**
     * Busca Veiculo	
     * @param int $dados Dados poder ser o nome do setor ou a descricai da Veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function pesquisar($dados=FALSE, $limit=FALSE) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("vei"=>"veiculo"),array("vei_codigo","vei_descricao","vei_placa","vei_capacidade"))
                            ->join(array("veie"=>"veiculo_especie"),"vei.veie_codigo = veie.veie_codigo" ,"veie_descricao")
                            ->join(array("veic"=>"veiculo_combustivel"), "vei.veic_codigo=veic.veic_codigo","veic_descricao")
                            ->join(array("veis"=>"veiculo_situacao"), "vei.veis_codigo=veis.veis_codigo");		
            if (is_string($dados))
                    $where->where("vei_descricao ilike '%$dados%' or vei_placa ilike '%$dados%'");
            if ($limit) {
                    $where->limit(15);
            }
            //die($where);
            return $this->fetchAll($where);
    }
    /**
     * Busca Cota do veículo	
     * @param int $via_codigo Código da viagem
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function verificaCota($via_codigo) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)                            
                            ->from(array("vei"=>"veiculo"),"vei_capacidade")
                            ->join(array("via"=>"viagem"),"vei.vei_codigo=via.vei_codigo","")
                            ->joinLeft(array("vu"=>"viagem_usuario"),"via.via_codigo=vu.via_codigo" ,array("total"=>'(count(vu.viausu_codigo) + count(ua.viausu_codigo))'))
                            ->joinLeft(array("ua"=>"usuario_acompanhante"),"vu.viausu_codigo=ua.viausu_codigo","")
                            ->where("via.via_codigo=?",$via_codigo)
                            ->group("vei_capacidade");
                            // die($where);
            return $this->fetchRow($where);
    }
  
}

