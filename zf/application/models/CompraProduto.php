<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

/**
 * Essa agenda é resposável pelo novo agendamento
 * @todo agendar exames e consultas, por quantidade (cota) e valor
 */
class Application_Model_CompraProduto extends Elotech_Db_Table_Abstract {

	protected $_name = 'compra_produto';
	protected $_primary = 'comp_codigo';
	/**
	 * Insert ou update na agenda, com seus filhos
	 * @param array $data dados do formulário
	 * @return int chave primária do registro inserido ou atualizado 
	 */
	public function getHistoricoPorUsuario($usu_codigo=FALSE,$data_inicial=FALSE,$data_final=FALSE){
            
            $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("comp"=>"compra_produto"),array("comp_data"))
				->join(array("compi"=>"compra_produto_itens"),"comp.comp_codigo=compi.comp_codigo",array("pro_nome","compi_quantidade","compi_valor"))
                                ->join(array("forn"=>"fornecedor"),"forn.for_codigo=comp.for_codigo","for_nome")
                                ->where("usu_codigo=$usu_codigo")
                                ->order(array("comp_data","pro_nome"));
            
            if($data_inicial){
                    $where->where("comp_data >='$data_inicial'");
                }
            if($data_final){
                $where->where("comp_data <=' $data_final'");
            }
            return $this->fetchAll($where);
            
        }
}
