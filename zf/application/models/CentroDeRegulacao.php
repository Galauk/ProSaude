<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_CentroDeRegulacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'centro_de_regulacao';
    protected $_primary = 'cr_codigo';
 
    public function salvar($nomeDaImagem, $idAgendamentoExterno, $entregue, $validaEncaminhamentoAgendamento){
        
        $nomeDaImagem = $nomeDaImagem; 
        $idAgendamentoExterno = intval($idAgendamentoExterno);
        $entregue = $entregue;
        $validaEncaminhamentoAgendamento = $validaEncaminhamentoAgendamento;

        if ($validaEncaminhamentoAgendamento == 1) {
            if (empty($entregue)) {
                $cr_entregue = "f";
            } else{
                $cr_entregue = "t";
            }

            $buscaIdAgendamento = $this->getDefaultAdapter()->query(
                "SELECT * FROM centro_de_regulacao WHERE encaminhamento_externo_codigo = $idAgendamentoExterno"
            )->fetchAll();        

            if (empty($buscaIdAgendamento)) {
                $sql = $this->getDefaultAdapter()->query(
                    "
                        INSERT INTO centro_de_regulacao(encaminhamento_externo_codigo, cr_imagem, cr_entregue) VALUES ($idAgendamentoExterno, '$nomeDaImagem', '$cr_entregue');
                    "
                )->fetchAll();
                return $sql;
                
            } else{
                $sql = $this->getDefaultAdapter()->query(
                    "
                        UPDATE centro_de_regulacao SET encaminhamento_externo_codigo = $idAgendamentoExterno,  cr_imagem = '$nomeDaImagem', cr_entregue = '$cr_entregue' WHERE encaminhamento_externo_codigo =  $idAgendamentoExterno;
                    "
                )->fetchAll();
                return $sql;
            }
        }

        if ($validaEncaminhamentoAgendamento == 2) {
            
            if (empty($entregue)) {
                $cr_entregue = "f";
            } else{
                $cr_entregue = "t";
            }

            $buscaIdAgendamento = $this->getDefaultAdapter()->query(
                "SELECT * FROM centro_de_regulacao WHERE agendamento_externo_codigo = $idAgendamentoExterno"
            )->fetchAll();        

            // echo "<pre>";print_r($buscaIdAgendamento);die();

            if (empty($buscaIdAgendamento)) {

                $sql = $this->getDefaultAdapter()->query(
                    "
                        INSERT INTO centro_de_regulacao(agendamento_externo_codigo, cr_imagem, cr_entregue) VALUES ($idAgendamentoExterno, '$nomeDaImagem', '$cr_entregue');
                    "
                )->fetchAll();
                return $sql;
                
            } else{
                $sql = $this->getDefaultAdapter()->query(
                    "
                        UPDATE centro_de_regulacao SET agendamento_externo_codigo = $idAgendamentoExterno,  cr_imagem = '$nomeDaImagem', cr_entregue = '$cr_entregue' WHERE agendamento_externo_codigo =  $idAgendamentoExterno;
                    "
                )->fetchAll();
                return $sql;
            }
        }

    }   

    public function recuperaDadosCentroDeRegulacao($usrCodigo){
        $usrCodigo = intval($usrCodigo);

        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT * FROM centro_de_regulacao 
            "
        )->fetchAll();

        return $sql;
    }

    public function recebeNomeImagem($idCentroRegulador){
        $idCentroRegulador = intval($idCentroRegulador);

        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT cr_imagem FROM centro_de_regulacao WHERE encaminhamento_externo_codigo = $idCentroRegulador limit 1
            "
        )->fetchAll();
        
        return $sql;

    }

    public function recebeNomeImagemAgendamento($idCentroRegulador){
        $idCentroRegulador = intval($idCentroRegulador);

        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT cr_imagem FROM centro_de_regulacao WHERE agendamento_externo_codigo = $idCentroRegulador limit 1
            "
        )->fetchAll();
        
        return $sql;

    }
}
