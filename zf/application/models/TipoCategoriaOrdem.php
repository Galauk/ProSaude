<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TipoCategoriaOrdem extends Elotech_Db_Table_Abstract {

    protected $_name = 'tipo_categoria_ordem';// nome da tabela do banco
    protected $_primary = 'tco_codigo'; // pk da tabela

    public function atualizaOrdemConfiguracoesExames($ordemCont,$item){
        $where['txa_codigo = ?'] = $item;
        $dados = array("tco_ordem"=>$ordemCont);
        return $this->update($dados, $where);
    }
    
    public function getOrdemConfiguracaoExames($cteCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tco"=>"tipo_categoria_ordem"),array("tco_ordem"))
                    ->where("tco.cte_codigo =?",$cteCodigo)
                    ->order(array("tco_ordem DESC"))
                    ->limit(1);
        return $this->fetchRow($sql);
    }
    
    public function salvarOrdemConfiguracaoExames($data){
        return parent::salvar($data);
    }
    
    public function excluirOrdemPorCategoria($txa_codigo=FALSE){
        $item = $this->fetchRow("txa_codigo = $txa_codigo"); 
        if ($item)
            $item->delete();
        return true;
    }
    
}
