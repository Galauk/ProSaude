<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_GrupoProcedimento extends Elotech_Db_Table_Abstract {

	protected $_name = 'grupo_procedimento';
	protected $_primary = 'gp_codigo';
	protected $_sequence = 'grupo_procedimento_gp_codigo_seq';

	public function salvar(array $data) {
		try {
            $gp_codigo = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o agendamento: ".$exc->getMessage());
        }
        return $gp_codigo;
    }

	public function getGrupo($gp_codigo=FALSE){
		$sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("gp"=>"grupo_procedimento"), array("gp_codigo", "gp_descricao", "gp_obs"));
                if($gp_codigo) {
                	$sql->where("gp_codigo =?", $gp_codigo);
                }
             //   die($sql);
        return $this->fetchAll($sql);
	}

	public function getGrupoProcedimento($gp_codigo=FALSE){
		$sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("gp"=>"grupo_procedimento"), "")
                    ->join(array("rgp"=>"rl_grupo_procedimento"), "rgp.gp_codigo = gp.gp_codigo", array("co_gp_codigo"))
                    ->join(array("proc"=>"procedimento"), "proc.proc_codigo=rgp.proc_codigo", array("proc_codigo","proc_nome"));
                if($gp_codigo)
                	$sql->where("gp.gp_codigo =?", $gp_codigo);
                //die($sql);
        return $this->fetchAll($sql);
	}

    public function excluir($gp_codigo=FALSE) {
        $item = $this->fetchRow("gp_codigo=$gp_codigo");
        if ($item) {
                $item->delete();
        }
    }

}