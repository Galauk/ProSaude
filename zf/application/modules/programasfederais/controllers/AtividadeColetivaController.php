<?php

// error_reporting(E_ERROR);

class ProgramasFederais_AtividadeColetivaController extends Zend_Controller_Action {

    public function init() {
        $this->view->title = "Atividade Coletiva";
        $this->_helper->acl->allow(NULL);
    }


    public function indexAction() {
        $tbAtiv = new Application_Model_TbCdsFichaAtivCol();
        $this->view->dados = $tbAtiv->getDados();
    }

    public function buscarAction(){
        $busca = $this->_request->getPost("busca");
        $tipoBusca = $this->_request->getPost("tipo_busca");
        $tbAtiv = new Application_Model_TbCdsFichaAtivCol();
        $this->view->dados = $tbAtiv->busca($busca,$tipoBusca);
        return $this->render("index");
    }

    public function formAction() {
        $this->carregaDadosForm();
        // Pega dados edição
        $codFicha = $this->_request->getParam("id");
        if ($codFicha) {
            $tbFic = new Application_Model_TbCdsFichaAtivCol();
            $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
            $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
            $tbRlProf = new Application_Model_RlCdsFichaAtivColProf();
            $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
            $tbParts = new Application_Model_TbCdsAtivColParticipante();


            $this->view->dados = $tbFic->getDadosPorId($codFicha);
            $this->view->temasEdit = $tbRlTema->getDadosPorId($codFicha)->toArray();
            $this->view->pubAlvoEdit = $tbRlPub->getDadosPorId($codFicha)->toArray();
            $this->view->praticasEdit = $tbRlPrat->getDadosPorId($codFicha)->toArray();
            $this->view->profsEdit = $tbRlProf->getDadosPorId($codFicha);
            $this->view->partsEdit = $tbParts->getDadosPorId($codFicha);
        }
    }

    public function editarAction(){
        $idAtividadeColetiva = $this->_request->getParam("id");
        $this->carregaDadosForm();

        $tbFic = new Application_Model_TbCdsFichaAtivCol();
        $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
        $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
        $tbRlProf = new Application_Model_RlCdsFichaAtivColProf();
        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        $tbParts = new Application_Model_TbCdsAtivColParticipante();

        // die("aqui ó");
        $this->view->dados = $tbFic->getDadosPorId($idAtividadeColetiva);
        $this->view->temasEdit = $tbRlTema->getDadosPorId($idAtividadeColetiva)->toArray();
        $this->view->pubAlvoEdit = $tbRlPub->getDadosPorId($idAtividadeColetiva)->toArray();
        $this->view->praticasEdit = $tbRlPrat->getDadosPorId($idAtividadeColetiva)->toArray();
        $this->view->profsEdit = $tbRlProf->getDadosPorId($idAtividadeColetiva);
        $this->view->partsEdit = $tbParts->getDadosPorId($idAtividadeColetiva);

    }
    public function carregaDadosForm() {
        $tbTpa = new Application_Model_TbCdsTipoAtivCol();
        $tbTema = new Application_Model_TbCdsAtivColTema();
        $tbPubAlvo = new Application_Model_TbCdsAtivColPublicoAlvo();
        $tbPrat = new Application_Model_TbCdsAtivColPratica();
        $tbUni = new Application_Model_Unidade();
        $tbUsr = new Application_Model_Usuarios();
        $tbProc = new Application_Model_Procedimento();


        $this->view->outrosProcedimentosColetivos = $tbProc->buscaOutrosProcedimentosColetivos();

        $this->view->atividades = $tbTpa->getDadosTipoAtividade();
        $this->view->temas = $tbTema->getDadosTema();
        $this->view->pubAlvo = $tbPubAlvo->getDados();

        $recebePraticas = $tbPrat->getDadosPraticas();
        $this->view->praticas = $tbPrat->getDadosPraticas();
        $this->view->temasSaude = $tbPrat->getDadosTemas();

        //echo "<pre>".print_r($this->view->praticas,1);die();
        $this->view->unidade = $tbUni->fetchAll("cnes_ativo = 'A'");
        $this->view->logon = $tbUsr->getUsrAtual();
    }

    public function salvarAction() {
        $dados = array(
            "uni_codigo" => $this->_request->getPost("uni_codigo"),
            "usr_codigo" => $this->_request->getPost("prof_resp_codigo"),
            "observacoes_atividade_coletiva" => $this->_request->getPost("observacoes_atividade_coletiva"),
            "outro_procedimento_coletivo" => $this->_request->getPost("outro_procedimento_coletivo"),
            "tp_cds_ativ_col" => $this->trataValor($this->_request->getPost("atividades")),
            "dt_ativ_col" => $this->trataValor($this->_request->getPost("dt_atividade")),
            "hr_inicio" => $this->trataValor($this->_request->getPost("dt_atividade")." ".$this->_request->getPost("hr_inicio")),
            "hr_fim" => $this->trataValor($this->_request->getPost("dt_atividade")." ".$this->_request->getPost("hr_fim")),
            "co_inep_escola" => $this->trataValor($this->_request->getPost("num_inep")),
            "qt_participante_programado" => $this->trataValor($this->_request->getPost("num_participantes")),
            "qt_participante_ativ" => $this->trataValor($this->_request->getPost("num_particip")),
            "ds_local_ativ" => $this->trataValor($this->_request->getPost("ds_local")),
            "st_envio" => 0,
            "tp_cds_origem" => 1,
            "co_unico_ficha" => $this->getGUID(),
            "turno" => $this->_request->getPost("turno", NULL),
            "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
            "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
            "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
            "qt_avaliacao_alterada" => $this->trataValor($this->_request->getPost("num_aval")),
            "cod_equipe_ine" => $this->trataValor($this->_request->getPost("cod_equipe")),
            "cod_cnes_unidade" => $this->trataValor($this->_request->getPost("cod_cnes_uni")),
            "pse_educacao" => $this->_request->getPost("saudeEduacao") == "educacao" ? "t" : "f",
            "pse_saude" => $this->_request->getPost("saudeEduacao") == "saude" ? "t" : "f",
            "co_localidade_origem" => 9640,
            "st_enfileirado" => 0
        );

        // echo "<pre>";print_r($dados);die();
        // Valida Edição
        if ($this->_request->getPost("codFicha")) {
            $dados["co_cds_ficha_ativ_col"] = $this->_request->getPost("codFicha");
        }
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            $temasParaSaude = $this->_request->getPost("temasSaude");
            $praticasParaSaude = $this->_request->getPost("praticas");
            
            $tbFic = new Application_Model_TbCdsFichaAtivCol();
            $codFicha = $tbFic->salvar($dados);
        
            
            $temas = $this->_request->getPost("temasParaReuniao");

            if ($temas) { 
                $this->salvarTemasAction($temas,$codFicha); 
            }else{
                $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
                $tbRlTema->excluir($codFicha);
            }
            // Salvando público alvo
            $pubAlvo = $this->_request->getPost("publicoAlvo");
            if ($pubAlvo) { 
                $this->salvarPublicoAction($pubAlvo,$codFicha); 
            }else{
               $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
               $tbRlPub->excluir($codFicha); 
            }

            if ($praticasParaSaude) { 
                $this->salvarPraticasAction($praticasParaSaude,$codFicha); 

            }else{
                $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
                $tbRlPrat->excluir($codFicha);
            }
            
            if ($temasParaSaude) { 
                $this->salvarPraticasDoisAction($temasParaSaude,$codFicha);
                // essa da problema
            }else{
                $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
                $tbRlPrat->excluir($codFicha);
            }

            $ususPart = $this->_request->getPost("usus_part");

            if ($ususPart) {
                $this->salvarParticipantesAction($ususPart,$codFicha); 
                // die("aqui");
            }
            
            $profsPart = $this->_request->getPost("profs_part");

            //echo "<pre>".print_r($profsPart,1);die();
            
            if ($profsPart) { 
                $this->salvarResponsaveisAction($profsPart,$codFicha); 
            }

            Zend_Db_Table::getDefaultAdapter()->commit();

            $this->carregaDadosForm();
            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
            return $this->_redirect("/programasfederais/atividade-coletiva/index?alert=success");
        } catch (Exception $ex) {
            //die($ex->getMessage());
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->carregaDadosForm();
            $this->view->erro = $ex->getMessage();
            return $this->render("form");
        }
    }
    
    public function trataValor($valor){
        foreach ((array)$valor as $value){
            $valorFinal = ($value != "" ? $value : NULL);
            return $valorFinal;
        }
    }

    public function salvarTemasAction($temas=FALSE,$codFicha=FALSE) {
        // echo "<pre>";print_r($temas);die();
        $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
        $tbRlTema->excluir($codFicha);
        foreach ($temas as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_tema" => $value
            );
            
            try{
                $tbRlTema->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function salvarPublicoAction($pubAlvo=FALSE,$codFicha=FALSE) {
        $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
        $tbRlPub->excluir($codFicha);
        foreach ($pubAlvo as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_publico_alvo" => $value
            );
            try{
                $tbRlPub->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function salvarPraticasAction($praticasParaSaude=FALSE,$codFicha=FALSE) {
        
        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        $tbRlPrat->excluir($codFicha);
        // echo "<pre>";print_r($praticasParaSaude);die();
        foreach ($praticasParaSaude as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_pratica" => $value
            );
            
            try{
                $tbRlPrat->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function salvarPraticasDoisAction($temasParaSaude=FALSE,$codFicha=FALSE) {

        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        // $tbRlPrat->excluir($codFicha);
        foreach ($temasParaSaude as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_pratica" => $value
            );
            // echo "<pre>";print_r($dados);die();
            try{
                $tbRlPrat->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function salvarParticipantesAction($ususPart=FALSE,$codFicha=FALSE) {
        $tbAtivCol = new Application_Model_TbCdsAtivColParticipante();
        $tbAte = new Application_Model_Atendimento();

        $tbAtivCol->excluir($codFicha);
        foreach ($ususPart as $key => $value) {
            $dados = "";
            $dados = array(
                "usu_codigo" => $value["usu_codigo"],
                "dt_nascimento" => $value["dt_nascimento"],
                "st_avaliacao_alterada" => $value["st_avaliacao_alterada"] == 'SIM' ? 1 : 0,
                "nu_peso" => ($value["nu_peso"] == "" ? 0 :  str_replace(",",".",$value["nu_peso"])),
                "nu_altura" => ($value["nu_altura"] == "" ? 0 : $value["nu_altura"]),
                "st_cessou_habito_fumar" => $value["st_cessou_habito_fumar"] == 'SIM' ? 1 : 0,
                "st_abandonou_grupo" => $value["st_abandonou_grupo"] == 'SIM' ? 1 : 0,
                "co_cds_ficha_ativ_col" => $codFicha
            );

            $dadosUsu = array(
                "usu_codigo" => $dados["usu_codigo"],
                "ate_peso" => $dados["nu_peso"],
                "ate_altura" => $dados["nu_altura"]
            );

            try{
                $tbAte->atualizaPesoAltura($dadosUsu);
                $tbAtivCol->salvar($dados);

            } catch (Exception $ex) {
               die($ex->getMessage());
                return $ex->getMessage();
            }

        }
    }

    public function salvarResponsaveisAction($profsPart=FALSE,$codFicha=FALSE) {
        $tbAtiProfs = new Application_Model_RlCdsFichaAtivColProf();
        $tbAtiProfs->excluir($codFicha);
        foreach ($profsPart as $key => $value) {
            $dados = "";
            $dados = array(
                "usr_codigo" => $value["usr_codigo"],
                "cbo" => $value["cbo"],
                "co_cds_ficha_ativ_col" => $codFicha
            );

            try{
                $tbAtiProfs->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    private function getGUID() {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = md5(uniqid(rand(), true));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12);
            return $uuid;
        }
    }

    public function inconsistenciasAction() {
        $this->view->title = "E-SUS Inconsistências Atividade Coletiva";
        $uuid = $this->_request->getPost("uuid");
        if ($uuid){
            $tbEsusAc = new Application_Model_EsusAtividadeColetiva();
            $this->view->dados = $tbEsusAc->getDadosPorUuid($uuid);
            // $resultado = $tbEsusAc->getDadosPorUuid($uuid);
            // echo "<pre>";print_r($resultado);die();
        }
    }

}

?>
