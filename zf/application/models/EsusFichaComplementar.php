<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusFichaComplementar extends Elotech_Db_Table_Abstract
{

    protected $_name = 'esus_ficha_complementar';
    protected $_primary = 'efc_codigo';
    protected $_sequence = 'esus_ficha_complementar_efc_codigo_seq';

    public function getDadosPorUuid($uuid = FALSE)
    {
        $sql = $this->select(FALSE)
            ->distinct()
            ->setIntegrityCheck(FALSE)
            ->from(array("efc" => "esus_ficha_complementar"))
            ->join(array("esp" => "especialidade"), "efc.esp_codigo=esp.esp_codigo", array("cod_cbo",""))
            ->join(array("usr" => "usuarios"), "efc.usr_codigo=usr.usr_codigo", array("usr_nome"))
            ->join(array("uni" => "unidade"), "efc.uni_codigo=uni.uni_codigo", array("uni_desc"))
            ->where("uuid_ficha = ?", $uuid);
        return $this->fetchAll($sql);
    }

    public function getDadosPorId($id)
    {
        $sql = $this->select(FALSE)
            ->distinct()
            ->setIntegrityCheck(FALSE)
            ->from(["efc" => "esus_ficha_complementar"])
            ->join(["esp" => "especialidade"], "efc.esp_codigo = esp.esp_codigo", ["cod_cbo"])
            ->join(["usr" => "usuarios"], "efc.usr_codigo = usr.usr_codigo", ["usr_nome"])
            ->join(["usu" => "usuario"], "efc.usu_codigo = usu.usu_codigo", ["usu_nome"])
            ->joinLeft(["resp" => "usuario"], "efc.efc_usu_responsavel = resp.usu_codigo", ["usu_responsavel_nome" => "usu_nome"])
            ->join(["uni" => "unidade"], "efc.uni_codigo = uni.uni_codigo", ["uni_desc"])
            ->where("efc_codigo = ?", $id);
        return $this->fetchRow($sql);
    }

    public function salvar($data)
    {
        $this->emptyToUnset($data);
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao atualizar dados: " . $exc->getMessage());
        }
    }

    public function anularCampoUuidPeloUuid($UUID)
    {

        $data = array("uuid_ficha" => "");
        $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];

        //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

        return $this->update($data, $where);

    }

    public function buscarFichas($term = FALSE, $tipoBusca = FALSE)
    {
        $sql = $this->select(FALSE)
            ->distinct()
            ->setIntegrityCheck(FALSE)
            ->from(array("efc" => "esus_ficha_complementar"))
            ->join(array("esp" => "especialidade"), "efc.esp_codigo=esp.esp_codigo", array("cod_cbo"))
            ->join(array("usr" => "usuarios"), "efc.usr_codigo=usr.usr_codigo", array("usr_nome"))
            ->join(array("uni" => "unidade"), "efc.uni_codigo=uni.uni_codigo", array("uni_desc"));


        switch ($tipoBusca) {
            case 1:
                $sql->where("usr.usr_nome ILIKE '%$term%'");
                break;
            case 2:
                $sql->where("ate.ate_data = '$term'");
                break;
            case 3:
                $sql->where("esp.cod_cbo = '$term'");
                break;
            case 4:
                $sql->where("uni.uni_desc ILIKE '%$term%'");
                break;
        }
        $sql->order(array("efc.efc_data DESC","efc.efc_codigo DESC"));
        return $this->fetchAll($sql);
    }
    public function excluir($id)
    {
        $registro = $this->fetchRow("efc_codigo = $id");
        if ($registro) {
            $registro->delete();

            return true;
        }
    }
}
