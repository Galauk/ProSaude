<?php

class Agendamento_CentralDeRegulacaoController extends Zend_Controller_Action {
    
    public function init(){
        $this->_helper->acl->allow(NULL);
        $this->view->title = "Centro de Regulação";
    }

    public function indexAction(){
        $this->_helper->layout->setLayout("simples");
        
        $tbEncExt = new Application_Model_EncaminhamentoExterno();
        $tbUsu = new Application_Model_Usuarios();
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $tipoAgendamentoExterno = new Application_Model_AgendamentoExterno();

        $usrAtual = $tbUsu->getUsrAtual();
        $encaminhamentos = $tbEncExt->recuperarTodosOsEncaminhamentos();
        $agendamentosExternos = $tipoAgendamentoExterno->recuperarTodosOsAgendamenosExternos();
        $recuperaDadosCentroDeRegulacao = $centroDeRegulacao->recuperaDadosCentroDeRegulacao($usrAtual->usr_codigo);
        $this->view->encaminhamentos = $encaminhamentos;
        $this->view->agendamentosExternos = $agendamentosExternos;
        $this->view->usrAtual = $usrAtual;
        $this->view->recuperaDadosCentroDeRegulacao = $recuperaDadosCentroDeRegulacao;
    }

    public function listaDeAgendamentosAction(){
        $tbUsu = new Application_Model_Usuarios();
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $tipoAgendamentoExterno = new Application_Model_AgendamentoExterno();

        $usrAtual = $tbUsu->getUsrAtual();

        if ($_SESSION[logon][usr]->usr_tipo_medico == "R"){

            $agendamentosExternos = $tipoAgendamentoExterno->recuperarTodosOsAgendamenosExternosRegulador($usrAtual->uni_codigo);
            $this->view->agendamentosExternos = $agendamentosExternos;
            $this->view->usrAtual = $usrAtual;
        } else{
            
            $agendamentosExternos = $tipoAgendamentoExterno->recuperarTodosOsAgendamenosExternos($usrAtual->uni_codigo);
            $this->view->agendamentosExternos = $agendamentosExternos;
            $this->view->usrAtual = $usrAtual;
        }
        $recuperaDadosCentroDeRegulacao = $centroDeRegulacao->recuperaDadosCentroDeRegulacao($usrAtual->usr_codigo);
        $this->view->recuperaDadosCentroDeRegulacao = $recuperaDadosCentroDeRegulacao;
    }

    public function listaDeEncaminhamentosAction(){
        $tbEncExt = new Application_Model_EncaminhamentoExterno();
        $tbUsu = new Application_Model_Usuarios();
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $usrAtual = $tbUsu->getUsrAtual();
        $encaminhamentos = $tbEncExt->recuperarTodosOsEncaminhamentos();
        $recuperaDadosCentroDeRegulacao = $centroDeRegulacao->recuperaDadosCentroDeRegulacao($usrAtual->usr_codigo);
        $this->view->encaminhamentos = $encaminhamentos;
        $this->view->usrAtual = $usrAtual;
        $this->view->recuperaDadosCentroDeRegulacao = $recuperaDadosCentroDeRegulacao;
    }

    public function salvarAction(){
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $tipoEncaminhamento = 1;
        $tipoAgendamento = 2;

        for ($count=0; $count < count($_FILES); $count++) { 
            
            if ($_FILES[arquivoSolicitacao.$count]['name'] != "") {        

                if (!empty($_POST[codigoAgendamentoExterno.$count])) {
                    $recebeCaminhoAbsoluto = $_SERVER['DOCUMENT_ROOT'];

                    $destino = $recebeCaminhoAbsoluto.'/WebSocialUpload/centroRegulador/'.$_FILES[arquivoSolicitacao.$count]['name'];

                    $arquivo_tmp = $_FILES[arquivoSolicitacao.$count]['tmp_name'];
                     
                    if(!move_uploaded_file($arquivo_tmp, $destino)){
                        throw new Exception("Error Processing Request", 1);
                    }

                    $salvarCentroDeRegulacao = $centroDeRegulacao->salvar($_FILES[arquivoSolicitacao.$count]['name'], $_POST[codigoAgendamentoExterno.$count], $_POST[entregue.$count], $tipoAgendamento);      

                    return $this->_redirect("agendamento/central-de-regulacao?alert=success");        

                } else{

                    $recebeCaminhoAbsoluto = $_SERVER['DOCUMENT_ROOT'];

                    $destino = $recebeCaminhoAbsoluto.'/WebSocialUpload/centroRegulador/'.$_FILES[arquivoSolicitacao.$count]['name'];

                    $arquivo_tmp = $_FILES[arquivoSolicitacao.$count]['tmp_name'];
                     
                    if(!move_uploaded_file($arquivo_tmp, $destino)){
                        throw new Exception("Error Processing Request", 1);
                    }
                    $salvarCentroDeRegulacao = $centroDeRegulacao->salvar($_FILES[arquivoSolicitacao.$count]['name'], $_POST[codigoEncaminhamentoExterno.$count], $_POST[entregue.$count], $tipoEncaminhamento);

                    return $this->_redirect("agendamento/central-de-regulacao?alert=success");        
                }
                    return $this->_redirect("agendamento/central-de-regulacao?alert=success");       
            }
        }

    }

    public function buscarNomeEncaminhamentoAction(){
        session_start();
        Zend_Layout::getMvcInstance()->disableLayout();    
        $idCentroRegulador = $this->_request->getParam("idCentroRegulador");
        $diretorios = array("thumbnail","small");   
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $tbUpl = new Application_Model_UploadArquivo();

        $recebeNomeImagem = $centroDeRegulacao->recebeNomeImagem($idCentroRegulador);

        if (empty($recebeNomeImagem)) {
            echo 0;
            exit();
        } else{
            $resultado = $tbUpl->geraThumbsNovo($recebeNomeImagem[0][cr_imagem],100,67,"thumbnail");
            echo $resultado;
            exit();
        }

    }

    public function buscarNomeEncaminhamentoAgendamentoAction(){
        session_start();
        Zend_Layout::getMvcInstance()->disableLayout();    
        $idCentroRegulador = $this->_request->getParam("idCentroRegulador");
        $diretorios = array("thumbnail","small");   
        $centroDeRegulacao = new Application_Model_CentroDeRegulacao();
        $tbUpl = new Application_Model_UploadArquivo();

        $recebeNomeImagem = $centroDeRegulacao->recebeNomeImagemAgendamento($idCentroRegulador);

        if (empty($recebeNomeImagem)) {
            echo 0;
            exit();
        } else{
            $resultado = $tbUpl->geraThumbsNovo($recebeNomeImagem[0][cr_imagem],100,67,"thumbnail");
            echo $resultado;
            exit();
        }

    }

}

