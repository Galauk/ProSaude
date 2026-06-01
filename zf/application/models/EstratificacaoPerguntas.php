<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_EstratificacaoPerguntas extends Elotech_Db_Table_Abstract {

    protected $_name = 'estratificacao_perguntas';
	protected $_primary = 'id_perg';

    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($data);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($data);
    }

    public function atualizaPerguntas($recebeCodigo, $recebeTituloPergunta, $recebeValorPergunta){
        // die("dasdsa");
        
        $sql = $this->getDefaultAdapter()->query(
            "UPDATE estratificacao_perguntas set est_pergunta = '$recebeTituloPergunta', est_pergvalue = $recebeValorPergunta 
                where id_perg = $recebeCodigo
            ")->fetchAll();

        return $sql;

    }

    public function excluirPergunta($recebeCodigo){

        $recebeCodigoParam = $recebeCodigo;

        $sql = $this->getDefaultAdapter()->query(
            "DELETE FROM estratificacao_perguntas WHERE id_perg = $recebeCodigoParam
            ")->fetchAll();
        
        return $sql;
    }

}
