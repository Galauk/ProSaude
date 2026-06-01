<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ControleFracionado extends Elotech_Db_Table_Abstract {

	protected $_name = 'controlefracionado';
	protected $_primary = 'cont_codigo';
	protected $_sequence = 'controlevacina_cont_codigo_seq';

	public function salvar(array $data) {

		$this->notEmpty(array("cont_dose", "set_codigo", "ite_codigo"), $data);
		$data['cont_dose'] = (int) $data['cont_dose'];
		return parent::salvar($data);
	}

	public function dispensar($cont_codigo,$quantidade=1) {
		$cont = $this->fetchRow("cont_codigo=$cont_codigo");

		if ($cont->cont_dose === 0) {
			throw new Zend_Exception("O produto não possui mais frações disponíveis.");
		}

		$cont->cont_dose -= $quantidade;
		$cont->save();
		return $cont;
	}

	public function devolverFracao($cont_codigo, $quantidade=1) {
		$fracao = $this->fetchRow("cont_codigo=$cont_codigo");
		if (!$fracao)
			return FALSE;

		$fracao->cont_dose += $quantidade;
		$fracao->save();
		return TRUE;
	}

	public function getLotesFracionados($pro_codigo, $set_codigo, $somenteNaoVencidos=TRUE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cont" => "controlefracionado"), array("cont_codigo", "cont_dose"))
				->join(array("ite" => "itens_movimento"), "ite.ite_codigo=cont.ite_codigo", array("ite_lote","ite_validade"))
				->join(array("pro"=>"produto"),"pro.pro_codigo=ite.pro_codigo","pro_nome")
				->where("cont.set_codigo=?", $set_codigo)
				->where("cont.cont_dose > 0")
				->where("ite.pro_codigo=?", $pro_codigo)
				->order(array("ite_validade", "cont_dose"));

		if ($somenteNaoVencidos)
			$where->where("ite.ite_validade >= CURRENT_DATE");

		return $this->fetchAll($where);
	}
        
        public function removeConstraint(){
            try{
                $sql = $this
                    ->getDefaultAdapter()
                    ->query("ALTER TABLE controlefracionado DROP CONSTRAINT fk_ite_codigo")
                    ->fetchAll();
                return $sql;
            } catch (Exception $ex) {
                throw new Zend_Validate_Exception("Falha ao remover constraint: ".$ex->getMessage());
            }
        }
        
        public function adicionaConstraint(){
            try{
                $sql = $this
                    ->getDefaultAdapter()
                    ->query("ALTER TABLE controlefracionado ADD CONSTRAINT 
                                fk_ite_codigo FOREIGN KEY (ite_codigo)
                            REFERENCES 
                                itens_movimento (ite_codigo) 
                            MATCH SIMPLE
                                ON UPDATE NO ACTION ON DELETE NO ACTION")
                    ->fetchAll();
                return $sql;
            } catch (Exception $ex) {
                throw new Zend_Validate_Exception("Falha ao adicionar constraint: ".$ex->getMessage());
            }
        }

}
