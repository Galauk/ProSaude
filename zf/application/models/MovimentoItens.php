<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_MovimentoItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'itens_movimento';
	protected $_primary = 'ite_codigo';
    protected $_sequence = 'seq_ite_codigo';

    public function salvar(array $data) {
		$this->valoresPadrao($data);
		
		// validação
		$this->notEmpty(array("mov_codigo","pro_codigo","ite_quantidade","ite_lote","ite_validade"), $data);
		
		// verificar se é movimento de saida e se há saldo
		$this->verificarMovimento($data);
		
        return parent::salvar($data);
    }
	
	private function verificarMovimento($data){
		$tbMov = new Application_Model_Movimento();
		$mov = $tbMov->fetchRow("mov_codigo=".$data['mov_codigo']);
		
		if($mov->mov_tipo == Application_Model_Movimento::SAIDA){
			
			// verifica se há saldo
			$tbSal = new Application_Model_Saldo();
			$saldo = $tbSal->getSaldoAtual($data['pro_codigo'], $data['set_codigo'], $data['ite_lote'], $data['ite_validade']);
			
			if($saldo < $data['ite_quantidade'] ){
				throw new Zend_Validate_Exception( "Não há estoque sufiente para fazer esta movimentação" );
			}
			
		}
	}
	
	private function valoresPadrao(&$data){
		if(empty($data['usr_codigo'])){
			$tbUsr = new Application_Model_Usuarios;
			$data['usr_codigo'] = $tbUsr->getUsrAtual()->usr_codigo;
		}		
	}
	
	public function movimentarItem($mov_codigo, $pro_codigo, $quantidade=1){
		
	}

}
