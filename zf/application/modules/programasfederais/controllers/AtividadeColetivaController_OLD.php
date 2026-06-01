<?php

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

    public function carregaDadosForm() {
        $tbTpa = new Application_Model_TbCdsTipoAtivCol();
        $tbTema = new Application_Model_TbCdsAtivColTema();
        $tbPubAlvo = new Application_Model_TbCdsAtivColPublicoAlvo();
        $tbPrat = new Application_Model_TbCdsAtivColPratica();
        $tbUni = new Application_Model_Unidade();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->atividades = $tbTpa->getDadosTipoAtividade();
        $this->view->temas = $tbTema->getDadosTema();
        $this->view->pubAlvo = $tbPubAlvo->getDados();
        $this->view->praticas = $tbPrat->getDados();
        //echo "<pre>".print_r($this->view->praticas,1);die();
        $this->view->unidade = $tbUni->fetchAll("cnes_ativo = 'A'");
        $this->view->logon = $tbUsr->getUsrAtual();
    }

   public function salvarAction() {
        // die("bateu aqui");
        $dados = array(
            "uni_codigo" => $this->_request->getPost("uni_codigo"),
            "usr_codigo" => $this->_request->getPost("prof_resp_codigo"),
            "tp_cds_ativ_col" => $this->trataValor($this->_request->getPost("atividade")),
            "dt_ativ_col" => $this->trataValor($this->_request->getPost("dt_atividade")),
            "hr_inicio" => $this->trataValor($this->_request->getPost("dt_atividade")." ".$this->_request->getPost("hr_inicio")),
            "hr_fim" => $this->trataValor($this->_request->getPost("dt_atividade")." ".$this->_request->getPost("hr_fim")),
            "co_inep_escola" => $this->trataValor($this->_request->getPost("num_inep")),
            "qt_participante_programado" => $this->trataValor($this->_request->getPost("num_participantes")),
            "ds_local_ativ" => $this->trataValor($this->_request->getPost("ds_local")),
            "st_envio" => 0,
            "tp_cds_origem" => 1,
            "co_unico_ficha" => $this->getGUID(),
            "turno" => $this->_request->getPost("turno", NULL),
            "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
            "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
            "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
            "qt_avaliacao_alterada" => $this->trataValor($this->_request->getPost("num_aval")),
            "qt_participante_ativ" => $this->trataValor($this->_request->getPost("num_particip")),
            "cod_equipe_ine" => $this->trataValor($this->_request->getPost("cod_equipe")),
            "cod_cnes_unidade" => $this->trataValor($this->_request->getPost("cod_cnes_uni")),
            "co_localidade_origem" => 9640,
            "st_enfileirado" => 0
        );
        //echo "<pre>".print_r($dados,1);die();
        // Valida Edição
        if ($this->_request->getPost("codFicha")) {
            $dados["co_cds_ficha_ativ_col"] = $this->_request->getPost("codFicha");
        }
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            $tbFic = new Application_Model_TbCdsFichaAtivCol();
            $codFicha = $tbFic->salvar($dados);
            // Salvando temas e atualizando
            $temas = $this->_request->getPost("temas");
            if ($temas) { 
                $this->salvarTemasAction($temas,$codFicha); 
            }else{
                $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
                $tbRlTema->excluir($codFicha);
            }
            // Salvando público alvo
            $pubAlvo = $this->_request->getPost("pubAlvo");
            if ($pubAlvo) { 
                $this->salvarPublicoAction($pubAlvo,$codFicha); 
            }else{
               $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
               $tbRlPub->excluir($codFicha); 
            }
            // Salvando práticas
            $praticas = $this->_request->getPost("praticas");
            if ($praticas) { 
                $this->salvarPraticasAction($praticas,$codFicha); 
            }else{
                $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
                $tbRlPrat->excluir($codFicha);
            }
            $ususPart = $this->_request->getPost("usus_part");
            if ($ususPart) { $this->salvarParticipantesAction($ususPart,$codFicha); }
            $profsPart = $this->_request->getPost("profs_part");
            //echo "<pre>".print_r($profsPart,1);die();
            if ($profsPart) { $this->salvarResponsaveisAction($profsPart,$codFicha); }
            Zend_Db_Table::getDefaultAdapter()->commit();
            $this->carregaDadosForm();
            $this->view->dialog = array("Confirmação","Dados salvo com sucesso!",300,140);
            return $this->_redirect("/programasfederais/atividade-coletiva/index");
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

    public function salvarPraticasAction($praticas=FALSE,$codFicha=FALSE) {
        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        $tbRlPrat->excluir($codFicha);
        foreach ($praticas as $value) {
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

    public function salvarParticipantesAction($ususPart=FALSE,$codFicha=FALSE) {
        $tbAtivCol = new Application_Model_TbCdsAtivColParticipante();
        $tbAtivCol->excluir($codFicha);
        foreach ($ususPart as $key => $value) {
            $dados = "";
            $dados = array(
                "usu_codigo" => $value["usu_codigo"],
                "dt_nascimento" => $value["dt_nascimento"],
                "st_avaliacao_alterada" => $value["st_avaliacao_alterada"],
                "nu_peso" => ($value["nu_peso"] == "" ? 0 :  str_replace(",",".",$value["nu_peso"])),
                "nu_altura" => ($value["nu_altura"] == "" ? 0 : $value["nu_altura"]),
                "st_cessou_habito_fumar" => $value["st_cessou_habito_fumar"],
                "st_abandonou_grupo" => $value["st_abandonou_grupo"],
                "co_cds_ficha_ativ_col" => $codFicha
            );

            try{
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
