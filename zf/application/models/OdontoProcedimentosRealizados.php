<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_OdontoProcedimentosRealizados extends Elotech_Db_Table_Abstract {

    protected $_name = "odonto_procedimentos_realizados";
    protected $_primary = "odo_preal_codigo";
    protected $_dependentTables = Array();

    // Lista todos os procedimentos realizados
    public function listaProcedimentosRealizados($tratCodigo = FALSE, $procRealCodigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), array("odo_preal_codigo", "odo_preal_dentenum", "odo_preal_denteface", "odo_preal_denteanot", "proc_codigo"))
                ->join(array("proc" => "procedimento"), "odpr.proc_codigo=proc.proc_codigo", array("proc.proc_nome", "proc.proc_codigo_sus"))
                ->join(array("odpc" => "odonto_procedimentos_controle"), "odpr.odo_pcon_codigo=odpc.odo_pcon_codigo", "")
                ->where("odpc.odo_trat_codigo =?", $tratCodigo);
        if ($procRealCodigo != FALSE) {
            $sql->where("odpr.odo_preal_codigo =?", $procRealCodigo);
        }
        $sql->order("odpr.odo_preal_codigo DESC");
        return $this->fetchAll($sql);
    }

    public function confereProcedimentosRealizadosExodontiaPorUsu($tratCodigo, $usu_codigo, $dente_num) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odo_preal" => "odonto_procedimentos_realizados"), array("COUNT(odo_preal_codigo) AS confExodontia"))
                ->join(array("proc" => "procedimento"), "odo_preal.proc_codigo=proc.proc_codigo", "")
                ->join(array("odo_pcon" => "odonto_procedimentos_controle"), "odo_preal.odo_pcon_codigo=odo_pcon.odo_pcon_codigo", "")
                ->join(array("ate" => "atendimento"), "odo_pcon.ate_codigo=ate.ate_codigo", "")
                ->where("odo_pcon.odo_trat_codigo =?", $tratCodigo)
                ->where("odo_preal.odo_preal_dentenum =?", $dente_num)
                ->where("ate.usu_codigo =?", $usu_codigo)
                ->where("proc.proc_nome ilike '%EXODONTIA%'");
        //die($sql);
        return $this->fetchRow($sql);
    }

    public function listaDentesQuePrecisaValidacao($usu_codigo) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odo_preal" => "odonto_procedimentos_realizados"), array("odo_preal_dentenum", "odo_preal_denteface", "proc_codigo"))
                ->join(array("proc" => "procedimento"), "odo_preal.proc_codigo=proc.proc_codigo", "")
                ->join(array("odo_pcon" => "odonto_procedimentos_controle"), "odo_preal.odo_pcon_codigo=odo_pcon.odo_pcon_codigo", "")
                ->join(array("ate" => "atendimento"), "odo_pcon.ate_codigo=ate.ate_codigo", "")
                ->where("ate.usu_codigo =?", $usu_codigo)
                ->where("proc.proc_nome ilike '%EXODONTIA%'");
        return $this->fetchAll($sql);
    }

    // Pega os do procedimento realizado
    public function getProcedimentoRealizado($procRealCodigo) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), array("odo_preal_codigo", "proc_codigo", "odo_preal_dentenum", "odo_preal_denteface", "odo_preal_denteanot", "odo_pcon_codigo"))
                ->join(array("proc" => "procedimento"), "odpr.proc_codigo=proc.proc_codigo", array("proc.proc_nome"))
                ->where("odpr.odo_preal_codigo =?", $procRealCodigo);
        return $this->fetchRow($sql);
    }

    // Pega os dados do procedimento especificado de acordo com o tratamento
    public function getUltimoProcedimentoRealizado($tratCodigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), array("odo_preal_codigo", "odo_preal_dentenum", "odo_preal_denteface", "odo_preal_denteanot"))
                ->join(array("proc" => "procedimento"), "odpr.proc_codigo=proc.proc_codigo", array("proc.proc_nome"))
                ->join(array("odpc" => "odonto_procedimentos_controle"), "odpr.odo_pcon_codigo=odpc.odo_pcon_codigo", "")
                ->where("odpc.odo_trat_codigo =?", $tratCodigo)
                ->order("odpr.odo_preal_codigo DESC")
                ->limit(1);
        return $this->fetchRow($sql);
    }

    // Lista os procedimentos realizados no dente
    public function getProcedimentosDente($dente = FALSE, $tratCodigo = FALSE, $usuCodigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), array("odo_preal_denteanot", "odo_preal_denteface", "odo_preal_dtcadastro"))
                ->join(array("proc" => "procedimento"), "odpr.proc_codigo=proc.proc_codigo", array("proc_nome"))
                ->join(array("odpc" => "odonto_procedimentos_controle"), "odpr.odo_pcon_codigo=odpc.odo_pcon_codigo", "")
                ->join(array("odt" => "odonto_tratamento"), "odpc.odo_trat_codigo=odt.odo_trat_codigo", "")
                ->join(array("ate" => "atendimento"), "odt.ate_codigo_origem=ate.ate_codigo", "")
                ->where("odpr.odo_preal_dentenum =?", $dente)
                ->where("odpc.odo_trat_codigo =?", $tratCodigo)
                ->where("ate.usu_codigo =?", $usuCodigo);
        return $this->fetchAll($sql);
    }

    // Salvando o procedimento realizado em banco
    public function salvar($data) {
        parent::salvar($data);
    }

    // Função que remove o procedimento selecionado 
    public function excluirProcedimentoRealizado($procRealCodigo) {
        return $this->delete("odo_preal_codigo = $procRealCodigo");
    }

    // Método que confere se o procedimento está no atendimento atual ou não
    public function confereProcedimentoRealizadoAtendimento($tratCodigo = FALSE, $ateCodigo = FALSE, $procRealCodigo = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), "odo_preal_codigo")
                ->join(array("odpc" => "odonto_procedimentos_controle"), "odpr.odo_pcon_codigo=odpc.odo_pcon_codigo")
                ->where("odpr.odo_preal_codigo =?", $procRealCodigo)
                ->where("odpc.odo_trat_codigo =?", $tratCodigo)
                ->where("odpc.ate_codigo =?", $ateCodigo);
        return $this->fetchRow($sql);
    }

    public function excluirPorProcedimentoControle($odoPconCod = FALSE) {
        if ($odoPconCod) {
            $item = $this->fetchAll("odo_pcon_codigo=$odoPconCod");
            try {
                if (count($item) > 0)
                    foreach ($item as $value) {
                        $value->delete();
                    }
            } catch (Exception $ex) {
                throw new Zend_Validate_Exception("Falha ao excluir procedimento realizado: " . $ex->getMessage());
            }
        }
        return true;
    }

    public function listaProcedimentoRealizadoPorCodigo($codigoProcedimentoOdonto) {
        $sql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("opr" => "odonto_procedimentos_realizados"), array("opr.proc_codigo"))
                ->where("opr.proc_codigo = $codigoProcedimentoOdonto");
        
        return $this->fetchAll($sql);
    }

    public function atualizaProcedimentoOdontologico($codigoProcNovo, $codigoProcAnterior) {
        $data = array("proc_codigo" => $codigoProcNovo);
        $where = $this->select()->where("proc_codigo = $codigoProcAnterior")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        return $this->update($data, $condicao);
    }

    public function listaProcedimentosPorAtendimento($ate_codigo){
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("odpr" => "odonto_procedimentos_realizados"), array("odo_preal_codigo", "odo_preal_dentenum", "odo_preal_denteface", "odo_preal_denteanot", "proc_codigo"))
                ->join(array("proc" => "procedimento"), "odpr.proc_codigo=proc.proc_codigo", array("proc.proc_nome", "proc.proc_codigo_sus"))
                ->join(array("odpc" => "odonto_procedimentos_controle"), "odpr.odo_pcon_codigo=odpc.odo_pcon_codigo", "")
                ->where("odpc.ate_codigo =?", $ate_codigo);
        return $this->fetchAll($sql);
    }

    public function procedimentosOdontoEditar($ateCodigo){
        $ateCodigo = $ateCodigo;
        $sql = $this->getDefaultAdapter()->query(
            "
            SELECT odpc.odo_pcon_codigo, odpc.ate_codigo, odpr.odo_pcon_codigo, odpr.proc_codigo, proc.proc_nome from odonto_procedimentos_controle as odpc
            INNER JOIN odonto_procedimentos_realizados AS odpr
                ON odpc.odo_pcon_codigo = odpr.odo_pcon_codigo
            INNER JOIN procedimento AS proc
                ON odpr.proc_codigo = proc.proc_codigo
            WHERE odpc.ate_codigo = $ateCodigo
            "
        )->fetchAll();
        return $sql;
    }
}

?>
