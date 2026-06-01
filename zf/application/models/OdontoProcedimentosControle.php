<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_OdontoProcedimentosControle extends Elotech_Db_Table_Abstract {
    // Dados da tabela
    protected $_name = "odonto_procedimentos_controle";
    protected $_primary = "odo_pcon_codigo";
    protected $_dependentTables = Array();
    
    public function getCodigoTratamentoAtendimento($tratCodigo = FALSE, $ateCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odpc"=>"odonto_procedimentos_controle"),array("odo_pcon_codigo"))
                    ->join(array("ate"=>"atendimento"),"odpc.ate_codigo=ate.ate_codigo","")
                    ->join(array("odt"=>"odonto_tratamento"),"odpc.odo_trat_codigo=odt.odo_trat_codigo","")
                    ->where("odpc.odo_trat_codigo =?",$tratCodigo)
                    ->where("odpc.ate_codigo =?",$ateCodigo);
        return $this->fetchRow($sql);
    }
    
    public function salvar($data){
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            die($exc->getMessage());
        }
    }
    
    public function getDadosPorAtendimento($tratCodigo = FALSE, $ateCodigo = FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("odpc"=>"odonto_procedimentos_controle"),array("odo_pcon_codigo"))
                    ->join(array("ate"=>"atendimento"),"odpc.ate_codigo=ate.ate_codigo","")
                    ->join(array("odt"=>"odonto_tratamento"),"odpc.odo_trat_codigo=odt.odo_trat_codigo","")
                    ->where("odpc.ate_codigo =?",$ateCodigo);
        return $this->fetchRow($sql);
    }
    
    public function excluirPorAtendimento($ate_codigo=FALSE){
        $item = $this->fetchRow("ate_codigo=$ate_codigo");
        try{
            if ($item) { $item->delete(); }
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir procedimento controle: ".$ex->getMessage());
        }
        return true;
    }
 
}
?>
