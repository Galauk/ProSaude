<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_fichaEspecialidadesEstratificacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'ficha_especialidade_estratificacao';
	protected $_primary = 're_codigo';

    public function salvar(array $data) {
        // echo '<pre>';print_r($data);die();
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar a ficha de estratificação ".$exc->getMessage());
        }
    }
    
    public function ficharPorEspecializade($recebeEspecialidadeUsuario){

        $recebeEspecialidade = intval($recebeEspecialidadeUsuario);

        // echo '<pre>';var_dump($recebeEspecialidade);die();

        $sql = $this->getDefaultAdapter()->query(

            "SELECT ficha.est_nomeficha, ficha.id_estlista FROM ficha_especialidade_estratificacao AS ficha_esp

                INNER JOIN estratificacao_lista AS ficha
                    ON ficha_esp.ref_ficha_codigo = ficha.id_estlista

                WHERE ficha_esp.ref_especilidade_codigo = $recebeEspecialidade
            "

        )->fetchAll();
        

        return $sql;
    }

}


// ficha_especilidades_estratificacao