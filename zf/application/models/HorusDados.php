<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_HorusDados extends Elotech_Db_Table_Abstract {
    
    protected $_name = "horus_dados";
    protected $_primary = "hor_dad_codigo";
    
    public function listaMovEntradasParaExportar($dtInicioExpHorus,$dtFinalExpHorus){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_codigo","hor_dad_tpxml","hor_dad_counidadecnes","hor_dad_nuproduto","hor_dad_vlitem","TO_CHAR(hor_dad_dtvalidade,'YYYY-MM-DD') AS hor_dad_dtvalidade","hor_dad_nulote","hor_dad_qtd","TO_CHAR(hor_dad_dtrecebimentoprod,'YYYY-MM-DD') AS hor_dad_dtrecebimentoprod","hor_dad_tpproduto","hor_dad_tpmovimentacao"))
                    ->where("hor_dad_tpxml = 'E'")
                    ->where("hor_dad_status_envio = 'F'")
                    ->where("hor_dad_dtcadastro >= '$dtInicioExpHorus'")
                    ->where("hor_dad_dtcadastro <= '$dtFinalExpHorus'");
        // die($sql);
        return $this->fetchAll($sql);
    }
    
    public function atualizaDadosMovEntradas($dados,$dtInicioExpHorus, $dtFinalExpHorus){
        $where['hor_dad_tpxml = ?'] = 'E';
        $where['hor_dad_status_envio = ?'] = 'F';
        $where['hor_dad_dtcadastro >= ?'] = "'$dtInicioExpHorus'"; 
        $where['hor_dad_dtcadastro <= ?'] = "'$dtFinalExpHorus'";
        return $this->update($dados, $where);
    }
    
    public function listaMovSaidasParaExportar($dtInicioExpHorus,$dtFinalExpHorus){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_codigo","hor_dad_dtcadastro","hor_dad_tpxml","hor_dad_counidadecnes","hor_dad_nuproduto","hor_dad_vlitem","TO_CHAR(hor_dad_dtvalidade,'YYYY-MM-DD') AS hor_dad_dtvalidade","hor_dad_nulote","hor_dad_qtd","TO_CHAR(hor_dad_dtrecebimentoprod,'YYYY-MM-DD') AS hor_dad_dtrecebimentoprod","hor_dad_tpproduto","hor_dad_tpmovimentacao"))
                    ->where("hor_dad_tpxml = 'S'")
                    ->where("hor_dad_status_envio = 'F'")
                    ->where("hor_dad_dtcadastro >= '$dtInicioExpHorus'")
                    ->where("hor_dad_dtcadastro <= '$dtFinalExpHorus'");
        return $this->fetchAll($sql);
    }
    
    public function atualizaDadosMovSaidas($dados,$dtInicioExpHorus, $dtFinalExpHorus){
        $where['hor_dad_tpxml = ?'] = 'S';
        $where['hor_dad_status_envio = ?'] = 'F';
        $where['hor_dad_dtcadastro >= ?'] = "'$dtInicioExpHorus'"; 
        $where['hor_dad_dtcadastro <= ?'] = "'$dtFinalExpHorus'";
        return $this->update($dados, $where);
    }
    
    public function listaMovDispensacoesParaExportar($dtInicioExpHorus,$dtFinalExpHorus){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_codigo","hor_dad_tpxml","hor_dad_counidadecnes","hor_dad_nuproduto","hor_dad_vlitem","TO_CHAR(hor_dad_dtvalidade,'YYYY-MM-DD') AS hor_dad_dtvalidade","hor_dad_nulote","hor_dad_qtd","TO_CHAR(hor_dad_dtrecebimentoprod,'YYYY-MM-DD') AS hor_dad_dtrecebimentoprod","hor_dad_tpproduto","hor_dad_tpmovimentacao","hor_dad_nucnspaciente"))
                    ->where("hor_dad_tpxml = 'D'")
                    ->where("hor_dad_status_envio = 'F'")
                    ->where("hor_dad_dtcadastro >= '$dtInicioExpHorus'")
                    ->where("hor_dad_dtcadastro <= '$dtFinalExpHorus'");
                    //->where("hor_dad_codigo >= '15675'")
                    //->where("hor_dad_codigo < '22300'");
        return $this->fetchAll($sql);
    }
    
    public function atualizaDadosMovDispensacaoAction($dados,$dtInicioExpHorus, $dtFinalExpHorus){
        $where['hor_dad_tpxml = ?'] = 'D';
        $where['hor_dad_status_envio = ?'] = 'F';
        $where['hor_dad_dtcadastro >= ?'] = "'$dtInicioExpHorus'"; 
        $where['hor_dad_dtcadastro <= ?'] = "'$dtFinalExpHorus'";
        return $this->update($dados, $where);
    }
    
    public function salvar($data) {
        parent::salvar($data);
    }
    
    public function getNumRegistrosAExportar($tpXml,$dtInicioExpHorus,$dtFinalExpHorus){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"),array("total_ent" => "COUNT(hor_dad_codigo)"))
                    ->where("hor_dad_tpxml = '$tpXml'")
                    ->where("hor_dad_status_envio = 'F'")
                    ->where("hor_dad_dtcadastro >= '$dtInicioExpHorus'")
                    ->where("hor_dad_dtcadastro <= '$dtFinalExpHorus'");
                    // die($sql);
        return $this->fetchRow($sql);
    }
    
    public function listaDadosRelConsultaDados($dadosRelConsulta){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad" => "horus_dados"),array("hor_dad_tpxml","hor_dad_nuproduto","hor_dad_qtd","hor_dad_numprotocolo_envio","hor_dad_nome_respenvio"));
            if($dadosRelConsulta[hor_dad_status_envio] != "TODOS") {
                $sql->where("hor_dad_status_envio = '$dadosRelConsulta[hor_dad_status_envio]'");
            } 
            $numTpxml = count($dadosRelConsulta[hor_dad_tpxml]);
            if ($numTpxml == "1") {
                $sql->where("hor_dad_tpxml = '".$dadosRelConsulta[hor_dad_tpxml][0]."'");
            }
            if ($numTpxml == "2") {
                $sql->where("(hor_dad_tpxml = '".$dadosRelConsulta[hor_dad_tpxml][0]."'");
                $sql->orwhere("hor_dad_tpxml = '".$dadosRelConsulta[hor_dad_tpxml][1]."')");
            }
            if($dadosRelConsulta[hor_dad_nome_respenvio] != "") {
                $sql->where("hor_dad_nome_respenvio = '$dadosRelConsulta[hor_dad_nome_respenvio]'");
            }
            if($dadosRelConsulta[hor_dad_numprotocolo_envio] != "") {
                $sql->where("hor_dad_numprotocolo_envio = '$dadosRelConsulta[hor_dad_numprotocolo_envio]'");
            }
            if($dadosRelConsulta[hor_dad_status_envio] == "T") {
                if($dadosRelConsulta[hor_dad_dtinicial] != "") {
                    $sql->where("hor_dad_dtenvio >= '$dadosRelConsulta[hor_dad_dtinicial]'");
                }
                if($dadosRelConsulta[hor_dad_dtfinal] != "") {
                    $sql->where("hor_dad_dtenvio <= '$dadosRelConsulta[hor_dad_dtfinal]'");
                }
            }
            if($dadosRelConsulta[hor_dad_status_envio] != "T") {
                if($dadosRelConsulta[hor_dad_dtinicial] != "") {
                    $sql->where("hor_dad_dtcadastro >= '$dadosRelConsulta[hor_dad_dtinicial]'");
                }
                if($dadosRelConsulta[hor_dad_dtfinal] != "") {
                    $sql->where("hor_dad_dtcadastro <= '$dadosRelConsulta[hor_dad_dtfinal]'");
                }
            }
            $sql->order("hor_dad_tpxml ASC");
            return $this->fetchAll($sql);
    }
    
    public function listaProtocolos(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_numprotocolo_envio","hor_dad_dtenvio","hor_dad_tpxml" => "(CASE WHEN hor_dad_tpxml='E' THEN 'ENTRADA' WHEN hor_dad_tpxml='S' THEN 'SAÍDA' WHEN hor_dad_tpxml='D' THEN 'DISPENSAÇÃO' END)"))
                    ->where("hor_dad_status_envio = 't'")
                    ->order("hor_dad_dtenvio DESC")
                    ->limit(15);
        return $this->fetchAll($sql);
    }
    
    public function listaProtocolosPorData($data=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_numprotocolo_envio","hor_dad_dtenvio","hor_dad_tpxml" => "(CASE WHEN hor_dad_tpxml='E' THEN 'ENTRADA' WHEN hor_dad_tpxml='S' THEN 'SAÍDA' WHEN hor_dad_tpxml='D' THEN 'DISPENSAÇÃO' END)"))
                    ->where("hor_dad_status_envio = 't'");
        if ($data) {
            $sql->where("to_char(hor_dad_dtenvio,'DD/MM/YYYY') = '$data'");
        }
        $sql->order("hor_dad_dtenvio DESC");
        return $this->fetchAll($sql);
    }
    
    public function getDadosRespProtocolo($numProtocolo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_numprotocolo_envio","hor_dad_dtenvio","hor_dad_nome_respenvio","hor_dad_tpxml"))
                    ->where("hor_dad_numprotocolo_envio =?",$numProtocolo);
        return $this->fetchRow($sql);
    }
    
    public function atualizaDadosProtocolo($dados,$numProtocolo){
        $where['hor_dad_numprotocolo_envio = ?'] = "'$numProtocolo'";
        return $this->update($dados, $where);
    }
    
    public function getDadosCabecalhoXmlPorProtocolo($numProtocolo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("hor_dad"=>"horus_dados"),array("hor_dad_nome_respenvio","hor_dad_tpxml"))
                    ->where("hor_dad_numprotocolo_envio =?",$numProtocolo);
        return $this->fetchRow($sql);
    }
    
    public function getConteudoXmlPorProtocolo($numProtocolo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"))
                    ->where("hor_dad_numprotocolo_envio =?",$numProtocolo);
        return $this->fetchAll($sql);
    }
    
    public function getConteudoPorProtocoloProduto($numProtocolo=FALSE,$numProduto=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"))
                    ->where("hor_dad_numprotocolo_envio =?",$numProtocolo);
        if ($numProduto)
            $sql->where("hor_dad_nuproduto =?",$numProduto);
            
            $sql->order("hor_dad_codigo ASC");
                   
        return $this->fetchAll($sql);
    }
    
    public function verificaProtocolo($numProtocolo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"),array("COUNT(hor_dad_codigo) AS qtd_protocolo"))
                    ->where("hor_dad_numprotocolo_envio =?",$numProtocolo);
        return $this->fetchRow($sql);
    }
    
    public function getDadosPorCodigo($horDadCodigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("hor_dad"=>"horus_dados"))
                    ->where("hor_dad_codigo =?",$horDadCodigo);
        //die($sql);
        return $this->fetchRow($sql);
    }
    
} 
?>
