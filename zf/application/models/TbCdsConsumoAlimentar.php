<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsConsumoAlimentar extends Elotech_Db_Table_Abstract
{
    protected $_name = 'tb_cds_consumo_alimentar';
    protected $_primary = 'co_seq_cds_consumo_alimentar';

    public function salvar($dados)
    {
        try {
            return parent::salvar($dados);
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: " . $ex->getMessage());
        }
    }

    public function excluir($codConsumo)
    {
        $item = $this->fetchAll("co_seq_cds_consumo_alimentar=$codConsumo");
        if ($item) {
            foreach ($item as $value) {
                $value->delete();
            }
        }
    }

    public function getDados()
    {
        $tbUsr = new Application_Model_Usuarios();
        $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;

        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("csa" => "tb_cds_consumo_alimentar"), "co_seq_cds_consumo_alimentar")
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = csa.ate_codigo", "ate_data")
            ->join(array("usr" => "usuarios"), "ate.med_codigo = usr.usr_codigo", "usr_nome")
            ->join(array("usu" => "usuario"), "ate.usu_codigo = usu.usu_codigo", "usu_nome")
            ->join(array("esus" => "esus_consumo_alimentar"),"csa.co_seq_cds_consumo_alimentar = esus.co_cds_consumo_alimentar","uuid_ficha")
            ->order(array("ate_data DESC", "usr_nome DESC", "usu_nome DESC"));
        return $this->fetchAll($sql);
    }

    public function getDadosConsumoAlimentar($cod_consumo)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("csa" => "tb_cds_consumo_alimentar"))
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = csa.ate_codigo", array("ate_codigo", "ate_data"))
            ->join(array("usr" => "usuarios"), "ate.med_codigo = usr.usr_codigo", "usr_nome")
            ->join(array("usu" => "usuario"), "ate.usu_codigo = usu.usu_codigo", "usu_nome")
            ->where("co_seq_cds_consumo_alimentar = $cod_consumo");
        return $this->fetchAll($sql)->toArray();
    }

    public function carregaDados($id = NULL)
    {
        $sqlConsumo = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("csa" => "tb_cds_consumo_alimentar"))
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = csa.ate_codigo", array("ate_codigo", "ate_data"))
            ->join(array("uni" => "unidade"), "uni.uni_codigo = ate.uni_codigo", array("uni_codigo", "uni_desc"))
            ->join(array("tla" => "tb_local_atend"), "tla.co_local_atend = ate.co_local_atend", array("co_local_atend", "no_local_atend"))
            ->join(array("usr" => "usuarios"), "ate.med_codigo = usr.usr_codigo", array("usr_codigo", "usr_nome"))
            ->join(array("usu" => "usuario"), "ate.usu_codigo = usu.usu_codigo", array("usu_codigo", "usu_nome", "usu_datanasc"))
            ->where("csa.co_seq_cds_consumo_alimentar = $id");
        $dadosConsumo = $this->fetchRow($sqlConsumo)->toArray();
        $sqlRadios = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("car" => "tb_cds_consumo_alimentar_resposta"), array("co_qst_questao", "co_qst_resposta"))
            ->join(array("tca" => "tb_cds_consumo_alimentar"), "tca.co_seq_cds_consumo_alimentar = car.co_cds_consumo_alimentar", "ate_codigo")
            ->where("tca.co_seq_cds_consumo_alimentar = $id");
        foreach ($this->fetchAll($sqlRadios)->toArray() as $dados) {
            if ($dados["co_qst_questao"] == 12) {
                $dadosConsumo["check"][] = $dados;
            } else {
                $dadosConsumo["radio"][] = $dados;
            }
        }
        return $dadosConsumo;
    }

    public function busca($busca = FALSE, $tipoBusca = FALSE)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("csa" => "tb_cds_consumo_alimentar"), "co_seq_cds_consumo_alimentar")
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = csa.ate_codigo", "ate_data")
            ->join(array("usr" => "usuarios"), "ate.med_codigo = usr.usr_codigo", "usr_nome")
            ->join(array("usu" => "usuario"), "ate.usu_codigo = usu.usu_codigo", "usu_nome");
        switch ($tipoBusca) {
            case 1:
                $sql->where("usu.usu_nome ILIKE '%$busca%'");
                break;
            case 2:
                $sql->where("usr.usr_nome ILIKE '%$busca%'");
                break;
            case 3:
                $sql->where("ate.ate_data = '$busca'");
                break;
        }
        return $this->fetchAll($sql);
    }
}
