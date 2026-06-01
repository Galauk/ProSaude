<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ItensMovimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'itens_movimento';
    protected $_primary = 'ite_codigo';
    protected $_sequence = 'seq_ite_codigo';

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
        // echo "<pre>";print_r($data);die();
        $this->peloMenosUm(array("ite_quantidade"), $data);
        $this->emptyToUnset($data);

        try{
            $tbMov = new Application_Model_Movimento();
            $mov = $tbMov->getMovimento($data[mov_codigo]);
            
            if($mov->mov_tipo != "E"){
                $data[set_codigo] = $mov->set_saida;
                //$this->verificaSeAindaTemEstoque($data);
                unset($data[set_codigo]);
            }
            return parent::salvar($data);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar os itens da movimentação!".$ex->getMessage());
        }
        
    }
    
    private function verificaSeAindaTemEstoque($data){
        $tbSal = new Application_Model_Saldo();
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $val = $tbSal->getSaldoAtual($data[pro_codigo], $data[set_codigo], $data[ite_lote], $data[ite_validade]);
        //die($val ."<". $data[ite_quantidade]);
        if($val < $data[ite_quantidade]){
            throw new Zend_Validate_Exception("Este produto já não possui saldo suficiente. Atualize o mesmo para obter a quantidade correta.");
        }else{
            return true;
        }
    }
    
    public function salvarItensMovimentacaoRequisicao($dados){
        try {
            return parent::salvar($dados);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar os itens da movimentação!".$exc->getMessage());
        }
    }
    
    public function getProdutosPorMovimento($mov_codigo=FALSE,$pro_codigo=FALSE){
        
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("mov"=>"movimento"),array("mov_tipo","usu_codigo"))
                      ->join(array("ite"=>"itens_movimento"),"mov.mov_codigo=ite.mov_codigo",array("pro_frmmin","ite_codigo","pro_codigo","ite_quantidade","ite_validade","ite_lote","ite_vlrunit","ite_dose","ite_vlrtotal","fab_descricao"=>"(select fab_descricao from itens_movimento im join fabricante f on f.fab_codigo=im.fab_codigo where im.pro_codigo = ite.pro_codigo  and im.ite_lote = ite.ite_lote limit 1)"))
                      ->join(array("pro"=>"produto"),"pro.pro_codigo=ite.pro_codigo","pro_nome")
                      ->joinLeft(array("usu"=>"usuario"),"usu.usu_codigo=mov.usu_codigo",array("usu.usu_nome"))
                      ->where("ite.mov_codigo=?",$mov_codigo);
        
        if($pro_codigo)
            $where->where("ite.pro_codigo=$pro_codigo");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function deletar($ite_codigo=FALSE){
        $item = $this->fetchRow("ite_codigo=$ite_codigo");
        if($item){
            $item->delete();
        }
        
        return true;
        
    }
    
    public function verificaSeMovimentou($ite_lote=FALSE,$ite_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("im"=>"itens_movimento"),array("(select count(*) 
                                                                        from itens_movimento im2
                                                                        join movimento mov2
                                                                          on mov2.mov_codigo=im2.mov_codigo
                                                                        where im2.pro_codigo = im.pro_codigo
                                                                          and mov2.mov_data >= mov.mov_data
                                                                          and im2.ite_codigo <> $ite_codigo
                                                                          and im2.ite_codigo > $ite_codigo
                                                                          ".($ite_lote != "" || $ite_lote != "SEM_LOTE" ? "and im2.ite_lote ilike '%$ite_lote%'" : "")."
                                                                        ) as movs"))
                      ->join(array("mov"=>"movimento"),"mov.mov_codigo=im.mov_codigo","")
                      ->where("ite_codigo=$ite_codigo");
        
        
        
        return $this->fetchRow($where);
    }
    
    public function getValorPorProdutoLote($pro_codigo=FALSE,$ite_lote=FALSE){
        $where_entrada = $this->select(FALSE)
                                ->setIntegrityCheck(FALSE)
                                ->from(array("ite"=>"itens_movimento"),array("vlr_unitario","(coalesce(ite_quantidade,null,0) * coalesce(ite_vlrunit,null,0)) as t_entrada", "sum(ite_quantidade) as qtde_entrada"))
                                ->join(array("mov"=>"movimento"),"mov.mov_codigo=ite.mov_codigo","")
                                ->where("pro_codigo=$pro_codigo")
                                ->where("ite_lote='$ite_lote'")
                                ->where("ite_vlrunit is not null")
                                ->where("mov_tipo='E'")
                                ->group(array("t_entrada","vlr_unitario"));
        
        $where_saida = $this->select(FALSE)
                                ->setIntegrityCheck(FALSE)
                                ->from(array("ite"=>"itens_movimento"),array("vlr_unitario","(coalesce(ite_quantidade,null,0) * coalesce(ite_custo_medio,null,0)) as t_saida", "sum(ite_quantidade) as qtde_saida"))
                                ->join(array("mov"=>"movimento"),"mov.mov_codigo=ite.mov_codigo","")
                                ->where("pro_codigo=$pro_codigo")
                                ->where("ite_lote='$ite_lote'")
                                ->where("mov_tipo='S'")
                                ->group(array("t_saida", "vlr_unitario"));
        $array_valores = array("entradas" => $this->fetchAll($where_entrada)->toArray(),
                               "saidas" => $this->fetchAll($where_saida)->toArray());
        
        return $array_valores;
    }
    
    public function getItensCurvaAbc($data_inicial=FALSE,$data_final=FALSE,$set_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("ite"=>"itens_movimento"),array("sum(ite_quantidade) as total_qtde",
                                                                   " (select sum(ite_custo_medio) / count(ite_custo_medio)
                                                                        from itens_movimento im2 
                                                                        join movimento m2
                                                                          on m2.mov_codigo=im2.mov_codigo
                                                                       where im2.pro_codigo = pro.pro_codigo 
                                                                         and ite_custo_medio is not null
                                                                         AND (mov_data >= '$data_inicial') 
                                                                         AND (mov_data <= '$data_final')
                                                                         AND set_saida = $set_codigo) as vlr_unitario",
                                                                    "COALESCE((select sum(COALESCE(ite_custo_medio,NULL,0)) / count(COALESCE(ite_custo_medio,NULL,0))
                                                                        from itens_movimento im2 
                                                                        join movimento m2
                                                                          on m2.mov_codigo=im2.mov_codigo
                                                                       where im2.pro_codigo = pro.pro_codigo 
                                                                         and ite_custo_medio is not null
                                                                         AND (mov_data >= '$data_inicial') 
                                                                         AND (mov_data <= '$data_final')
                                                                         AND set_saida = $set_codigo),NULL,0) * sum(ite_quantidade) as vlr_total_item"))
                      ->join(array("mov"=>"movimento"),"mov.mov_codigo=ite.mov_codigo","")
                      ->join(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo",array("pro_nome","pro_codigo"))
                      ->group(array("pro.pro_codigo","pro_nome","vlr_unitario"))
                      ->order("total_qtde DESC")
                      ->order("vlr_unitario DESC")
                      ->where("mov_tipo in ('S','T')")
                      ->where("(select sum(ite_custo_medio) / count(ite_custo_medio)
                                                                        from itens_movimento im2 
                                                                        join movimento m2
                                                                          on m2.mov_codigo=im2.mov_codigo
                                                                       where im2.pro_codigo = pro.pro_codigo 
                                                                         and ite_custo_medio is not null
                                                                         AND (mov_data >= '$data_inicial') 
                                                                         AND (mov_data <= '$data_final')
                                                                         AND set_saida = $set_codigo)!='0.00000000000000000000'");
        
        if($data_inicial)
            $where->where("mov_data >= '$data_inicial'");
        
        if($data_final)
            $where->where("mov_data <= '$data_final'");
        
        if($set_codigo)
            $where->where("set_saida = $set_codigo");
      // die($where);
       // die($where);
	return $this->fetchAll($where);
    }
    
    public function getItensMovimentacoesPorSetor($set_codigo = FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("ite"=>"itens_movimento"))
                      ->join(array("mov"=>"movimento"),"ite.mov_codigo=mov.mov_codigo",array(""));
        
        if ($set_codigo)
                $where->where("mov.set_entrada = '$set_codigo' OR mov.set_saida = '$set_codigo'");
        
        return $this->fetchAll($where);
    }
    
    public function desabilitaTrigger01(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento DISABLE TRIGGER atualizaestoque;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao desabilitar trigger: ".$ex->getMessage());
        }
    }
    
    public function desabilitaTrigger02(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento DISABLE TRIGGER atualizaestoquedel;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao desabilitar trigger: ".$ex->getMessage());
        }
    }
    
    public function desabilitaTrigger03(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento DISABLE TRIGGER atualizaestoqueupdate;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao desabilitar trigger: ".$ex->getMessage());
        }
    }
    
    public function desabilitaTrigger04(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento DISABLE TRIGGER atualizahorus;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao desabilitar trigger: ".$ex->getMessage());
        }
    }
    
    public function habilitaTrigger01(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento ENABLE TRIGGER atualizaestoque;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao habilitar triggers: ".$ex->getMessage());
        }
    }
    
    public function habilitaTrigger02(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento ENABLE TRIGGER atualizaestoquedel;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao habilitar triggers: ".$ex->getMessage());
        }
    }
    
    public function habilitaTrigger03(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento ENABLE TRIGGER atualizaestoqueupdate;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao habilitar triggers: ".$ex->getMessage());
        }
    }
    
    public function habilitaTrigger04(){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("ALTER TABLE social.itens_movimento ENABLE TRIGGER atualizahorus;")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao habilitar triggers: ".$ex->getMessage());
        }
    }
    
    
    public function excluiItensMovimentacoesPorSetor($setores=FALSE,$data=FALSE){
        // Se data vier preenchida faz consula por data
        if($data)
            $sqlData = "AND mov_data <= '$data'";
        
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("DELETE FROM social.itens_movimento WHERE mov_codigo IN (
                                            SELECT DISTINCT
                                                mov_codigo
                                            FROM
                                                social.movimento
                                            WHERE
                                                (set_entrada IN ($setores) OR
                                                set_saida IN ($setores))
                                                $sqlData)")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir item: ".$ex->getMessage());
        }
        /*$item = $this->getItensMovimentacoesPorSetor($set_codigo);
        if ($item) {
            foreach ($item as $value){
                try{
                    $value->delete();
                } catch (Exception $exc) {
                    throw new Zend_Validate_Exception($exc->getMessage());
                }
            }
        }
        return true;*/ 
    }
	
	
    public function getCustoMedio($pro_codigo,$ite_lote){
        $this->getValorPorProdutoLote($pro_codigo, $ite_lote);
    }
    
    public function verificaSeJaDispensou($params=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("im"=>"itens_movimento"),"count(*) as qtde")
                      ->join(array("mov"=>"movimento"),"mov.mov_codigo=im.mov_codigo","")
                      ->where("mov_data='$params[mov_data]'")
                      ->where("usu_codigo='$params[usu_codigo]'")
                      ->where("pro_codigo='$params[pro_codigo]'")
                      ->where("ite_lote='$params[ite_lote]'");
        
        $qtde = $this->fetchRow($where);

        $semValidacaoHospital = $_SESSION['logon']['usr']->cnes_tp_unid_id;
        // var_dump($semValidacaoHospital);die();

        if ($semValidacaoHospital == '05') {
            return true;

        } else{

            if($qtde->qtde > 0){
                 throw new Zend_Validate_Exception("Esse medicamento neste lote nessa validade, nesta data, para esse paciente já foi pego");
                return false;
                
             }else{
                
                 return true;
             }
        }
       // return true;
    }

    public function getFracionamentoMinimo($pro_codigo) {
        $where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("ite_mov"=>"itens_movimento"), "pro_frmmin")
			->where("pro_codigo = $pro_codigo");
		
		return $this->fetchRow($where);
    }

    public function retornaEstoqueCentroDestino($recebeCodigoItensMovimento, $setCodigoDestino, $pro_codigo){
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT SUM(SAL_QTDE) FROM produto AS pro
                    INNER JOIN saldo AS sal
                        ON sal.pro_codigo = pro.pro_codigo
                    INNER JOIN setor AS setor
                        ON sal.set_codigo = setor.set_codigo
                    WHERE pro.pro_codigo = $pro_codigo AND setor.set_codigo = $setCodigoDestino
            "
        )->fetchAll();

        return $sql;

    }

    public function recuperaVlr_unitario($produtoId){
        $sql = $this->getDefaultAdapter()->query(
            "SELECT vlr_unitario from itens_movimento where pro_codigo = $produtoId order by ite_codigo desc limit 1"
        )->fetchAll();

        return $sql;

    }
}