<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbReferenciaRespostaFicha extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_referencia_resposta_ficha';
	protected $_primary = 'ref_res_codigo';

    public function salvar(array $data) {
        
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($data);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($data);
    }

    public function imprimirFicha($codigoFichaUsuario){

        $codigoFichaUsuarioParam = $codigoFichaUsuario;

        $sql = $this->getDefaultAdapter()->query(
            "SELECT ficha_resposta.ref_res_ficha_usu, ficha_resposta.ref_res_resposta, ficha_resposta.ref_grupo_pergunta, perguntas.est_pergunta,
                    grupo.est_gruponome, grupo.id_grupoperg, ficha_lista.est_nomeficha, ficha_lista.est_nivelalto_inicio, ficha_lista.est_nivelmedio_inicio,
                    ficha_lista.est_nivelmedio_fim, ficha_lista.est_nivelbaixo_inicio, ficha_lista.est_nivelbaixo_fim, ficha_lista.est_recomendacao_nivel_medio,
                    ficha_lista.est_recomendacao_nivel_alto, ficha_lista.est_recomendacao_nivel_baixo,
                    moni_baixo.desc_monitoramento baixo, moni_medio.desc_monitoramento medio, moni_alto.desc_monitoramento alto, 
                    ficha_usu.est_score, to_char(ficha_lista.dt_cadastro, 'DD/MM/YYYY'), perguntas.id_perg

                    FROM tb_referencia_resposta_ficha  as ficha_resposta
                
                        inner join estratificacao_perguntas as perguntas
                            on perguntas.id_perg = ficha_resposta.ref_res_id_pergunta
                
                        inner join estratificacao_grupos as grupo
                            on grupo.id_grupoperg = ficha_resposta.ref_grupo_pergunta
                
                        inner join estratificacao_usu as ficha_usu
                            on ficha_usu.id_estusu = ficha_resposta.ref_res_ficha_usu
                
                        inner join estratificacao_lista as ficha_lista
                            on ficha_usu.est_listaid = ficha_lista.id_estlista
                            
                        inner join tb_monitoramento as moni_baixo
                            on ficha_lista.est_monitoramento_baixo = moni_baixo.id_monitoramento

                        inner join tb_monitoramento as moni_medio
                            on ficha_lista.est_monitoramento_medio = moni_medio.id_monitoramento

                        inner join tb_monitoramento as moni_alto
                            on ficha_lista.est_monitoramento_alto = moni_alto.id_monitoramento
                        
                where ficha_resposta.ref_res_ficha_usu = $codigoFichaUsuarioParam
            ")->fetchAll();
        
        return $sql;
    }

}
























