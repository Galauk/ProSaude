<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbComposicaoFamiliar extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_composicao_familiar';
    protected $_primary = 'tcomf_id';

    public function salvar($data) {
        try{
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao salvar dados: ".$exc->getMessage());
        }
    }

        /**
     * Buscar os CID's
     * usado para alimentar o plugin de busca (jquery)
     * @return json
     */
    public function getMembros($tcf_prontuario_familiar){
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("tbcad"=>"tb_cadastro_familiar"),array("tcf_prontuario_familiar"))
            ->join(array("tbcomf" => "tb_composicao_familiar"), "tbcad.tcf_prontuario_familiar=tbcomf.tcf_numero_prontuario_familiar")
            ->join(array("usu" => "usuario"), "usu.usu_codigo=tbcomf.usu_membro")
            ->where("tcf_numero_prontuario_familiar=$tcf_prontuario_familiar");
        return $this->fetchAll($sql);
    }   

    public function getMembro($usu_membro){
        try{
            $sql = $this
                ->getDefaultAdapter()
                ->query("DELETE FROM tb_composicao_familiar WHERE usu_membro = $usu_membro")
                ->fetchAll();
            return $sql;
            } catch (Exception $ex) {
                throw new Zend_Validate_Exception("Falha ao excluir saldo: ".$ex->getMessage());
            }
    }

    public function verificaSeUsuarioJaEResponsavel($idDoPaciente){
        $idDoPaciente = $idDoPaciente;
        $sql = $this->getDefaultAdapter()
        ->query(" SELECT tcomf_responsavel FROM tb_composicao_familiar WHERE usu_codigo = $idDoPaciente ")->fetch();
        return $sql;
    }

    public function verificaSeUsuarioJaEstaCadastradoEmOutraFamilia($idDoPaciente){
        $idDoPaciente = $idDoPaciente;
        $sql = $this->getDefaultAdapter()
        ->query(" SELECT usu_codigo FROM tb_composicao_familiar WHERE usu_codigo = $idDoPaciente")->fetch();
        return $sql;
    }

    public function recuperaRendaTotalFamiliar($numeroDoProntuarioFamiliar){
        $numeroDoProntuarioFamiliar = $numeroDoProntuarioFamiliar;
        $sql = $this->getDefaultAdapter()->query("
            SELECT SUM(tcomf_renda_mensal_usuario) FROM tb_composicao_familiar 
                where tcf_numero_prontuario_familiar = $numeroDoProntuarioFamiliar
        ")->fetch();
        return $sql;
    }

    public function recuperaNumeroTotalDeMembros($numeroDoProntuarioFamiliar){
        $numeroDoProntuarioFamiliar = $numeroDoProntuarioFamiliar;
        $sql = $this->getDefaultAdapter()->query("
            SELECT COUNT(tcf_numero_prontuario_familiar) FROM tb_composicao_familiar 
                WHERE tcf_numero_prontuario_familiar = $numeroDoProntuarioFamiliar
        ")->fetch();
        return $sql;
    }

    public function excluirMembrosDaFamilia($tcf_prontuario_familiar){
        $prontuarioFamiliar = $tcf_prontuario_familiar;
        $sql = $this->getDefaultAdapter()->query(" DELETE FROM tb_composicao_familiar
            WHERE tcf_numero_prontuario_familiar = $prontuarioFamiliar ")->fetch();
        return $sql;
    }

    public function getIntegrantes($recebe_prontuario_familiar){
        $recebeProntuario = $recebe_prontuario_familiar;

        $sql = $this->getDefaultAdapter()->query("
        SELECT  tcomf_renda_mensal_usuario , usu.usu_nome ,tbcad.tcf_prontuario_familiar , tgp_descricao, tbcomf.usu_codigo,
            tbcomf.tcomf_responsavel
            FROM tb_composicao_familiar AS tbcomf
            INNER JOIN usuario AS usu
                ON usu.usu_codigo = tbcomf.usu_codigo
                
            INNER JOIN tb_cadastro_familiar AS tbcad
                ON tbcad.tcf_prontuario_familiar = tbcomf.tcf_numero_prontuario_familiar
                
            INNER JOIN tb_grau_parentesco AS tbgrau
                ON tbgrau.tgp_codigo = tbcomf.tgp_grau_parentesco

            WHERE tcf_numero_prontuario_familiar = $recebeProntuario

            ")->fetchAll();

        return $sql;
    }

    public function recuperaRendaIndividual($recebeIdIntegrante){
        $recebeIdIntegrante = $recebeIdIntegrante;
        $sql = $this->getDefaultAdapter()->query("
            SELECT tcomf_renda_mensal_usuario FROM tb_composicao_familiar WHERE usu_codigo = $recebeIdIntegrante
            
        ")->fetch();

        return $sql;
    }

    public function buscar($recebeProntuarioFamiliar){
        $recebeProntuarioFamiliar = $recebeProntuarioFamiliar;

        $sql = $this->getDefaultAdapter()->query(
            "SELECT usu.usu_nome, tcomf_responsavel, tgp_grau_parentesco, tbcomf.usu_codigo, tcomf_renda_mensal_usuario, tcf_numero_prontuario_familiar, tgp_descricao
                FROM tb_composicao_familiar AS tbcomf
                INNER JOIN usuario AS usu
                    ON usu.usu_codigo = tbcomf.usu_codigo

                INNER JOIN tb_grau_parentesco as tgp
                    ON tgp.tgp_codigo = tbcomf.tgp_grau_parentesco

                WHERE tcf_numero_prontuario_familiar = $recebeProntuarioFamiliar"
        )->fetchAll();

        return $sql;
    }

}
