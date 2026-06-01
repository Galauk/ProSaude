<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_AtendimentoPrenatal extends Elotech_Db_Table_Abstract
{

    protected $_name = 'atendimento_prenatal';
    protected $_primary = 'atp_codigo';

    public function salvar(array $data){
        try {
            $this->emptyToNull($data);
            $atendimento = parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar atendimento de pré natal!" . $exc->getMessage());
        }
        return $atendimento;
    }

    public function checaNumeroGestacao($usu_codigo)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("apn" => "atendimento_prenatal"), array("numero_gestacao"))
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = apn.ate_codigo", "")
            ->where("ate.usu_codigo=?", $usu_codigo)
            ->order(array("ate.ate_codigo DESC"));
        $gestacao = $this->fetchRow($sql);
        if (!$gestacao) {
            $numGestacao = 1;
        } else {
            $numGestacao = $gestacao->numero_gestacao;
        }
        return $numGestacao;
    }

    public function checaUltimaConsulta($usu_codigo)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("apn" => "atendimento_prenatal"), array("tipo_consulta"))
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = apn.ate_codigo", "")
            ->where("ate.usu_codigo=?", $usu_codigo)
            ->order(array("ate.ate_codigo DESC"));
        $consulta = $this->fetchRow($sql);

        if (!$consulta) {
            $tipoConsulta = '';
        } else {
            $tipoConsulta = $consulta->tipo_consulta;
        }
        return $tipoConsulta;
    }

    public function getDados($ate_codigo)
    {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck()
            ->from(array("atepn" => "atendimento_prenatal"))
            ->where("atepn.ate_codigo = $ate_codigo");
        return $this->fetchRow($sql);
    }

    public function buscaConsultas($usu_codigo, $numero_gestacao = FALSE)
    {
        $subSql = $this-> select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("pc" => "pre_consulta"), array("pc_peso", "pc_altura"))
            ->join(array("ate" => "atendimento"), "pc.age_codigo = ate.age_codigo")
            ->where("ate.usu_codigo = $usu_codigo")
            ->order("pc_codigo DESC")
            ->limit(1);

        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("atepn" => "atendimento_prenatal"))
            ->join(array("ate" => "atendimento"), "atepn.ate_codigo = ate.ate_codigo", array("ate_data", "usu_codigo"))
            ->join(array("usr" => "usuarios"), "ate.med_codigo = usr.usr_codigo", "usr_nome")
            ->joinLeft(array("pc" => $subSql), "pc.age_codigo = ate.age_codigo", array("pc_peso", "pc_altura"))
            ->where("ate.usu_codigo = $usu_codigo");
        if ($numero_gestacao) {
            $sql->where("numero_gestacao = $numero_gestacao");
        }
        $sql->order("ate.ate_data");
        return $this->fetchAll($sql);
    }

    public function getDum($usu_codigo)
    {
        $sql = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("atepn" => "atendimento_prenatal"), array("dum"))
            ->join(array("ate" => "atendimento"), "ate.ate_codigo = atepn.ate_codigo")
            ->where("ate.usu_codigo = $usu_codigo")
            ->order("atp_codigo DESC");
        $dado = $this->fetchAll($sql);

        foreach ($dado as $dum) {
            if ($dum->dum) {
                return $dum->dum;
            }
        }

    }
}
