<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_EstratificacaoGrupos extends Elotech_Db_Table_Abstract {

    protected $_name = 'estratificacao_grupos';
	protected $_primary = 'id_grupoperg';

    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        $this->emptyToUnset($data);
        //echo "<pre>".print_r($data,1);die();
        return parent::salvar($data);
    }
    public function listaGrupos(){

        $sql = $this->getDefaultAdapter()->query(
            "SELECT * FROM estratificacao_grupos
            "
        )->fetchAll();

        return $sql;
    }

    public function listarDadosBasicosDosGrupos(){
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT  grupo.id_grupoperg, grupo.est_gruponome, to_char(dt_cadastro, 'DD/MM/YYYY'), usr.usr_nome, grupo.grupo_ativo  from estratificacao_grupos as grupo
                inner join usuarios as usr
                    on usr.usr_codigo = grupo.est_usr
                ORDER BY grupo.id_grupoperg DESC
            "
        )->fetchAll();

        return $sql;

    }

    public function recuperaGrupoPerguntas($recebeCodigoGrupo){
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT grupo.id_grupoperg, grupo.est_gruponome, perg.est_pergunta, perg.est_pergvalue, perg.id_perg from estratificacao_grupos as grupo

                inner join estratificacao_perguntas as perg
                    on perg.est_idgrupo = grupo.id_grupoperg
        
                where grupo.id_grupoperg = $recebeCodigoGrupo
            "
        )->fetchAll();

        return $sql;

    }

    public function atualizaNomeGrupo($recebeCodigo, $recebeNomeGrupo){
        
        $sql = $this->getDefaultAdapter()->query(

            "UPDATE estratificacao_grupos SET est_gruponome = '$recebeNomeGrupo' WHERE id_grupoperg = $recebeCodigo
            
            ")->fetchAll();
        
        return $sql;
    }

    public function listarGrupoFicha(){

        $sql = $this->getDefaultAdapter()->query(
            "SELECT est_gruponome, id_grupoperg FROM estratificacao_grupos where grupo_ativo <> 'F'
            "
        )->fetchAll();

        return $sql;
    }

    public function carregaPerguntasDosGrupos($recebeCodigoGrupo){

        $recebeCodigoGrupoParam = $recebeCodigoGrupo;

        $sql = $this->getDefaultAdapter()->query(
            "SELECT est_gruponome, id_grupoperg, pergunta.est_pergunta, pergunta.est_pergvalue, grupo.est_gruponome 
                from estratificacao_grupos as grupo

                inner join estratificacao_perguntas as pergunta
                    on grupo.id_grupoperg = pergunta.est_idgrupo
            
                where grupo.id_grupoperg = $recebeCodigoGrupoParam
            "
        )->fetchAll();

        return $sql;
    }

    public function carregaPerguntasDosGruposPorFicha($recebeCodigoFicha){

        $recebeCodigoFichaParam = $recebeCodigoFicha;
        
        $sql = $this->getDefaultAdapter()->query(
            "SELECT perg.est_pergunta, est_pergvalue, perg.est_idgrupo, ficha.id_estlista, perg.id_perg, grupo.est_gruponome, ficha.est_nivelbaixo_inicio, 
                ficha.est_nivelbaixo_fim, ficha.est_nivelmedio_inicio, ficha.est_nivelmedio_fim, ficha.est_nivelalto_inicio,
                ficha.est_recomendacao_nivel_baixo, ficha.est_recomendacao_nivel_medio, ficha.est_recomendacao_nivel_alto
        
            FROM ficha_estratificacao_ref_grupo AS ficha_grupo
        
                INNER JOIN estratificacao_lista AS ficha
                    ON ficha_grupo.ficha_ref_codigo = id_estlista
            
                INNER JOIN estratificacao_perguntas AS perg
                    ON perg.est_idgrupo = ficha_grupo.grupo_ref_codigo
        
                INNER JOIN estratificacao_grupos as grupo
                   ON ficha_grupo.grupo_ref_codigo = grupo.id_grupoperg
        

            WHERE ficha.id_estlista = $recebeCodigoFichaParam;

            "
        )->fetchAll();

        
        return $sql;
    }

    public function desativarGrupo($recebeCodigoGrupo){

        $sql = $this->getDefaultAdapter()->query(
            "UPDATE estratificacao_grupos SET grupo_ativo = 'F' WHERE id_grupoperg = $recebeCodigoGrupo
            ")->fetchAll();
        
        return $sql;
    }

    
    public function ativarGrupo($recebeCodigoGrupo){

        $sql = $this->getDefaultAdapter()->query(
            "UPDATE estratificacao_grupos SET grupo_ativo = 'T' WHERE id_grupoperg = $recebeCodigoGrupo
            ")->fetchAll();
        
        return $sql;
    }

}
























