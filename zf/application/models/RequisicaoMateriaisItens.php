<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RequisicaoMateriaisItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'requisicao_materiais_itens';
    protected $_primary = 'remi_codigo';
    protected $_sequence = 'requisicao_materiais_itens_remi_codigo_seq';

    const SOLICITADO = "S";

    public function salvar(array $data) {

        // validação:
        //echo "<pre>".print_r($data,1);die();
        $this->emptyToUnset($data);
        if(!$data[remi_codigo])
            $this->notEmpty(array("pro_codigo", "remi_quantidade","rem_codigo"), $data);

        return parent::salvar($data);
    }
    
    public function atualizaStatusItemRequisicao($data){
        return parent::salvar($data);
    }
    
    public function getStatusItemRequisicao($codRequisicaoItens){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("remi"=>"requisicao_materiais_itens"),array("remi_status"))
                    ->where("remi_codigo =?",$codRequisicaoItens);
        return $this->fetchRow($sql);
    }

    public function ValidaData($dat) {
        $data = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
        $d = $data[0];
        $m = $data[1];
        $y = $data[2];

        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        if ($y == "") {
            return false;
        } else {
            $res = checkdate($m, $d, $y);
            if ($res == 1) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getProdutosRequisicao($rem_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("rem"=>"requisicao_materiais"),array("usr_codigo","set_codigo_sol"))
                      ->join(array("remi"=>"requisicao_materiais_itens"),"remi.rem_codigo=rem.rem_codigo",array("remi_codigo","pro_codigo","remi_quantidade","remi_situacao"=>"(CASE WHEN remi_status = 'E' THEN 'Aguardando Confirmação' WHEN remi_status = 'C' THEN 'Confirmado' WHEN remi_status = 'S' THEN 'Solicitado' WHEN remi_status = 'F' THEN 'Finalizado' WHEN remi_status = 'N' THEN 'Não enviado' END)","remi_codigo","remi_status","saldo"=>"(SELECT sum(sal_qtde) from saldo where set_codigo = rem.set_codigo_sol and pro_codigo = remi.pro_codigo AND sal_validade >= CURRENT_DATE)","saldo_retorno"=>"(SELECT SUM(remil_quantidade) FROM requisicao_materiais_itens_lote WHERE remi_codigo = remi.remi_codigo)"))
                      ->join(array("pro"=>"produto"),"pro.pro_codigo=remi.pro_codigo","pro_nome")
                      ->join(array("usr"=>"usuarios"),"usr.usr_codigo=rem.usr_codigo","usr_nome")
                      ->where("remi.rem_codigo=?",$rem_codigo);
        return $this->fetchAll($where);
    }
    
    public function deletar($remi_codigo=FALSE){
       $item = $this->fetchRow("remi_codigo=$remi_codigo");
       if($item)
           $item->delete();

       return true;

    }
    
    public function getItensPorRequisicao($rem_codigo=FALSE) {
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("remi"=>"requisicao_materiais_itens"))
                      ->join(array("pro"=>"produto"),"pro.pro_codigo=remi.pro_codigo","pro_nome")
                      ->joinLeft(array("remil"=>"requisicao_materiais_itens_lote"),"remil.remi_codigo=remi.remi_codigo",array("remil_codigo","remil_lote","remil_quantidade"))
                      ->where("rem_codigo=$rem_codigo");
        return $this->fetchAll($where);
    }
   
    public function cancelarItem(array $data){
       return parent::salvar($data);
    }
    
    public function getItem($remi_codigo=FALSE){
        return $this->fetchRow("remi_codigo=$remi_codigo");
    }
    

}




