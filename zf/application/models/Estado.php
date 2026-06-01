<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Estado extends Elotech_Db_Table_Abstract {

    protected $_name = 'estado';
    protected $_primary = 'uf_codigo';
    
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
    
    public function buscar($estado){
		if(empty($estado)) { $estado = 0; }
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("uf"=>"estado"),array("uf_codigo","uf_nome"))
                    ->where("uf_nome ilike '%".$estado."%'");
        $all = $this->fetchAll($sql);
        
        $out = array();
        foreach ($all as $dados) {
            $out [] = array(
                "id" => $dados->uf_codigo,
                "label" => $dados->uf_nome,
                "data" => array(
                    "uf" => $dados->uf_nome,
                    "uf_codigo" => $dados->uf_codigo
                )
            );
        }

        if (!count($out)) {
            $out [] = array(
                    "id" => 0,
                    "label" => "Nenhum item encontrado",
                    "data" => array("categoria" => "Nenhum item encontrado")
            );
        }
        return $out;
    }
    
    public function getEstados(){
        $where = $this->select(FALSE)
                     ->setIntegrityCheck(FALSE)
                     ->from(array("est"=>"estado"),array("uf_sigla","uf_codigo"))
                     ->order("uf_sigla");
       
       return $this->fetchAll($where);
    }
    
    public function getSiglaPorCodigo($uf_codigo=FALSE){
				if(empty($uf_codigo)) { $uf_codigo = 0; }
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("est"=>"estado"),"uf_sigla")
                      ->where("uf_codigo=$uf_codigo");
        return $this->fetchRow($where);
    }
 

}
