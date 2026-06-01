<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Cidade extends Elotech_Db_Table_Abstract {

    protected $_name = 'cidade';
    protected $_primary = 'cid_codigo';

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
	/**
	 * Busca o código IBGE da cidade onde o sistema está instalado
	 * @return int 
	 */
	private function getCodigoIbgeCidadeAtual(){
		$tbConfig = new Application_Model_Configuracao();
		return $tbConfig->getConfig('CID_CODIGO_IBGE');
	}
	
	/**
	 * Busca um registro na tabela cidade pelo código IBGE
	 * @param int $ibge
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getCidadePeloCodigoIbge($ibge){
		return $this->fetchRow("cid_codigo_ibge='$ibge'");
	}
	
	/**
	 * Busca o registro da cidade atual
	 * @return Zend_Db_Table_Row_Abstract 
	 */
	public function getCidadeAtual(){
		$ibge = $this->getCodigoIbgeCidadeAtual();
		return $this->getCidadePeloCodigoIbge($ibge);
	}
        /**
	 * Buscar as cidades
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("cid" => "cidade"), array("cid_codigo", "cid_nome","uf_sigla"))
                                ->joinLeft(array("uf"=>"estado"),"cid.uf_codigo=uf.uf_codigo",array("uf_codigo","uf_nome"))
				->where("retira_acentos(cid_nome) ilike retira_acentos('%$term%')")
                                ->where("cid.uf_sigla is not null")
				->order("cid_nome");

		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $cid) {                     
			$out [] = array(
				"id" => $cid->cid_codigo,
				"label" => trim($cid->cid_nome)." - ".$cid->uf_sigla,
				"data" => $cid->toArray()
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array()
			);
		}

		return $out;
	}
        
        public function listaCidadePorEstado($ufSigla){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cid"=>"cidade"),array("cid_codigo","cid_nome"))
                        ->where("uf_sigla ='$ufSigla'");
            return $this->fetchAll($sql);
        }

        public function listaCidadePorEstadoCodigo($ufCodigo){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cid"=>"cidade"),array("cid_codigo","cid_nome"))
                        ->where("uf_codigo =?",$ufCodigo);
            //die($sql);
            return $this->fetchAll($sql);
        }
        
        public function buscaCidadePeloNome($cidade=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cid"=>"cidade"),array("cid_codigo","cid_nome"))
                        ->where("retira_acentos(UPPER(cid_nome)) =?",$cidade);
            return $this->fetchRow($sql);
        }
        
        public function getDadosCidade($busca){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("cid"=>"cidade"),array("cid_codigo","cid_nome"))
                        ->where("cid_codigo = $busca");
            return $this->fetchRow($sql);
        }
        
        public function getCidadePorDistrito($dis_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("cid"=>"cidade"),array("cid_codigo","cid_nome"))
                          ->join(array("dis"=>"distrito"),"dis.cid_codigo=cid.cid_codigo","")
                          ->where("dis_codigo=$dis_codigo");
         
            return $this->fetchRow($where);
        }
}
