<?php

class Elotech_View_Helper_VerificaAnexoArquivos extends Zend_View_Helper_Abstract {

    /**
     * Verifica se tem arquivos salvos para as solicitacoes
     * @param Zend_Db_Table_Row_Abstract $ate atendimento ($ate->buscar())
     * @return bool 
     */
    function verificaAnexoArquivos($req_codigo=FALSE,$ate_codigo=FALSE) {
        $tbReq = new Application_Model_RequisicaoExame();
        $registros = $tbReq->getRequisicoesComAnexo($req_codigo,$ate_codigo);
        if(count($registros)){
            return true;
        }else{
            return false;
        }
        
    }

} 