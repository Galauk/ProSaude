<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_CadastroFamiliar extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cadastro_familiar';
    protected $_primary = 'tcf_prontuario_familiar';

    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar dados: ".$exc->getMessage());
        }
    }

    public function salvarRendaENumeroDeMembros($dados, $id){
        $recebeDados = $dados;
        $recebeId = $id;

        $sql = $this->getDefaultAdapter()->query("
            UPDATE tb_cadastro_familiar SET tcf_renda_familiar  = $recebeDados[tcf_renda_familiar] , 
            tcf_numero_membros = $recebeDados[tcf_numero_membros] 
            where tcf_prontuario_familiar = $id
        ")->fetch();

        return $sql;
    }

    public function excluirFamilia($tcf_prontuario_familiar){
        $prontuarioFaimliar = $tcf_prontuario_familiar;
        $sql = $this->getDefaultAdapter()->query(" DELETE FROM tb_cadastro_familiar WHERE tcf_prontuario_familiar = $prontuarioFaimliar ")->fetch();
        return $sql;
    }

    public function recuperaFamilias(){
        $sql = $this->getDefaultAdapter()->query("

            SELECT tbcad.tcf_prontuario_familiar, tbcad.tcf_renda_familiar, tbcad.tcf_numero_membros , usu.usu_codigo, tcomf_responsavel, usu_nome
            FROM tb_cadastro_familiar as tbcad
            INNER JOIN tb_composicao_familiar as tbcomf
                ON tbcad.tcf_prontuario_familiar = tbcomf.tcf_numero_prontuario_familiar and tcomf_responsavel = 'T'
            INNER JOIN usuario as usu 
                on usu.usu_codigo = tbcomf.usu_codigo

            ")->fetchAll();
        return $sql;
	}   

    public function recuperaRendaFamiliar($recebeProntuarioFamiliar){
        $recebeProntuarioFamiliar = $recebeProntuarioFamiliar;
        // die($recebeProntuarioFamiliar);
        $sql = $this->getDefaultAdapter()->query("
            SELECT tcf_renda_familiar FROM tb_cadastro_familiar WHERE tcf_prontuario_familiar = $recebeProntuarioFamiliar
        ")->fetch();

        return $sql;
    }
	
    public function decrementaRendaFamiliar($recebeRendaFamiliar, $recebeRendaIndividual, $recebeProntuarioFamiliar){
        // error_reporting(E_ALL);
        $recebeRendaFamiliar = $recebeRendaFamiliar[tcf_renda_familiar];
        $recebeRendaIndividual = $recebeRendaIndividual[tcomf_renda_mensal_usuario];
        $recebeProntuarioFamiliar = $recebeProntuarioFamiliar;

        $numeroDeMembros = $this->getDefaultAdapter()->query("
            SELECT tcf_numero_membros FROM tb_cadastro_familiar WHERE tcf_prontuario_familiar = $recebeProntuarioFamiliar
        ")->fetch();

        $novoTotalDeMembros = $numeroDeMembros[tcf_numero_membros] - 1;

        $resultado = $recebeRendaFamiliar - $recebeRendaIndividual;
        floatval($resultado);

        $sql = $this->getDefaultAdapter()->query("
            UPDATE tb_cadastro_familiar SET tcf_renda_familiar  = $resultado, tcf_numero_membros = $novoTotalDeMembros
            where tcf_prontuario_familiar = $recebeProntuarioFamiliar
        ")->fetch();
        
        return $sql;

    }

    public function excluirIntegrante($recebeCodigoIntegrante){
        $recebeCodigoIntegrante = $recebeCodigoIntegrante;

        $sql = $this->getDefaultAdapter()->query("
            DELETE FROM tb_composicao_familiar WHERE usu_codigo = $recebeCodigoIntegrante;
        ")->fetch();

        return $sql;
    }

    public function excluirComposicaoFamiliar($recebeProntuarioFamiliar){
        $recebeProntuarioFamiliar = $recebeProntuarioFamiliar;

        $sql = $this->getDefaultAdapter()->query("
                DELETE FROM tb_composicao_familiar WHERE tcf_numero_prontuario_familiar  = $recebeProntuarioFamiliar
        ")->fetch();

        return $sql;
    }
}

