<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Cid extends Elotech_Db_Table_Abstract {

	protected $_name = 'cid10';
	protected $_primary = 'cd10_codigo';

	public function selectTag($procedimento) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("c" => "cid10"), array("cd10_codigo", "cd10_descricao"))
				->join(array("rl" => "rl_procedimento_cid"), "rl.co_cid=cd10_codigo_cid", "")
				->join(array("p" => "procedimento"), "p.proc_codigo_sus=rl.co_procedimento")
				->where("p.proc_codigo=?", $procedimento)
                                ->order("c.cd10_descricao ASC");
		// die($where);                                
		return parent::selectTag($where, "cd10_descricao", NULL, FALSE, FALSE);
	}

	/**
	 * Buscar os CID's
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE) {
		if ($term)
			$busca = "retira_acentos(cd10_descricao) ilike retira_acentos('%$term%') OR cd10_codigo_cid ilike '%$term%'";

		$all = $this->fetchAll($busca, "cd10_descricao");

		$out = array();
		foreach ($all as $cid) {
			$out [] = array(
				"id" => $cid->cd10_codigo,
				"label" => trim($cid->cd10_descricao),
                                "cd10_codigo_cid"=>$cid->cd10_codigo_cid,
				"data" => array("cd10_codigo" => $cid->cd10_codigo,"cd10_codigo_cid" => $cid->cd10_codigo_cid)
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("cd10_codigo" => "")
			);
		}

		return $out;
	}


    public function getDadosPorAtendimento($ate_codigo=FALSE) {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("c" => "cid10"), array("cd10_codigo", "cd10_descricao", "cd10_codigo_cid"))
            ->join(array("pa" => "procedimento_atendimento"), "pa.cd10_codigo = c.cd10_codigo")
            ->where("pa.ate_codigo =?",$ate_codigo);
        return $this->fetchAll($sql);
    }


}
