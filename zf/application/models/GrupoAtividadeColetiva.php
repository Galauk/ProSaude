<?php

class Application_Model_GrupoAtividadeColetiva extends Elotech_Db_Table_Abstract {

	protected $_name = 'grupo_atividade_coletiva';
	protected $_primary = 'gac_codigo';

	public function salvar(array $data) {
		try {
			return parent::salvar($data);
		} catch (Exception $exc) {
			throw new Zend_Validate_Exception("Erro ao cadastrar grupo: ".$exc->getMessage(), $exc->getMessage());
		}
	}

	public function getGrupos($status=NULL) {
		$subselect = "(SELECT COUNT(gap.gap_codigo) FROM grupo_atividade_participante gap WHERE gap.gac_codigo = gac.gac_codigo) as qtd_part";
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(
				array("gac" => "grupo_atividade_coletiva"),
				array(
					"gac_codigo", "gac_descricao", "gac_status",
					$subselect)
			)
			->order("gac_descricao");
		if($status){
			$where->where("gac_status=?", $status);
		}
		return $this->fetchAll($where);
	}

	public function getGrupo($codGrupo) {
		$subselect = "(SELECT COUNT(gap.gap_codigo) FROM grupo_atividade_participante gap WHERE gap.gac_codigo = gac.gac_codigo) as qtd_part";
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(
				array("gac" => "grupo_atividade_coletiva"),
				array(
					"gac_codigo", "gac_descricao", "gac_status","(0) as participantes",
					$subselect)
			)
			->where("gac_codigo = ?", $codGrupo)
			->order("gac_descricao");
		$data = $this->fetchRow($where);
		$data->__set('participantes', $this->getParticipantesPorGrupo($codGrupo));
		return $data;
	}

	public function getParticipantesPorGrupo($gac_codigo = FALSE) {
		if (empty($gac_codigo))
			return false;
		$where = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(array("gap" => "grupo_atividade_participante"))
			->join(array("usu" => "usuario"), "usu.usu_codigo=gap.usu_codigo", "usu_nome")
			->where("gap.gac_codigo = $gac_codigo");
		return $this->fetchAll($where);
	}

	public function getGruposPorNome($grupoDescricao = FALSE) {
		$subselect = "(SELECT COUNT(gap.gap_codigo) FROM grupo_atividade_participante gap WHERE gap.gac_codigo = gac.gac_codigo) as qtd_part";
		$sql = $this->select(FALSE)
			->setIntegrityCheck(FALSE)
			->from(
				array("gac" => "grupo_atividade_coletiva"),
				array(
					"gac_codigo", "gac_descricao", "gac_status",
					$subselect)
			)
			->where("gac_descricao ilike ?", "%" . $grupoDescricao . "%");
		return $this->fetchAll($sql);
	}

	public function ativar($id=FALSE){
		$item = $this->fetchRow("gac_codigo=$id");
		if ($item){
			$item->gac_status = 1;
			$item->save();
		}
		return true;
	}

	public function desativar($id=FALSE){
		$item = $this->fetchRow("gac_codigo=$id");
		if ($item){
			$item->gac_status = 0;
			$item->save();
		}
		return true;
	}
}
