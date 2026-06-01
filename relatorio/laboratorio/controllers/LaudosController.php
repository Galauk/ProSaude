<?php

class Laboratorio_LaudosController extends Zend_Controller_Action {
    
    public function init(){
        $this->_helper->acl->allow(NULL,array('imprimir'));
    }
    
    public function indexAction(){
        //$itens = 

    }
    
    public function imprimirAction(){
        $this->_helper->layout->setLayout("simples");
        $proc_codigos = $this->_request->getParam("proc_codigos",FALSE);
        $age_codigo = $this->_request->getParam("age_codigo",FALSE);
        $sql_usr_codigo = $this->_request->getParam("sql_usr_codigo",FALSE);
        $tbCatExa = new Application_Model_CategoriaDeExames();
        $tbConv = new Application_Model_Convenio();
        $tbSec = new Application_Model_Secretaria();
        $tbAge = new Application_Model_Agenda();
        $tbConf = new Application_Model_Configuracao();
        $tbUsr = new Application_Model_Usuarios();
        $tbAgei = new Application_Model_AgendaItens();
        $this->view->bioquimicos_resp = $tbAgei->getBioquimicosResponsavelAgendamento($age_codigo);
        $this->view->bioquimicos = $tbUsr->getUsrPorTipo("B")->toArray();
        $this->view->nome_cidade = $tbConf->getConfig("NOME_CIDADE");
        $this->view->dados_pac = $tbAge->getDadosUsuarioPorAgendamento($age_codigo);
        $this->view->secretaria  = $tbSec->getDadosSec();
        $this->view->convenio = $tbConv->getConvenioPorAgendamento($age_codigo);
        $this->view->procsCodigo = $proc_codigos; 
        $this->view->catExames = $tbCatExa->getCategoriaPorProcedimentos($proc_codigos);
        $proc_categoria = $tbCatExa->getProcedimentosPorCategoria($proc_codigos);
        //echo "<pre>".print_r($proc_categoria,1);die();
        $this->view->proc_por_cat =  $this->montaLaudos($proc_categoria,$this->view->dados_pac->usu_codigo,$age_codigo);
        $this->view->sql_usr_codigo = $sql_usr_codigo;

        if($this->_request->getParam('json', FALSE)){
            header("Access-Control-Allow-Origin: *");
            die(json_encode($this->view->proc_por_cat));
        }
        
    }
    
    public function montaLaudos($procedimentos=FALSE,$usu_codigo=FALSE,$age_codigo=FALSE){
        $tbUsu = new Application_Model_Usuario();
        $tbAgei = new Application_Model_AgendaItens();
        $tbRes = new Application_Model_ResultadoExame();
        $tbTma = new Application_Model_TipoDeMaterial();
        $tbTpm = new Application_Model_TipoDeMetodos();
        $tbCol = new Application_Model_Coleta();
        foreach($procedimentos as $procedimento){
            $agei_codigo = $tbAgei->getAgendaItemPorProcedimento($age_codigo, $procedimento->proc_codigo)->agei_codigo;
            $laudos = $tbRes->getResultados($agei_codigo)->toArray();
            $metodo = $tbTpm->getTipoPorProcedimento($procedimento->proc_codigo)->tpm_metodo;
            $material = $tbTma->getTipoPorProcedimento($procedimento->proc_codigo)->tma_tipo;
            $data_coleta = $tbCol->getColeta($agei_codigo)->col_data_coleta;
            $historico = $tbRes->getItensHistorico($procedimento->proc_codigo,$usu_codigo,$age_codigo)->toArray();
            $array_laudos[$procedimento->proc_codigo] = array("cte_codigo"=>$procedimento->cte_codigo,
                                                              "cte_cargo"=>$procedimento->cte_cargo,
                                                              "proc_codigo"=>$procedimento->proc_codigo,
                                                              "proc_nome"=>$procedimento->proc_nome,
                                                              "col_data_coleta"=>$data_coleta,
                                                              "agei_codigo"=>$agei_codigo,
                                                              "metodo"=>$metodo,
                                                              "material"=>$material,
                                                              "laudos"=>$laudos,
                                                              "historico"=>$historico);
        }
        //echo "<pre>".print_r($array_laudos,1);die();
        return $array_laudos;
    }
    
    public function listaResponsaveisLaudosAction(){
        $this->view->title = "Bioquímicos";
        $tbUsr = new Application_Model_Usuarios();
        $tbAgeBr = new Application_Model_AgendaBioquimicosResponsavel();
        $age_codigo = $this->_request->getParam("age_codigo",FALSE);
        $id_login = $this->_request->getParam("id_login",FALSE);
        $validacao = $this->_request->getParam("validacao",FALSE);
        // $bioquimicos = $tbUsr->listaBioquimicos()->toArray(); 
        // $i=0;
        // foreach($bioquimicos as $value) {
        //     $usr_codigo = $value["usr_codigo"];
        //     $agebr_codigo = $tbAgeBr->getBioquimicosResponsavel($age_codigo,$usr_codigo)->agebr_codigo;
        //     $bioquimicos[$i]["agebr_codigo"] = ($agebr_codigo != "" ? $agebr_codigo : NULL);
        //     $i++;
        // }
        $this->view->id_login = $id_login;
        $this->view->age_codigo = $age_codigo;
        $this->view->validacao = $validacao;
    }
    
    public function salvarListaResponsaveisLaudosAction(){
        // $bioquimicos = $this->_request->getPost("bioquimicos",FALSE);
        $id_login = $this->_request->getPost("id_login",FALSE);
        $age_codigo = $this->_request->getPost("age_codigo",FALSE);
        $tbAgeBr = new Application_Model_AgendaBioquimicosResponsavel();
        $tbAgeBr->excluirBioquimicosResponsavel($age_codigo);
        // Remove responsaveis antigos e coloca novos se quiser
        // foreach ($bioquimicos as $value) {
        //     $dados = array(
        //         "age_codigo" => $age_codigo,
        //         "usr_codigo" => $value
        //     );
        //     $tbAgeBr->salvar($dados);
        // }
        $dados = array(
                "age_codigo" => $age_codigo,
                "usr_codigo" => $id_login
            );
            $tbAgeBr->salvar($dados);
        return $this->render("laudos/lista-responsaveis-laudos",NULL,TRUE);
        //$this->view->dialog = array("Confirmação","Importação de dados do CNES realizada com sucesso!",300,140);
        //return $this->render("laudos/lista-responsaveis-laudos",NULL,TRUE);
        //$this->_redirect("laboratorio/laudos/lista-responsaveis-laudos/age_codigo/$age_codigo");
    }
    
    public function confereAssinaturaResponsaveisAction(){
        $age_codigo = $this->_request->getPost("age_codigo",FALSE);
        $tbAgeBr = new Application_Model_AgendaBioquimicosResponsavel();
        if (count($tbAgeBr->getBioquimicosResponsavel($age_codigo)) > 0) {
            $this->view->dados = "assinado";
            return $this->render("dados",NULL,TRUE);
        } else {
            $this->view->dados = "erro";
            return $this->render("dados",NULL,TRUE);
        }
    }
}

