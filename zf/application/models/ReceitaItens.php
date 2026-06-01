<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ReceitaItens extends Elotech_Db_Table_Abstract {

	protected $_name = 'itemreceita';
	protected $_primary = 'irec_codigo';
	protected $_sequence = 'seq_irec_codigo';
	protected $_dependentTables = array();

	public function salvar(array $data, $tipo='posto') {
		//echo "<pre>".print_r($data,1);exit;
		//die("entro");
		$this->addRealName(array(
			"irec_quantidade" => "quantidade",
			"irec_produto" => "produto",
			"desc_produto" => "descricao"
		));

		$this->filterDigits(array("irec_quantidade", "irec_qtde_pendente"), $data);
		$this->notEmpty(array("rec_codigo"), $data);

		// Se não for informado a quantidade pendente, usar a quantidade (total)
		if (empty($data['irec_qtde_pendente']))
			$data['irec_qtde_pendente'] = $data['irec_quantidade'];

		if ($tipo == "externo") {
			$this->notEmpty(array("irec_produto", "desc_produto"), $data);
		} else {
			$this->maiorQueZero(array("pro_codigo"), $data);
		}

		$this->maiorQueZero(array("irec_quantidade", "irec_qtde_pendente"), $data);

		$this->emptyToUnset($data);
                //echo"<pre>".print_r($data,1);die();

		return parent::salvar($data);
	}

	public function getItens($tipo = false, $recCodigo = false, $selecionados = false) {
            $tbRec = new Application_Model_Receita();
            $rec = $tbRec->temReceita($tipo);

            if (!$rec) {
                return false;
            }

            $codigoAtendimento = $rec[ate_codigo];
            //echo"<pre>".print_r($rec,1);
            $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("i" => "itemreceita"), array("distinct(irec_codigo)", "irec_recomendacao", "irec_quantidade", "irec_produto", "desc_produto"))
                ->joinLeft(array("p" => "produto"), "p.pro_codigo=i.pro_codigo", array("pro_nome"))
                ->join(array("rec" => "receita"),"rec.rec_codigo = i.rec_codigo",array("rec_validade","rec_data"))
                ->join(array("ate" => "atendimento"),"ate.ate_codigo = rec.ate_codigo", array())
                ->join(array("un" => "unidade"), "un.uni_codigo = ate.uni_codigo", array())
                ->where("rec_data = CURRENT_DATE")
                ->where("rec_tipo = '$tipo'")
                ->where("ate.ate_codigo = $codigoAtendimento")
                ->order("pro_nome");

            if ($selecionados && $selecionados!= 'null' && $selecionados != null) {
                $sql->where("irec_codigo in($selecionados)");
            }
            return $this->fetchAll($sql);
	}

	public function getItensInternacao($io_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ati" => "atendimento_internacao"),"ati.io_codigo")
				->join(array("ate"=>"atendimento"),"ate.ate_codigo=ati.ate_codigo","")
				->join(array("rec" => "receita"),"rec.ate_codigo = ate.ate_codigo",array("rec_data","rec_validade"))
				->join(array("ite" => "itemreceita"),"rec.rec_codigo = ite.rec_codigo",array("irec_codigo", "irec_recomendacao", "irec_quantidade", "irec_produto", "desc_produto"))
				->joinLeft(array("p" => "produto"), "p.pro_codigo=ite.pro_codigo", array("pro_nome"))
				->where("io_codigo=?", $io_codigo)
				->order("rec_data");
		//die($where);
		return $this->fetchAll($where);
	}
	public function getHistorico($ate_codigo, $tipo=FALSE) {
		// Aqui deve buscar pelo atendimento e não pela receita
		// mas filtrar pelo tipo

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("i" => "itemreceita"), array("irec_codigo", "irec_recomendacao", "irec_quantidade", "irec_produto", "desc_produto"))
				->joinLeft(array("p" => "produto"), "p.pro_codigo=i.pro_codigo", array("pro_nome"))
				->join(array("r" => "receita"), "r.rec_codigo=i.rec_codigo", "")
				->where("r.ate_codigo=?", $ate_codigo)
				->order("pro_nome");

		if($tipo)
			$where->where("r.rec_tipo=?",$tipo);


                //echo $where;
		return $this->fetchAll($where);
	}

        public function getItem($irec_codigo) {
		// Aqui deve buscar pelo atendimento e não pela receita
		// mas filtrar pelo tipo

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("i" => "itemreceita"), array("irec_codigo", "irec_recomendacao", "irec_quantidade", "irec_produto", "desc_produto"))
				->joinLeft(array("p" => "produto"), "p.pro_codigo=i.pro_codigo", array("pro_nome"))
				->join(array("r" => "receita"), "r.rec_codigo=i.rec_codigo", "rec_data")
				->where("i.irec_codigo=?", $irec_codigo);

                //echo $where;
		return $this->fetchRow($where);
	}

	/**
	 * Exclui um item da receita.
	 * O método verifica se faz parte do agendamento atual
	 * @param int $irec_codigo
	 */
	public function excluir($irec_codigo) {
		$age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("i" => "itemreceita"), "")
				->join(array("r" => "receita"), "r.rec_codigo=i.rec_codigo", "")
				->join(array("a" => "atendimento"), "a.ate_codigo=r.ate_codigo", "age_codigo")
				//->where("age_codigo=?", $age_codigo)
				->where("irec_codigo=?", $irec_codigo);

		$item = $this->fetchRow($where);
		if ($item)
			$this->delete("irec_codigo=" . $irec_codigo);

		return true;
	}

  public function receitasPorUsuario($usu_codigo=FALSE){
    $seisMesesAtras = date('Y-m-d', strtotime('6 months ago'));
    $sql = $this->select(FALSE)
      ->setIntegrityCheck(FALSE)
      ->from(array("rec"=>"receita"),array('rec_codigo', "rec_validade","rec_data"))
      ->join(array("itrec"=>"itemreceita"),"itrec.rec_codigo=rec.rec_codigo",array("itrec.pro_codigo","irec_recomendacao","irec_quantidade","irec_produto","itrec.irec_codigo"))
      ->join(array("ate"=>"atendimento"),"ate.ate_codigo=rec.ate_codigo","")
      ->joinLeft(array("pro"=>"produto"),"pro.pro_codigo=itrec.pro_codigo","pro_nome")
      ->joinLeft(array("usr"=>"usuarios"),"ate.med_codigo = usr.usr_codigo","usr_nome")
      ->where("ate.usu_codigo=?",$usu_codigo)
      ->where("rec.rec_data>=?",$seisMesesAtras)
      ->order('rec_codigo DESC');
      // die($sql);
    return $this->fetchAll($sql);
  }

  public function getItensReceita($rec_codigo=false){
      $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("irec"=>"itemreceita"))
                    ->join(array("pro"=>"produto"),"pro.pro_codigo=irec.pro_codigo",array("pro_codigo","pro_nome"))
                    ->join(array("rec"=>"receita"),"rec.rec_codigo=irec.rec_codigo","")
                    ->where("irec.rec_codigo=$rec_codigo")
                    ->where("rec_finalizada<>'S'")
                    ->order("irec_codigo");
      return $this->fetchAll($where);
  }




}
