<?php
// echo error_reporting(E_ALL);
class Atendimento_AtendimentoSimplificadoController extends Zend_Controller_Action {

    public function indexAction(){
        // error_reporting(E_ALL);
        // print_r(error_get_last());
        $this->_helper->layout->setLayout("simples");
        $tbVisistaMotivo = new Application_Model_TbCdsVisitaDomMotivo();
        $aleitamentoMaterno = new Application_Model_TbAleitamentoMaterno();
        $tbVisistaDesfecho = new Application_Model_TbCdsVisitaDomDesfecho();
        $tbCiap = new Application_Model_TbCiap();
        $tbTa = new Application_Model_TipoAtendimento();

        $this->view->dadosAleitamentoMaterno =  $aleitamentoMaterno->recuperaDadosAleitamentoMaterno();
        $this->view->motivosPt01 = $tbVisistaMotivo->getMotivosParte01();
        $this->view->motivosPt02 = $tbVisistaMotivo->getMotivosParte02();
        $this->view->buscaAtiva = $tbVisistaMotivo->getBuscaAtiva();
        $this->view->acompanhamento = $tbVisistaMotivo->getAcompanhamento();
        $this->view->desfechos = $tbVisistaDesfecho->getDesfecho();
        $this->view->ciap = $tbCiap->getCiaps();
        
        // $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();

        //#96579
        //Recebe o ate_codigo caso for passado e começa a preparar para receber as informações
        $ateCodigo = $this->_request->getParam("ate_codigo");

        $ageCodigo = $this->_request->getParam("age_codigo");
        //Este fica fora para executar o else
        $tbLocal = new Application_Model_TbLocalAtend();

        // die("asdasdsa");
        if($ateCodigo){
            //buscar o atendimento
            $tbAte = new Application_Model_Atendimento();
            $tbUsu = new Application_Model_Usuario();

            $recuperaId = $tbUsu->recuperaId($ateCodigo);

            $atendimento = $tbAte->buscar($ateCodigo);
            $dadosPreNatal = $tbUsu->recuperaDadosDaGestacao($recuperaId[usu_codigo]);
            
            $this->view->dadosPreNatal = $dadosPreNatal;

            $tbAge = new Application_Model_Agendamento();
            $agendamento = $tbAge->getDadosAgendamentoUsuario($ageCodigo);
            $tbProc = new Application_Model_ProcedimentoAtendimento();
            $procedimento = $tbProc->getAtendimentoPorAteCodigo($ateCodigo);
            //local
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento($atendimento->co_local_atend);
            //manda o atendimento para a view adequar o formulario correto
            $this->view->atendimento = $atendimento;
            //busca as ciaps do atendimento, e manda para a view
            $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
            $ciapsDoAtendimento = $rlAteCiap->getCiapAtendimento($ateCodigo);
            $this->view->ciap_selecionados = $ciapsDoAtendimento;
            //conduta do atendimento
            $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
            $condutasDoAtendimento = $tbCond->getDadosPorAtendimento($ateCodigo);
            
            $this->view->condutasDoAtendimento = $condutasDoAtendimento;
            // motivos visita
            $this->view->ate_inter_val = "N";
            if($atendimento->ate_tipo == "V"){
                if($atendimento->ate_inter_data_formatado != null && $atendimento->ate_inter_motivo != null){
                    $this->view->ate_inter_val = 'S';
                    $this->view->ate_inter_data_formatado = $atendimento->ate_inter_data_formatado;
                    $this->view->ate_inter_motivo = $atendimento->ate_inter_motivo;
                }
                $tbVisita = new Application_Model_TbCdsVisitaDomiciliar();
                $visita = $tbVisita->getVisitaDoAtendimento($ateCodigo);
                $rlVisitaMotivo = new Application_Model_RlCdsVisitaDomMotivo();
                $motivosDoAte = $rlVisitaMotivo->getMotivosDoAtendimento($visita->co_seq_cds_visita_domiciliar);
                $this->view->motivosDoAte = $motivosDoAte;
                $this->view->visita = $visita;
            }

            $this->view->agendamento = $agendamento;
            //#109388 CORREÇÃO BUG
            if(count($procedimento) > 0){
                $this->view->procedimento = $procedimento;
            }
            //#109388 CORREÇÃO BUG
        }else{
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        $tipo_atendimento = strtoupper($this->_request->getParam("tipo"));
        if(in_array($tipo_atendimento, ['A', 'V', 'P'])){
            $this->view->tipo_atendimento = $tipo_atendimento;
        } else {
            //caso não for uma edição e nao for passado o parametro na requisicao, o tipo do atendimento recebe A(atendimento individual) como padrão
            $this->view->tipo_atendimento = "A";
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        if(($this->_request->getParam("uni_codigo")) != null){
            $unid = new Application_Model_Unidade();
            $unidade = $unid->getUnidade($this->_request->getParam("uni_codigo"));
            $this->view->uni_desc =  $unidade['0']->uni_desc;
            $this->view->uni_codigo = $this->_request->getParam("uni_codigo");
        }
        if(($this->_request->getParam("usr_codigo")) != null){
            $usr = new Application_Model_Usuarios();
            $usrInfo = $usr->getInfoUsr($this->_request->getParam("usr_codigo"));
            $this->view->usr_nome = $usrInfo->usr_nome;
            $this->view->usr_codigo = $this->_request->getParam("usr_codigo");
        }
        //#96579
    }
    
    public function indexVisitaDomiciliarAction(){
        // $this->_helper->layout->disableLayout();
        $this->_helper->layout->setLayout("simples");

        $tbVisistaMotivo = new Application_Model_TbCdsVisitaDomMotivo();
        $aleitamentoMaterno = new Application_Model_TbAleitamentoMaterno();
        $tbVisistaDesfecho = new Application_Model_TbCdsVisitaDomDesfecho();
        $tbCiap = new Application_Model_TbCiap();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbTipoDom = new Application_Model_TbTipoDomicilio();



        $this->view->tipodom = $tbTipoDom->getDescricao();
        $this->view->dadosAleitamentoMaterno =  $aleitamentoMaterno->recuperaDadosAleitamentoMaterno();
        $this->view->motivosPt01 = $tbVisistaMotivo->getMotivosParte01();
        $this->view->motivosPt02 = $tbVisistaMotivo->getMotivosParte02();
        $this->view->buscaAtiva = $tbVisistaMotivo->getBuscaAtiva();
        $this->view->acompanhamento = $tbVisistaMotivo->getAcompanhamento();
        $this->view->desfechos = $tbVisistaDesfecho->getDesfecho();
        $this->view->ciap = $tbCiap->getCiaps();
        
        // $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();

        //#96579
        //Recebe o ate_codigo caso for passado e começa a preparar para receber as informações
        $ateCodigo = $this->_request->getParam("ate_codigo");

        $ageCodigo = $this->_request->getParam("age_codigo");
        //Este fica fora para executar o else
        $tbLocal = new Application_Model_TbLocalAtend();

        if($ateCodigo){
            //buscar o atendimento
            $tbAte = new Application_Model_Atendimento();
            $tbUsu = new Application_Model_Usuario();

            $recuperaId = $tbUsu->recuperaId($ateCodigo);

            $atendimento = $tbAte->buscar($ateCodigo);
            $dadosPreNatal = $tbUsu->recuperaDadosDaGestacao($recuperaId[usu_codigo]);
            
            $this->view->dadosPreNatal = $dadosPreNatal;

            $tbAge = new Application_Model_Agendamento();
            $agendamento = $tbAge->getDadosAgendamentoUsuario($ageCodigo);
            $tbProc = new Application_Model_ProcedimentoAtendimento();
            $procedimento = $tbProc->getAtendimentoPorAteCodigo($ateCodigo);
            //local
            $this->view->selectLocais = $tbLocal->selectTag($atendimento->co_local_atend);
            //manda o atendimento para a view adequar o formulario correto
            $this->view->atendimento = $atendimento;
            //busca as ciaps do atendimento, e manda para a view
            $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
            $ciapsDoAtendimento = $rlAteCiap->getCiapAtendimento($ateCodigo);
            $this->view->ciap_selecionados = $ciapsDoAtendimento;
            //conduta do atendimento
            $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
            $motivoDaVisita = $tbCond->getDadosPorVisita($ateCodigo);
            // echo "<pre>";print_r($motivoDaVisita);die();
            $this->view->motivoDaVisita = $motivoDaVisita;
            // motivos visita
            $this->view->ate_inter_val = "N";
            if($atendimento->ate_tipo == "V"){
                if($atendimento->ate_inter_data_formatado != null && $atendimento->ate_inter_motivo != null){
                    $this->view->ate_inter_val = 'S';
                    $this->view->ate_inter_data_formatado = $atendimento->ate_inter_data_formatado;
                    $this->view->ate_inter_motivo = $atendimento->ate_inter_motivo;
                }
                $tbVisita = new Application_Model_TbCdsVisitaDomiciliar();
                $visita = $tbVisita->getVisitaDoAtendimento($ateCodigo);
                $rlVisitaMotivo = new Application_Model_RlCdsVisitaDomMotivo();
                $motivosDoAte = $rlVisitaMotivo->getMotivosDoAtendimento($visita->co_seq_cds_visita_domiciliar);
                $this->view->motivosDoAte = $motivosDoAte;
                $this->view->visita = $visita;
            }

            $this->view->agendamento = $agendamento;
            //#109388 CORREÇÃO BUG
            if(count($procedimento) > 0){
                $this->view->procedimento = $procedimento;
            }
            //#109388 CORREÇÃO BUG
        }else{
            $this->view->selectLocais = $tbLocal->selectTag();
        }
        $tipo_atendimento = strtoupper($this->_request->getParam("tipo"));
        if(in_array($tipo_atendimento, ['A', 'V', 'P'])){
            $this->view->tipo_atendimento = $tipo_atendimento;
        } else {
            //caso não for uma edição e nao for passado o parametro na requisicao, o tipo do atendimento recebe A(atendimento individual) como padrão
            $this->view->tipo_atendimento = "A";
            $this->view->selectLocais = $tbLocal->selectTag();
        }
        if(($this->_request->getParam("uni_codigo")) != null){
            $unid = new Application_Model_Unidade();
            $unidade = $unid->getUnidade($this->_request->getParam("uni_codigo"));
            $this->view->uni_desc =  $unidade['0']->uni_desc;
            $this->view->uni_codigo = $this->_request->getParam("uni_codigo");
        }
        if(($this->_request->getParam("usr_codigo")) != null){
            $usr = new Application_Model_Usuarios();
            $usrInfo = $usr->getInfoUsr($this->_request->getParam("usr_codigo"));
            $this->view->usr_nome = $usrInfo->usr_nome;
            $this->view->usr_codigo = $this->_request->getParam("usr_codigo");
        }
        //#96579
    }

    public function indexProcedimentoAction(){
        // $this->_helper->layout->disableLayout();
        $this->_helper->layout->setLayout("simples");

        $tbVisistaMotivo = new Application_Model_TbCdsVisitaDomMotivo();
        $aleitamentoMaterno = new Application_Model_TbAleitamentoMaterno();
        $tbVisistaDesfecho = new Application_Model_TbCdsVisitaDomDesfecho();
        $tbCiap = new Application_Model_TbCiap();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbProcedimento = new Application_Model_Procedimento();

        $this->view->procedimentosAB = $tbProcedimento->recuperaProcedimentosAB();
        
        $this->view->dadosAleitamentoMaterno =  $aleitamentoMaterno->recuperaDadosAleitamentoMaterno();
        $this->view->motivosPt01 = $tbVisistaMotivo->getMotivosParte01();
        $this->view->motivosPt02 = $tbVisistaMotivo->getMotivosParte02();
        $this->view->buscaAtiva = $tbVisistaMotivo->getBuscaAtiva();
        $this->view->acompanhamento = $tbVisistaMotivo->getAcompanhamento();
        $this->view->desfechos = $tbVisistaDesfecho->getDesfecho();
        $this->view->ciap = $tbCiap->getCiaps();
        
        // $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();

        //#96579
        //Recebe o ate_codigo caso for passado e começa a preparar para receber as informações
        $ateCodigo = $this->_request->getParam("ate_codigo");

        $ageCodigo = $this->_request->getParam("age_codigo");
        //Este fica fora para executar o else
        $tbLocal = new Application_Model_TbLocalAtend();

        if($ateCodigo){
            //buscar o atendimento
            $tbAte = new Application_Model_Atendimento();
            $tbUsu = new Application_Model_Usuario();

            $recuperaId = $tbUsu->recuperaId($ateCodigo);

            $atendimento = $tbAte->buscar($ateCodigo);
            $dadosPreNatal = $tbUsu->recuperaDadosDaGestacao($recuperaId[usu_codigo]);
            
            $this->view->dadosPreNatal = $dadosPreNatal;

            $tbAge = new Application_Model_Agendamento();
            $agendamento = $tbAge->getDadosAgendamentoUsuario($ageCodigo);
            $tbProc = new Application_Model_ProcedimentoAtendimento();
            $procedimento = $tbProc->getAtendimentoPorAteCodigo($ateCodigo);
            //local
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento($atendimento->co_local_atend);
            //manda o atendimento para a view adequar o formulario correto
            $this->view->atendimento = $atendimento;
            //busca as ciaps do atendimento, e manda para a view
            $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
            $ciapsDoAtendimento = $rlAteCiap->getCiapAtendimento($ateCodigo);
            $this->view->ciap_selecionados = $ciapsDoAtendimento;
            //conduta do atendimento
            $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
            $condutasDoAtendimento = $tbCond->getDadosPorAtendimento($ateCodigo);
            $this->view->condutasDoAtendimento = $condutasDoAtendimento;
            // motivos visita
            $this->view->ate_inter_val = "N";
            if($atendimento->ate_tipo == "V"){
                if($atendimento->ate_inter_data_formatado != null && $atendimento->ate_inter_motivo != null){
                    $this->view->ate_inter_val = 'S';
                    $this->view->ate_inter_data_formatado = $atendimento->ate_inter_data_formatado;
                    $this->view->ate_inter_motivo = $atendimento->ate_inter_motivo;
                }
                $tbVisita = new Application_Model_TbCdsVisitaDomiciliar();
                $visita = $tbVisita->getVisitaDoAtendimento($ateCodigo);
                $rlVisitaMotivo = new Application_Model_RlCdsVisitaDomMotivo();
                $motivosDoAte = $rlVisitaMotivo->getMotivosDoAtendimento($visita->co_seq_cds_visita_domiciliar);
                $this->view->motivosDoAte = $motivosDoAte;
                $this->view->visita = $visita;
            }

            $this->view->agendamento = $agendamento;
            //#109388 CORREÇÃO BUG
            if(count($procedimento) > 0){
                $this->view->procedimento = $procedimento;
            }
            //#109388 CORREÇÃO BUG
        }else{
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        $tipo_atendimento = strtoupper($this->_request->getParam("tipo"));
        if(in_array($tipo_atendimento, ['A', 'V', 'P'])){
            $this->view->tipo_atendimento = $tipo_atendimento;
        } else {
            //caso não for uma edição e nao for passado o parametro na requisicao, o tipo do atendimento recebe A(atendimento individual) como padrão
            $this->view->tipo_atendimento = "P";
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        if(($this->_request->getParam("uni_codigo")) != null){
            $unid = new Application_Model_Unidade();
            $unidade = $unid->getUnidade($this->_request->getParam("uni_codigo"));
            $this->view->uni_desc =  $unidade['0']->uni_desc;
            $this->view->uni_codigo = $this->_request->getParam("uni_codigo");
        }
        if(($this->_request->getParam("usr_codigo")) != null){
            $usr = new Application_Model_Usuarios();
            $usrInfo = $usr->getInfoUsr($this->_request->getParam("usr_codigo"));
            $this->view->usr_nome = $usrInfo->usr_nome;
            $this->view->usr_codigo = $this->_request->getParam("usr_codigo");
        }
        //#96579
    }
    
    public function indexBeneficiosConcedidosAction(){
        $this->_helper->layout->setLayout("simples");
        $tbVisistaMotivo = new Application_Model_TbCdsVisitaDomMotivo();
        $aleitamentoMaterno = new Application_Model_TbAleitamentoMaterno();
        $tbVisistaDesfecho = new Application_Model_TbCdsVisitaDomDesfecho();
        $tbCiap = new Application_Model_TbCiap();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbProcedimento = new Application_Model_Procedimento();

        $this->view->procedimentosAB = $tbProcedimento->recuperaProcedimentosAB();
        
        $this->view->dadosAleitamentoMaterno =  $aleitamentoMaterno->recuperaDadosAleitamentoMaterno();
        $this->view->motivosPt01 = $tbVisistaMotivo->getMotivosParte01();
        $this->view->motivosPt02 = $tbVisistaMotivo->getMotivosParte02();
        $this->view->buscaAtiva = $tbVisistaMotivo->getBuscaAtiva();
        $this->view->acompanhamento = $tbVisistaMotivo->getAcompanhamento();
        $this->view->desfechos = $tbVisistaDesfecho->getDesfecho();
        $this->view->ciap = $tbCiap->getCiaps();
        //EDITAR
        // $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();

        //#96579
        //Recebe o ate_codigo caso for passado e começa a preparar para receber as informações
        $ateCodigo = $this->_request->getParam("ate_codigo");

        $ageCodigo = $this->_request->getParam("age_codigo");
        //Este fica fora para executar o else
        $tbLocal = new Application_Model_TbLocalAtend();
        
        if($ateCodigo){
            //buscar o atendimento
            $tbAte = new Application_Model_Atendimento();
            $tbUsu = new Application_Model_Usuario();
            
            $recuperaId = $tbUsu->recuperaId($ateCodigo);
            
            $atendimento = $tbAte->buscar($ateCodigo);
            
            //echo "<pre>";print_r($atendimento);die();
            $dadosPreNatal = $tbUsu->recuperaDadosDaGestacao($recuperaId[usu_codigo]);
            
            $this->view->dadosPreNatal = $dadosPreNatal;

            $tbAge = new Application_Model_Agendamento();
            $agendamento = $tbAge->getDadosAgendamentoUsuario($ageCodigo);
            $tbProc = new Application_Model_ProcedimentoAtendimento();
            $procedimento = $tbProc->getAtendimentoPorAteCodigo($ateCodigo);
            //local
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento($atendimento->co_local_atend);
            //manda o atendimento para a view adequar o formulario correto
            $this->view->atendimento = $atendimento;
            //busca as ciaps do atendimento, e manda para a view
            $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
            $ciapsDoAtendimento = $rlAteCiap->getCiapAtendimento($ateCodigo);
            $this->view->ciap_selecionados = $ciapsDoAtendimento;
            //conduta do atendimento
            $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
            $condutasDoAtendimento = $tbCond->getDadosPorAtendimento($ateCodigo);
            $this->view->condutasDoAtendimento = $condutasDoAtendimento;
            // motivos visita
            $this->view->ate_inter_val = "N";
            if($atendimento->ate_tipo == "V"){
                if($atendimento->ate_inter_data_formatado != null && $atendimento->ate_inter_motivo != null){
                    $this->view->ate_inter_val = 'S';
                    $this->view->ate_inter_data_formatado = $atendimento->ate_inter_data_formatado;
                    $this->view->ate_inter_motivo = $atendimento->ate_inter_motivo;
                }
                $tbVisita = new Application_Model_TbCdsVisitaDomiciliar();
                $visita = $tbVisita->getVisitaDoAtendimento($ateCodigo);
                $rlVisitaMotivo = new Application_Model_RlCdsVisitaDomMotivo();
                $motivosDoAte = $rlVisitaMotivo->getMotivosDoAtendimento($visita->co_seq_cds_visita_domiciliar);
                $this->view->motivosDoAte = $motivosDoAte;
                $this->view->visita = $visita;
            }

            $this->view->agendamento = $agendamento;
            //#109388 CORREÇÃO BUG
            if(count($procedimento) > 0){
                $this->view->procedimento = $procedimento;
            }
            //#109388 CORREÇÃO BUG
        }else{
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        $tipo_atendimento = strtoupper($this->_request->getParam("tipo"));
        if(in_array($tipo_atendimento, ['A', 'V', 'P'])){
            $this->view->tipo_atendimento = $tipo_atendimento;
        } else {
            //caso não for uma edição e nao for passado o parametro na requisicao, o tipo do atendimento recebe A(atendimento individual) como padrão
            $this->view->tipo_atendimento = "B";
            $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        }
        if(($this->_request->getParam("uni_codigo")) != null){
            $unid = new Application_Model_Unidade();
            $unidade = $unid->getUnidade($this->_request->getParam("uni_codigo"));
            $this->view->uni_desc =  $unidade['0']->uni_desc;
            $this->view->uni_codigo = $this->_request->getParam("uni_codigo");
        }
        if(($this->_request->getParam("usr_codigo")) != null){
            $usr = new Application_Model_Usuarios();
            $usrInfo = $usr->getInfoUsr($this->_request->getParam("usr_codigo"));
            $this->view->usr_nome = $usrInfo->usr_nome;
            $this->view->usr_codigo = $this->_request->getParam("usr_codigo");
        }
        //#96579
    }

    public function formAtendimentoSimplificadoAction(){
        $tbConf = new Application_Model_Configuracao();
        $tbLocal = new Application_Model_TbLocalAtend();
        $tbTipoCond = new Application_Model_TbCdsTipoConduta();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbUsr = new Application_Model_Usuarios();

        $tbTema = new Application_Model_TbCdsAtivColTema();
        $tb = new Application_Model_Procedimento();
        $this->view->ciapComum = $tb->getCiapComum('A');

        $this->view->temas = $tbTema->getDadosTema();

        $this->view->cadastro_aise = $tbConf->getConfig("CADASTRO_AISE");
        $this->view->conduta = $tbTipoCond->getDados();
        $this->view->encaminhamentos = $tbTipoCond->getDadosEncaminhamento();
        $this->view->tipo_atendimento01 = $tbTa->getTiposDeAtendimento01();
        $this->view->tipo_atendimento02 = $tbTa->getTiposDeAtendimento02();
        $usr = $tbUsr->getUsrAtual();
        $this->view->usuario = $usr;

        $this->view->unidade_controle = $usr->uni_desc;
        $this->view->uni_codigo_controle = $usr->uni_codigo;
        $this->view->usr_nome_controle = $usr->usr_nome;
        $this->view->usr_codigo_controle = $usr->usr_codigo;

    }

    public function formVisitaDomiciliarAction(){
        // $this->_helper->layout->setLayout("layout");
        // $this->view->title = "Visita domiciliar";
        
        // $this->_helper->layout->setLayout("atendimento-simplificado/form-visita-domiciliar");
        $tbTipoDom = new Application_Model_TbTipoDomicilio();
        $tbConf = new Application_Model_Configuracao();
        $tbTipoCond = new Application_Model_TbCdsTipoConduta();
        $tbUsr = new Application_Model_Usuarios();
        $tbTema = new Application_Model_TbCdsAtivColTema();
        $tbVisistaMotivo = new Application_Model_TbCdsVisitaDomMotivo();
        $tbVisistaDesfecho = new Application_Model_TbCdsVisitaDomDesfecho();
        $tbCiap = new Application_Model_TbCiap();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbLocal = new Application_Model_TbLocalAtend();
        $tb = new Application_Model_Procedimento();
        $this->view->ciapComum = $tb->getCiapComum('V');

        $this->view->temas = $tbTema->getDadosTema();

        $this->view->tipodom = $tbTipoDom->getDescricao();
        $this->view->cadastro_aise = $tbConf->getConfig("CADASTRO_AISE");
        $this->view->conduta = $tbTipoCond->getDados();
        $this->view->encaminhamentos = $tbTipoCond->getDadosEncaminhamento();
        $this->view->motivosPt01 = $tbVisistaMotivo->getMotivosParte01();
        $this->view->motivosPt02 = $tbVisistaMotivo->getMotivosParte02();
        $this->view->buscaAtiva = $tbVisistaMotivo->getBuscaAtiva();
        $this->view->controleAmbiental = $tbVisistaMotivo->getControleAmbiental();
        $this->view->outros = $tbVisistaMotivo->getOutros();

        $tbTipoDom = new Application_Model_TbTipoDomicilio();
        $this->view->tipodom = $tbTipoDom->getDescricao();

        $this->view->acompanhamento = $tbVisistaMotivo->getAcompanhamento();
        $this->view->desfechos = $tbVisistaDesfecho->getDesfecho();
        
        $this->view->ciap = $tbCiap->getCiaps();
        $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();
        $usr = $tbUsr->getUsrAtual();
        $this->view->usuario = $usr;
        $this->view->selectLocais = $tbLocal->selectTag();

        $this->view->unidade_controle = $usr->uni_desc;
        $this->view->uni_codigo_controle = $usr->uni_codigo;
        $this->view->usr_nome_controle = $usr->usr_nome;
        $this->view->usr_codigo_controle = $usr->usr_codigo;

        $this->render('form-visita-domiciliar');
    }

    public function formProcedimentoAction(){
        $this->view->title = "Procedimento";

        $tbLocal = new Application_Model_TbLocalAtend();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbUsr = new Application_Model_Usuarios();
        $tb = new Application_Model_Procedimento();

        $usr = $tbUsr->getUsrAtual();
        $this->view->ciapComum = $tb->getCiapComum('P');

        $this->view->usuario = $usr;
        $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();
        $this->view->unidade_controle = $usr->uni_desc;
        $this->view->uni_codigo_controle = $usr->uni_codigo;
        $this->view->usr_nome_controle = $usr->usr_nome;
        $this->view->usr_codigo_controle = $usr->usr_codigo;

        $this->render('form-procedimento');
    }

    public function formBeneficiosConcedidosAction(){
        $this->view->title = "Beneficíos Concedidos";

        $tbLocal = new Application_Model_TbLocalAtend();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbUsr = new Application_Model_Usuarios();
        $tb = new Application_Model_Procedimento();
        $tipoMedico = new Application_Model_Medico();

        $usr = $tbUsr->getUsrAtual();
        $this->view->ciapComum = $tb->getCiapComum('P');


        $this->view->destino = $tipoMedico->buscarDestino();
        $this->view->usuario = $usr;
        $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();
        $this->view->unidade_controle = $usr->uni_desc;
        $this->view->uni_codigo_controle = $usr->uni_codigo;
        $this->view->usr_nome_controle = $usr->usr_nome;
        $this->view->usr_codigo_controle = $usr->usr_codigo;

        $this->render('form-beneficios-concedidos');
    }

    public function salvarAction(){
        if($this->_request->getParam("ate_codigo")){
            $editar=TRUE;
            $ate_codigo = $this->_request->getParam("ate_codigo");
            $age_codigo = $this->_request->getParam("age_codigo");
        }

        $tbUsuarios = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $tbAte = new Application_Model_Atendimento();
        $tbUsu = new Application_Model_Usuario();
        $tbPreNatal = new Application_Model_AtendimentoPrenatal();

        $tbAteProc = new Application_Model_ProcedimentoAtendimento();
        $dadosUsuarios = $tbUsuarios->getUsrAtual();
        // Salvar Agendamento, por causa da estrutura do BPA
        
        date_default_timezone_set('America/Brasilia');
        $dadosAge = array(
            "age_codigo" => (empty($age_codigo))?0:$age_codigo,
            "age_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "age_horario" => date("H:i"),
            "tat_codigo" => (empty($this->_request->getPost("tat_codigo", FALSE)))?1:$this->_request->getPost("tat_codigo"),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_atendido" => 'A',
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "uni_codigo" => $dadosUsuarios->uni_codigo,
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "usr_codigo_cad" => $dadosUsuarios->usr_codigo,
            "dt_cadastro" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "dt_atualizacao" => "NOW()",
            "age_data_atend" => "NOW()",
            "age_emergencia" => 'N'
            // "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL)
        );

        
        //Dados que podem ser atualizados 
        $dadosUsu = array(
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "usu_esta_gestante" => $this->_request->getPost("usu_esta_gestante", FALSE)
        );
        
        $tbUsu->salvarEstaGestante($dadosUsu);
        
        
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
                if(!$this->_request->getParam("age_codigo")){
                    $ageCodigo = $tbAge->salvarAgendamento($dadosAge);
                }else{
                    $ageCodigo = $this->_request->getParam("age_codigo");
                }

                // Salvar Atendimento
                $dadosAte = array(
                    "ate_codigo" => (empty($ate_codigo))?0:$ate_codigo,
                    "ate_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
                    "ate_hora" => date("H:i"),
                    "ate_reclamacao" => $this->_request->getPost("agee_observacao", FALSE),
                    "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                    "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                    "age_codigo" => $ageCodigo,
                    "ate_valor_proc" => "0.00",
                    "co_equipe" => $this->_request->getPost("cod_equipe", FALSE),
                    "mic_codigo" => (empty($this->_request->getPost("usu_microarea", FALSE)))?0:$this->_request->getPost("usu_microarea", FALSE),
                    "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
                    "tat_codigo" => (empty($this->_request->getPost("tat_codigo", FALSE)))?1:$this->_request->getPost("tat_codigo"),
                    "ate_data_insert" => "NOW()",
                    "ate_simplificado" => TRUE,
                    "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                    "ate_peso" => (empty($this->_request->getPost("ate_peso", FALSE)))?'0.00':$this->_request->getPost("ate_peso", FALSE),
                    "ate_perimetro_cefalico" => (empty($this->_request->getPost("ate_perimetro_cefalico", FALSE)))?'0.00':$this->_request->getPost("ate_perimetro_cefalico", FALSE),
                    "ate_altura" => (empty($this->_request->getPost("ate_altura", FALSE)))?'0.00':$this->_request->getPost("ate_altura", FALSE),
                    "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                    "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
                    "ate_tipo" => ("A"),
                    "turno" => ($this->_request->getPost("turno", false) == '' ? 1 : $this->_request->getPost("turno", false) ),
                    "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL),
                    "ate_tipo_atendimento_paciente" => $this->_request->getPost("ate_tipo_atendimento_paciente", NULL),
                    "ate_conduta_desfecho" => $this->_request->getPost("ate_conduta_desfecho", NULL),
                    "ate_encaminhamento_conduta" => $this->_request->getPost("ate_encaminhamento_conduta", NULL),
                    "vacinacao_em_dia" => $this->_request->getPost("vacinacao_em_dia", NULL),
                    "ate_pres_sist" => ($this->_request->getPost("ate_pres_sist") == '' ? 0 : $this->_request->getPost("ate_pres_sist", false) ), //pressao sistolica
                    "ate_pres_dias" => ($this->_request->getPost("ate_pres_dias") == '' ? 0 : $this->_request->getPost("ate_pres_dias", false) )  //pressao diastolica
                );
                // Salva atendimento
                // echo '<pre>';print_r($dadosAte);die();
                $ateCodigo = $tbAte->salvarAtendimento($dadosAte);
                
                // Salva Dados da visita
                

                // Salva Ciap
                $this->salvaCiap($this->_request->getPost("ciap-selecionados",'F'),$ateCodigo);
                
                // Salva dados conduta
                if ($this->_request->getPost("conduta","F")) {
                    $this->salvarCondutas($this->_request->getPost("conduta", "F"),$ateCodigo);
                }
                // Salvar Procedimentos dos atendimentos

                // Deleta todos os atendimentos do atendimento evitando duplicar procedimento
                $tbAteProc->excluirProcedimentosAtendimento($ateCodigo);
                $dadosProcAte = $this->_request->getPost("procedimento",FALSE);
                $cids = $this->_request->getPost("cid",FALSE);

                $controleCid = 0;

                foreach ($dadosProcAte as $procAte) {
                    $codigoCid = $cids[$controleCid];
                    $controleCid = $controleCid + 1;

                    $dadosProcAte = array(
                        "ate_codigo" => $ateCodigo,
                        "proc_codigo" => $procAte,
                        "usr_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                        "cd10_codigo" => $codigoCid
                    );
                    $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);
                }


                // Salvar exames
                $tbRequisicaoExame = new Application_Model_RequisicaoExame();
                $dadosRequisicaoExames = $this->_request->getPost("exame", FALSE);
                $examesSolicitado = $this->_request->getPost("exame_solicitado", FALSE);
                $examesAvaliado = $this->_request->getPost("exame_avaliado", FALSE);

                $tbRequisicaoExame->excluirAteCodigo($ateCodigo);
                // die("aqui");

                if ($dadosRequisicaoExames != null && $dadosRequisicaoExames != "") {
                    foreach ($dadosRequisicaoExames as $exameAte) {

                        $exaAvaliado = null;
                        $exaSolicitado = null;

                        foreach ($examesSolicitado as $exa) {
                            if (substr($exa, 1, strlen($exa) - 1) == $exameAte) {
                                $exaSolicitado = substr($exa, 0, 1);
                                //$exaAvaliado = null;
                                break;
                            }
                        }

                        foreach ($examesAvaliado as $exa) {
                            if (substr($exa, 1, strlen($exa) - 1) == $exameAte) {
                                $exaAvaliado = substr($exa, 0, 1);
                                //$exaSolicitado = null;
                                break;
                            }
                        }

                        $dadosRequisicaoExames = array(
                            "ate_codigo" => $ateCodigo,
                            "proc_codigo" => $exameAte,
                            "usr_codigo_solicitante" => $this->_request->getPost("usr_codigo", FALSE),
                            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                            "req_observacao" => $this->_request->getPost("agee_observacao", FALSE),
                            "proc_avaliado" => $exaAvaliado,
                            "proc_solicitado" => $exaSolicitado
                        );

                        $tbRequisicaoExame->salvar($dadosRequisicaoExames);
                    }
                }
                // fim salvar exames
                if ($dadosUsu[usu_esta_gestante] == T) {
                    $dum = $this->_request->getPost("dum", FALSE);
                    $data_provavel_parto = date('d-m-Y', strtotime("$dum + 10 month"));

                    $dadosPreNatal = array(
                        "ate_codigo" => $ateCodigo,
                        "dum" => $dum,
                        "gravidez_planejada" => $this->_request->getPost("gravidez_planejada", FALSE),
                        "am_codigo" => $this->_request->getPost("aleitamento_materno", FALSE),
                        "gestas_previas" => $this->_request->getPost("gestas_previas", false) == '' ? 0 : $this->_request->getPost("gestas_previas", false),
                        "partos" => (empty(trim($this->_request->getPost("partos", FALSE))))?0:$this->_request->getPost("partos", FALSE),
                        "tipo_consulta" => $this->_request->getPost("consulta_pre_natal", FALSE),
                        "idade_gestacional" => $this->_request->getPost("idade_gestacional", FALSE),
                        "risco_gestacao" => $this->_request->getPost("risco_gestacao", FALSE),
                        "data_provavel_parto" => $data_provavel_parto

                    );
                    
                $retornoPreNatal = $tbPreNatal->salvar($dadosPreNatal);
            }
            
            Zend_Db_Table::getDefaultAdapter()->commit();

            if($this->_request->getPost('tipoDoAtendimento', FALSE) == "VD"){
                return $this->_redirect("atendimento/atendimento-simplificado/form-visita-domiciliar?alert=success");
            } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "A"){
                return $this->_redirect("/atendimento/atendimento-simplificado/index?alert=success");
            } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "P") {
                return $this->_redirect("atendimento/atendimento-simplificado/form-procedimento?alert=success");
            }
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }

    }

    public function salvarProcedimentoAction(){
        // die("aqui ???");
        if($this->_request->getParam("ate_codigo")){
            $editar=TRUE;
            $ate_codigo = $this->_request->getParam("ate_codigo");
            $age_codigo = $this->_request->getParam("age_codigo");
        }

        $tbUsuarios = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $tbAte = new Application_Model_Atendimento();
        $tbUsu = new Application_Model_Usuario();
        $tbPreNatal = new Application_Model_AtendimentoPrenatal();

        $tbAteProc = new Application_Model_ProcedimentoAtendimento();
        $dadosUsuarios = $tbUsuarios->getUsrAtual();
        // Salvar Agendamento, por causa da estrutura do BPA
        
        date_default_timezone_set('America/Brasilia');
        $dadosAge = array(
            "age_codigo" => (empty($age_codigo))?0:$age_codigo,
            "age_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "age_horario" => date("H:i"),
            "tat_codigo" => (empty($this->_request->getPost("tat_codigo", FALSE)))?1:$this->_request->getPost("tat_codigo"),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_atendido" => 'A',
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "uni_codigo" => $dadosUsuarios->uni_codigo,
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "usr_codigo_cad" => $dadosUsuarios->usr_codigo,
            "dt_cadastro" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "dt_atualizacao" => "NOW()",
            "age_data_atend" => "NOW()",
            "age_emergencia" => 'N'
            // "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL)
        );

        //Dados que podem ser atualizados 
        $dadosUsu = array(
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "usu_esta_gestante" => $this->_request->getPost("usu_esta_gestante", FALSE)
        );
        

        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
                if(!$this->_request->getParam("age_codigo")){
                    $ageCodigo = $tbAge->salvarAgendamento($dadosAge);
                }else{
                    $ageCodigo = $this->_request->getParam("age_codigo");
                }

                // Salvar Atendimento
                $dadosAte = array(
                    "ate_codigo" => (empty($ate_codigo))?0:$ate_codigo,
                    "ate_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
                    "ate_hora" => date("H:i"),
                    "ate_reclamacao" => $this->_request->getPost("agee_observacao", FALSE),
                    "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                    "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                    "age_codigo" => $ageCodigo,
                    "ate_valor_proc" => "0.00",
                    // "co_equipe" => $this->_request->getPost("cod_equipe", FALSE),
                    "mic_codigo" => (empty($this->_request->getPost("usu_microarea", FALSE)))?0:$this->_request->getPost("usu_microarea", FALSE),
                    "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
                    "ate_data_insert" => "NOW()",
                    "ate_simplificado" => TRUE,
                    "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                    "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                    "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
                    "ate_tipo" => ("P"),
                    "proc_codigo_ab" => $this->_request->getPost("procAB", FALSE),
                    "escuta_inicial_realizada" => $this->_request->getPost("escutaInicialRealizada", FALSE),
                    "turno" => ($this->_request->getPost("turno", false) == '' ? 1 : $this->_request->getPost("turno", false) ),

                    "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL) == "" ? 0 :$this->_request->getPost("usu_ate_dom_mod", null),

                    "ate_tipo_atendimento_paciente" => $this->_request->getPost("ate_tipo_atendimento_paciente", NULL) == "" ? 0 : $this->_request->getPost("ate_tipo_atendimento_paciente", NULL),

                    "ate_conduta_desfecho" => $this->_request->getPost("ate_conduta_desfecho", NULL) == "" ? 0 : 
                        $this->_request->getPost("ate_conduta_desfecho", NULL) ,

                    "ate_encaminhamento_conduta" => $this->_request->getPost("ate_encaminhamento_conduta", NULL) == "" ? 0: $this->_request->getPost("ate_encaminhamento_conduta", NULL) == "",
                );

                // Salva atendimento
                // echo "<pre>";print_r($dadosAte);die();
                $ateCodigo = $tbAte->salvarAtendimento($dadosAte);
                // die("aqui chega");
                // echo "<pre>";print_r(error_get_last($ateCodigo));die();

                // Salva Ciap
                $this->salvaCiap($this->_request->getPost("ciap-selecionados",'F'),$ateCodigo);
                
                // Salva dados conduta
                if ($this->_request->getPost("conduta","F")) {
                    $this->salvarCondutas($this->_request->getPost("conduta", "F"),$ateCodigo);
                }
                // Salvar Procedimentos dos atendimentos

                // Deleta todos os atendimentos do atendimento evitando duplicar procedimento
                $tbAteProc->excluirProcedimentosAtendimento($ateCodigo);
                $dadosProcAte = $this->_request->getPost("procedimento",FALSE);
                $cids = $this->_request->getPost("cid",FALSE);

                $controleCid = 0;

                foreach ($dadosProcAte as $procAte) {
                    $codigoCid = $cids[$controleCid];
                    $controleCid = $controleCid + 1;

                    $dadosProcAte = array(
                        "ate_codigo" => $ateCodigo,
                        "proc_codigo" => $procAte,
                        "usr_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                        "cd10_codigo" => $codigoCid
                    );


                    $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);
                }
            
                Zend_Db_Table::getDefaultAdapter()->commit();

                if($this->_request->getPost('tipoDoAtendimento', FALSE) == "VD"){
                    return $this->_redirect("atendimento/atendimento-simplificado/form-visita-domiciliar?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "A"){
                    return $this->_redirect("/atendimento/atendimento-simplificado/index?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "P") {
                    return $this->_redirect("atendimento/atendimento-simplificado/index-procedimento?alert=success");
                }
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }   
    }

    public function salvarBeneficiosConcedidosAction(){
        if($this->_request->getParam("ate_codigo")){
            $editar=TRUE;
            $ate_codigo = $this->_request->getParam("ate_codigo");
            $age_codigo = $this->_request->getParam("age_codigo");
        }

        $tbUsuarios = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $tbAte = new Application_Model_Atendimento();
        $tbUsu = new Application_Model_Usuario();
        $tbPreNatal = new Application_Model_AtendimentoPrenatal();

        $tbAteProc = new Application_Model_ProcedimentoAtendimento();
        $dadosUsuarios = $tbUsuarios->getUsrAtual();
        // Salvar Agendamento, por causa da estrutura do BPA
        
        date_default_timezone_set('America/Brasilia');
        $dadosAge = array(
            "age_codigo" => (empty($age_codigo))?0:$age_codigo,
            "age_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "age_horario" => date("H:i"),
            "tat_codigo" => (empty($this->_request->getPost("tat_codigo", FALSE)))?1:$this->_request->getPost("tat_codigo"),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_atendido" => 'A',
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "uni_codigo" => $dadosUsuarios->uni_codigo,
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "usr_codigo_cad" => $dadosUsuarios->usr_codigo,
            "dt_cadastro" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "dt_atualizacao" => "NOW()",
            "age_data_atend" => "NOW()",
            "age_emergencia" => 'N'
        );

        //Dados que podem ser atualizados 
        $dadosUsu = array(
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "usu_esta_gestante" => $this->_request->getPost("usu_esta_gestante", FALSE)
        );
        

        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
                if(!$this->_request->getParam("age_codigo")){
                    $ageCodigo = $tbAge->salvarAgendamento($dadosAge);
                }else{
                    $ageCodigo = $this->_request->getParam("age_codigo");
                }

                // Salvar Atendimento
                    $dadosAte = array(
                    "ate_codigo" => (empty($ate_codigo))?0:$ate_codigo,
                    "ate_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
                    "ate_hora" => date("H:i"),
                    "ate_reclamacao" => $this->_request->getPost("agee_observacao", FALSE),
                    "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                    "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                    "age_codigo" => $ageCodigo,
                    "ate_valor_proc" => "0.00",
                    "mic_codigo" => (empty($this->_request->getPost("usu_microarea", FALSE)))?0:$this->_request->getPost("usu_microarea", FALSE),
                    "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
                    "ate_data_insert" => "NOW()",
                    "beneficio_emergencia" => ($this->_request->getPost("estaEmUrgencia", FALSE) == '' ? 'F' : $this->_request->getPost("estaEmUrgencia", FALSE)),
                    "ate_simplificado" => TRUE,
                    "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                    "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                    "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
                    "laboratorio_de_destino" => $this->_request->getPost("LaboratorioDeDestino", FALSE),
                    "ate_tipo" => ("B"),
                    "proc_codigo_ab" => 70,
                    "turno" => 1,
                    "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL) == "" ? 0 :$this->_request->getPost("usu_ate_dom_mod", null),

                    "ate_tipo_atendimento_paciente" => $this->_request->getPost("ate_tipo_atendimento_paciente", NULL) == "" ? 0 : $this->_request->getPost("ate_tipo_atendimento_paciente", NULL),

                    "ate_conduta_desfecho" => $this->_request->getPost("ate_conduta_desfecho", NULL) == "" ? 0 : 
                        $this->_request->getPost("ate_conduta_desfecho", NULL) ,

                    "ate_encaminhamento_conduta" => $this->_request->getPost("ate_encaminhamento_conduta", NULL) == "" ? 0: $this->_request->getPost("ate_encaminhamento_conduta", NULL) == "",
                );

                
                // Salva atendimento
                //echo "<pre>";print_r($dadosAte);die();
                $ateCodigo = $tbAte->salvarAtendimento($dadosAte);
                // die("aqui chega");
                // echo "<pre>";print_r(error_get_last($ateCodigo));die();

                // Salva Ciap
                $this->salvaCiap($this->_request->getPost("ciap-selecionados",'F'),$ateCodigo);
                
                // Salva dados conduta
                if ($this->_request->getPost("conduta","F")) {
                    $this->salvarCondutas($this->_request->getPost("conduta", "F"),$ateCodigo);
                }
                // Salvar Procedimentos dos atendimentos

                // Deleta todos os atendimentos do atendimento evitando duplicar procedimento
                $tbAteProc->excluirProcedimentosAtendimento($ateCodigo);
                $dadosProcAte = $this->_request->getPost("procedimento",FALSE);

                $cids = $this->_request->getPost("cid",FALSE);

                $controleCid = 0;
                $recebeQuantidade = $this->_request->getPost("quantidadeTotalDoProcedimento");
                $recebeValor = $this->_request->getPost("valorDoProcedimento");

                // echo "<pre>";print_r($recebeQuantidade);die();

                $contadorDeQuantidadeValor = 0;
                foreach ($dadosProcAte as $procAte) {
                    $codigoCid = $cids[$controleCid];
                    $controleCid = $controleCid + 1;
                    
                    $dadosProcAte = array(
                        "ate_codigo" => $ateCodigo,
                        "proc_codigo" => $procAte,
                        "usr_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                        "quantidade_total_do_procedimento" => $recebeQuantidade[$contadorDeQuantidadeValor],
                        "valor_do_procedimento" => $recebeValor[$contadorDeQuantidadeValor],
                        "cd10_codigo" => $codigoCid
                    );
                    // echo "<pre>";print_r($dadosProcAte);die();
                    $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);
                    $contadorDeQuantidadeValor = $contadorDeQuantidadeValor +1;
                }

                // $dadosProcAteAB = $this->_request->getPost("procAB",FALSE);
            
                Zend_Db_Table::getDefaultAdapter()->commit();

                if($this->_request->getPost('tipoDoAtendimento', FALSE) == "VD"){
                    return $this->_redirect("atendimento/atendimento-simplificado/form-visita-domiciliar?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "A"){
                    return $this->_redirect("/atendimento/atendimento-simplificado/index?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "P") {
                    return $this->_redirect("atendimento/atendimento-simplificado/index-procedimento?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "B") {
                    $this->_redirect("atendimento/atendimento-simplificado/index-beneficios-concedidos?imprimir=".$ateCodigo);
                    // $this->view->codigoAtendimento = $ateCodigo;
                }
        } catch (Exception $exc) {
            // Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }   
    }

    public function imprimirBeneficiosEventuaisAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        // Zend_Layout::getMvcInstance()->setLayout("print");
        $tipoAtendimento = new Application_Model_Atendimento();
        $codigoAtendimento = $this->_getParam('codigoAtendimento', FALSE);
        $codigoAtendimentoInt = intval($codigoAtendimento);
        $this->view->recebeAtendimentoTipoBeneficio = $tipoAtendimento->recuperaBeneficioAoSalvar($codigoAtendimentoInt);
        $this->view->title = "Imprimir Encaminhamento";
        return $this->render("imprimir-beneficios-salvar");
    }

    public function salvarVisitaDomiciliarAction(){

        $dataNascimento = 0;

        $recebeTipoImovel = $this->_request->getPost("visita_tipo_domicilio");
        $recebeVisitaMotivo = $this->_request->getPost("visita_motivo");


        if ($recebeTipoImovel == 1 || $recebeTipoImovel == 7 || $recebeTipoImovel == 8 ||
            $recebeTipoImovel == 9 || $recebeTipoImovel == 10 || $recebeTipoImovel == 11 ||
            $recebeTipoImovel == 99) {
            
            foreach ($recebeVisitaMotivo as $motivo) {
                if ($motivo == 2 || $motivo == 3 || $motivo == 4 || $motivo == 30 ||
                    $motivo == 5 ||  $motivo == 6 ||  $motivo == 7 ||  $motivo == 8 ||  $motivo == 9 ||  $motivo == 10 ||
                    $motivo == 11 ||  $motivo == 12 ||  $motivo == 13 ||  $motivo == 14 ||  $motivo == 15 ||  $motivo == 16 ||
                    $motivo == 17 ||  $motivo == 18 ||  $motivo == 32 ||  $motivo == 33 ||  $motivo == 19 ||  $motivo == 20 ||
                    $motivo == 21 ||  $motivo == 22 ||  $motivo == 23 ||  $motivo == 24 ||  $motivo == 35 ||  $motivo == 31)

                {
                    $dataNascimento = $this->_request->getPost("id_data");
                }
            }

        } else{
            $dataNascimento = null;
        }        

        // ----------------------------------------------------------------------

        $usu_sexo = null;

        if ($recebeTipoImovel == 1 || $recebeTipoImovel == 7 || $recebeTipoImovel == 8 ||
            $recebeTipoImovel == 9 || $recebeTipoImovel == 10 || $recebeTipoImovel == 11 ||
            $recebeTipoImovel == 99) {
            
            foreach ($recebeVisitaMotivo as $motivo) {
                if ($motivo == 2 || $motivo == 3 || $motivo == 4 || $motivo == 30 ||
                    $motivo == 5 ||  $motivo == 6 ||  $motivo == 7 ||  $motivo == 8 ||  $motivo == 9 ||  $motivo == 10 ||
                    $motivo == 11 ||  $motivo == 12 ||  $motivo == 13 ||  $motivo == 14 ||  $motivo == 15 ||  $motivo == 16 ||
                    $motivo == 17 ||  $motivo == 18 ||  $motivo == 32 ||  $motivo == 33 ||  $motivo == 19 ||  $motivo == 20 ||
                    $motivo == 21 ||  $motivo == 22 ||  $motivo == 23 ||  $motivo == 24 ||  $motivo == 35 ||  $motivo == 31)

                {
                    $usu_sexo = $this->_request->getPost("usu_sexo");
                }
            }

        } else{
            $usu_sexo = null;
        }

        // --------------------------------------------------------------------
            
        // --------------------------------------------------------------------
        if($this->_request->getParam("ate_codigo")){
            $editar=TRUE;
            $ate_codigo = $this->_request->getParam("ate_codigo");
            $age_codigo = $this->_request->getParam("age_codigo");
        }

        $tbUsuarios = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $tbAte = new Application_Model_Atendimento();
        $tbUsu = new Application_Model_Usuario();
        $tbPreNatal = new Application_Model_AtendimentoPrenatal();

        $tbAteProc = new Application_Model_ProcedimentoAtendimento();
        $dadosUsuarios = $tbUsuarios->getUsrAtual();
        // Salvar Agendamento, por causa da estrutura do BPA
        date_default_timezone_set('America/Brasilia');
        $dadosAge = array(
            "age_codigo" => (empty($age_codigo))?0:$age_codigo,
            "age_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "age_horario" => date("H:i"),
            "tat_codigo" => (empty($this->_request->getPost("tat_codigo", FALSE)))?1:$this->_request->getPost("tat_codigo"),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_atendido" => 'A',
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "uni_codigo" => $dadosUsuarios->uni_codigo,
            "esp_codigo" => $this->_request->getPost("esp_codigo", FALSE),
            "usr_codigo_cad" => $dadosUsuarios->usr_codigo,
            "dt_cadastro" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "dt_atualizacao" => "NOW()",
            "age_data_atend" => "NOW()",
            "age_emergencia" => 'N'
            // "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL)
        );

        //Dados que podem ser atualizados 
        $dadosUsu = array(
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "usu_esta_gestante" => $this->_request->getPost("usu_esta_gestante", FALSE)
        );
        


        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
                if(!$this->_request->getParam("age_codigo")){
                    $ageCodigo = $tbAge->salvarAgendamento($dadosAge);
                }else{
                    $ageCodigo = $this->_request->getParam("age_codigo");
                }
         
                // Salvar Atendimento
                
                $dadosAte = array(
                    "ate_codigo" => (empty($ate_codigo))?0:$ate_codigo,
                    "ate_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : date('Y-m-d'),
                    "ate_hora" => date("H:i"),
                    "ate_reclamacao" => $this->_request->getPost("agee_observacao", FALSE),
                    "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                    "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                    "age_codigo" => $ageCodigo,
                    "ate_valor_proc" => "0.00",
                    "co_equipe" => $this->_request->getPost("cod_equipe", FALSE),
                    "usu_microarea_fa" => $this->_request->getPost("usu_microarea_fa", FALSE),
                    "mic_codigo" => (empty($this->_request->getPost("usu_microarea", FALSE)))?0:$this->_request->getPost("usu_microarea", FALSE),
                    "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
                    "ate_data_insert" => date('Y-m-d'),
                    "ate_simplificado" => TRUE,
                    "ate_peso" => (empty($this->_request->getPost("ate_peso", FALSE)))?'0.00':$this->_request->getPost("ate_peso", FALSE),
                    "ate_perimetro_cefalico" => (empty($this->_request->getPost("ate_perimetro_cefalico", FALSE)))?'0.00':$this->_request->getPost("ate_perimetro_cefalico", FALSE),
                    "ate_altura" => (empty($this->_request->getPost("ate_altura", FALSE)))?'0.00':$this->_request->getPost("ate_altura", FALSE),
                    "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                    "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                    "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
                    "ate_tipo" => ("V"),
                    "visita_compartilhada_por_profissional" => $this->_request->getPost("visitaCompartilhadaPorProfissional", FALSE),
                    "turno" => ($this->_request->getPost("turno", false) == '' ? 1 : $this->_request->getPost("turno", false) ),
                    "usu_ate_dom_mod" => $this->_request->getPost("usu_ate_dom_mod", NULL),
                    "ate_tipo_atendimento_paciente" => $this->_request->getPost("ate_tipo_atendimento_paciente", NULL),
                    "ate_conduta_desfecho" => $this->_request->getPost("ate_conduta_desfecho", NULL),
                    "ate_encaminhamento_conduta" => $this->_request->getPost("ate_encaminhamento_conduta", NULL),
                    "visita_tipo_domicilio" => $this->_request->getPost("visita_tipo_domicilio", NULL),
                    "usu_dtnascimento" => "NOW()",
                    "usu_sexo" => $usu_sexo
                );

                // Salva atendimento

                // echo "<pre>";print_r($dadosAte);die();
                $ateCodigo = $tbAte->salvarAtendimento($dadosAte);
                
                if($this->_request->getPost("tipoDoAtendimento", FALSE) == "VD") {
                    $this->salvaDadosVisita($this->_request->getPost(), $ateCodigo);
                }

                // Salva Ciap
                $this->salvaCiap($this->_request->getPost("ciap-selecionados",'F'),$ateCodigo);
                
                // Salva dados conduta
                if ($this->_request->getPost("conduta","F")) {
                    $this->salvarCondutas($this->_request->getPost("conduta", "F"),$ateCodigo);
                }
                // Salvar Procedimentos dos atendimentos

                // Deleta todos os atendimentos do atendimento evitando duplicar procedimento
                $tbAteProc->excluirProcedimentosAtendimento($ateCodigo);

                //procedimento_atendimento VISITA DOMICILIAR 
                
                $codigoDaEspecialidade = $this->_request->getPost("carregaEspecialidade", FALSE);

                if ($codigoDaEspecialidade ==  'C' || $codigoDaEspecialidade == 'A') {
                    
                    $dadosProcAte = array(
                        "ate_codigo" => $ateCodigo,
                        "proc_codigo" => 4396,
                        "usr_codigo" => $this->_request->getPost("usr_codigo", FALSE)
                    );

                    $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);

                } else{

                    $dadosProcAte = array(
                        "ate_codigo" => $ateCodigo,
                        "proc_codigo" => 104,
                        "usr_codigo" => $this->_request->getPost("usr_codigo", FALSE)
                    );

                    $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);
                }
            
                Zend_Db_Table::getDefaultAdapter()->commit();

                if($this->_request->getPost('tipoDoAtendimento', FALSE) == "VD"){
                    return $this->_redirect("atendimento/atendimento-simplificado/index-visita-domiciliar?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "A"){
                    return $this->_redirect("/atendimento/atendimento-simplificado/index?alert=success");
                } else if($this->_request->getPost('tipoDoAtendimento', FALSE) == "P") {
                    return $this->_redirect("atendimento/atendimento-simplificado/form-procedimento?alert=success");
                }
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }
    }

    public function salvaDadosVisita($data,$ate_codigo){
        $tbVisista = new Application_Model_TbCdsVisitaDomiciliar();
        $data_visita = array("st_acompanhada_outro_prof"=>null,
                             "ate_codigo"=>$ate_codigo,
                             "co_cds_visita_dom_desfecho"=>$data[visita_desfecho]
                            );
        $co_seq_cds_visita_domiciliar = $tbVisista->salvar($data_visita);
        $this->salvarMotivosVisita($data[visita_motivo],$co_seq_cds_visita_domiciliar);
        
        return true;
    }

    public function salvarCondutas($post=FALSE,$ateCod=FALSE){
        $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
        foreach ($post as $val) {
            $dados = "";
            $dados = array(
                "ate_codigo" => $ateCod,
                "tp_cds_conduta" => $val
            );
            try{
                $tbCond->salvar($dados);
            } catch (Exception $exc) {
                return $exc->getMessage();
            }
        }
    }

    public function salvarMotivosVisita($data,$cod_visita){
        $rlVisitaMotivo = new Application_Model_RlCdsVisitaDomMotivo();
        $array = array();

        foreach ($data as $itens){

            $array = array("co_cds_visita_domiciliar"=>$cod_visita, "co_cds_visita_dom_motivo"=>$itens);

            $cod_motivo_visita = $rlVisitaMotivo->salvar($array);

        }

        
        return true;
    }

    private function salvaCiap($data,$ate_codigo){
        $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
        $dados = array();
        $rlAteCiap->limpaCiapAtendimento($ate_codigo);
        foreach ($data as $item){
            $dados = array("ate_codigo"=>$ate_codigo,
                           "co_ciap"=>$item);
            $rlAteCiap->salvar($dados);
        }
        return true;
    }

    public function listaAtendimentosSimplificadosAction(){
        $tbAte = new Application_Model_Atendimento();
        $busca = $this->_request->getPost("busca",FALSE);
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->dados = $tbAte->listaAtendimentosSimplificados($busca,$tipoBusca,$usr_codigo);
    }

    public function listaVisitaDomiciliarAction(){
        $tbAte = new Application_Model_Atendimento();
        $busca = $this->_request->getPost("busca",FALSE);
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->dados = $tbAte->listaVisitaDomiciliar($busca,$tipoBusca,$usr_codigo);
    }

    public function listaProcedimentoAction(){
        $tbAte = new Application_Model_Atendimento();
        $busca = $this->_request->getPost("busca",FALSE);
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->dados = $tbAte->listaProcedimento($busca,$tipoBusca, $usr_codigo);
    }

    public function listaBeneficiosConcedidosAction(){
        $tbAte = new Application_Model_Atendimento();
        $busca = $this->_request->getPost("busca",FALSE);
        $tipoBusca = $this->_request->getPost("tipo_busca",FALSE);
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->dados = $tbAte->listaBeneficiosConcedidos($busca,$tipoBusca, $usr_codigo);
    }

    public function excluirAtendimentoSimplificadoAction(){
        $ateCodigo = $this->_request->getParam("ate_codigo");
        $ageCodigo = $this->_request->getParam("age_codigo");
        $ate_tipo = $this->_request->getParam("ate_tipo");
        // Excluindo os procedimentos do atendimento
        $tbProcAte = new Application_Model_ProcedimentoAtendimento();
        $tbProcAte->excluirProcedimentosAtendimento($ateCodigo);

        $tbAteCond = new Application_Model_RlCdsAtendIndividualCondut();
        $tbAteCond->excluirPorAtendimento($ateCodigo);

        $tbAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
        $tbAteCiap->limpaCiapAtendimento($ateCodigo);
        // Excluindo o atendimento
        $tbEvd = new Application_Model_EsusVisitaDomiciliar();
        $tbEvd->excluir($ateCodigo);

        $tbAte = new Application_Model_Atendimento();
        $tbAte->excluir($ateCodigo);

        // Excluindo o agendamento
        $tbAge = new Application_Model_Agendamento();
        $tbAge->excluir($ageCodigo);
        switch ($ate_tipo) {
            case 'B':
                    $this->_redirect("atendimento/atendimento-simplificado/index-beneficios-concedidos/#tabs2-2");
                break;

            case 'P':
                $this->_redirect("atendimento/atendimento-simplificado/index-procedimento/#tabs2-2");
            break;

            case 'V':
                $this->_redirect("atendimento/atendimento-simplificado/index-visita-domiciliar/#tabs2-2");
            break;

            case 'A':
                $this->_redirect("atendimento/atendimento-simplificado/index/#tabs2-2");
            break;
            
            default:
                $this->_redirect("atendimento/atendimento-simplificado/index/#tabs2-2");
            break;
        }

    }

    public function getCiapAction(){
        $tbCiap = new Application_Model_TbCiap();

        echo json_encode($tbCiap->getCiaps()->toArray());
        
        exit(0);
    }


    public function indexFichaRaasAction(){
        $this->_helper->layout->setLayout("simples");

        $volta = $this->_getParam("param",FALSE);
        //echo "<pre>";print_r($volta);die();
        if($volta==1) $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");



        $tb = new Application_Model_EditarRaas();
        //$recebeCodigoFichaRaas =  $this->_request->getParam("ras_prontuario");
        if($recebeCodigoFichaRaas =  $this->_request->getParam("ras_prontuario")){



            $x = $tb->recuperaFichaRaas($recebeCodigoFichaRaas);
            $y = $x[0][ras_cnes_esf];

            if($y!=NULL){
                $z = $tb->recuperaUnidadeEsf($y);
                $this->view->recuperaUnidadeEsf = $z;
                
            }else{
                $z[0][uni_desc] = '';
            }

            //echo "<pre>";print_r($x[0][ras_cidp]); die();

            if($x[0][ras_cidp] != ""){
                $r = $tb->recuperaCids($recebeCodigoFichaRaas);

                //echo "<pre>"; print_r($r);die();
                $this->view->grupo_doencas = $r;
            }

            //echo "<pre>"; print_r($x);die();
            $this->view->recuperaFichaRaas = $x;

        }
    }

    public function editarFichaRaasAction(){
        //die("teste");
        $form = $this->_request->getPost("recebeForm");
        $tb = new Application_Model_EditarRaas();
        $tbRaas = new Application_Model_FichaRaas();
        $tbAcoes = new Application_Model_Acoes();
        $raasid = $this->_request->getPost("recebeId");
        //echo "<pre>";print_r($form);die();

        $drogas = $form[alcool][0] . $form[crack][0] . $form[outros][0];

        $nascpaciente = $form[usu_datanasc];

        $nasc1 = date('Y', strtotime($nascpaciente));
        $anoatual1 = date('Y');

        $responsavel = $anoatual1 - $nasc1;
        if($responsavel <18){
            $nomeresponsavel = $form[usu_mae];
        }else{
            $nomeresponsavel = $form[usu_nome];
        }

        $coberturaesf = $form[radio2];

        if($coberturaesf == 'N'){
            $esf = 0;
        }
        else{
            // die("teste");
            $esf = $form[uni_cnes];

            if($esf=="[object HTMLInputElement]"){
                $this->_redirect("atendimento/atendimento-simplificado/form-ficha-raas?alert=error");
                return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");
            }
        }
        
        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $unicod = $usr->uni_codigo;
        $usrcod = $usr->usr_codigo;
        $unn = $tbAcoes->pegaUniCnes($unicod);

        $variavel2 = $unn[0][uni_cnes];
        //$rasid = $form[raas_id];
        //die("teste");
        $dados = array(
            "raas_id" => $raasid,
            "ras_codlinha_ad" => 15,
            "ras_cod_paciente" => $form[usu_codigo],
            "ras_cns_paciente" => $form[usu_cartao_sus],
            "ras_uf" => 41,
            "ras_paciente" => $form[usu_nome],
            "ras_usu_droga" => $form[usu_drogas],
            "ras_usu_tipo_droga" => ($form[usu_drogas]=='N' ? '' : $drogas),
            "ras_origem" => $form[origem_paciente],
            "ras_destino" => $form[destino_paciente],
            "ras_cobertura_esf" => $form[radio2],
            "ras_cnes_esf" => $esf,

            "ras_cidp" => ($form[cid_codigo1] =!"" ? $form[cid_codigo1] : $this->_request->getPost("ras_cidp",FALSE)),
            "ras_cids1" => ($form[cid_codigo2] =!"" ? $form[cid_codigo2] : $this->_request->getPost("ras_cids1",FALSE)),
            "ras_cids2" => ($form[cid_codigo3] =!"" ? $form[cid_codigo3] : $this->_request->getPost("ras_cids2",FALSE)),
            "ras_cids3" => ($form[cid_codigo4] =!"" ? $form[cid_codigo4] : $this->_request->getPost("ras_cids3",FALSE)),
            "ras_cidca" => ($form[cid_codigo5] =!"" ? $form[cid_codigo5] : $this->_request->getPost("ras_cidca",FALSE)),
             
            "ras_usr" => $usrcod,

            "ras_situacao_rua" => $form[usu_sit_rua],
            "ras_carater" => $form[carater],
            "ras_org" => $form[origem_info],
            "ras_autorizacao" => $form[autorizacao],

            "ras_nomemae" => $form[usu_mae],
            "ras_logradouro" =>$form[rua_nome],
            "ras_numero" =>$form[dom_numero],
            "ras_complemento" => $form[dom_complemento],
            "ras_cep" => $form[rua_cep],

            "ras_ibge_mun" => 4117909,

            "ras_datanasc" => $nascpaciente,
            "ras_sexo" => $form[usu_sexo],
            "ras_raca" =>$form[rac_codigo],
            "ras_responsavel" => $nomeresponsavel,

            "ras_nacionalidade" =>($form[pais_codigo] != '' ?  $form[pais_codigo]  :'010' ),
            "ras_telefone" =>$form[dom_telefone],
            "ras_celular" =>$form[usu_celular],

            "ras_cnes" => $variavel2 

        );

        $retorno = $tb->salvar($dados);

        return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");
    }

    public function finalizarFichaRaasAction(){
        $tbRaas = new Application_Model_FichaRaas();

        $x = $this->_request->getPost("valor");
        $z = $this->_request->getPost("param");
        $y = date('Ymd');

        $retorno = $tbRaas->updateFinalizaRaas($x,$y,$z);
        if($retorno)$this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");

    }


    public function formFichaRaasAction(){
        $this->view->title = "Ficha RAAS";
        $tbLocal = new Application_Model_TbLocalAtend();
        $tbTa = new Application_Model_TipoAtendimento();
        $tbUsr = new Application_Model_Usuarios();
        $tb = new Application_Model_Procedimento();
        $tipoMedico = new Application_Model_Medico();

        $usr = $tbUsr->getUsrAtual();

        $this->view->usuario = $usr;
        $this->view->selectLocais = $tbLocal->selectTagLocalAtendimento();
        $this->view->tipo_atendimento = $tbTa->getTiposDeAtendimento();
        $this->view->unidade_controle = $usr->uni_desc;
        $this->view->uni_codigo_controle = $usr->uni_codigo;
        $this->view->usr_nome_controle = $usr->usr_nome;
        $this->view->usr_codigo_controle = $usr->usr_codigo;

        $this->render('form-ficha-raas');
    }

    public function listaFichaRaasAction(){
        $this->view->title = "Listagem Ficha RAAS";
        $tbRas = new Application_Model_FichaRaas();

        $volta = $this->_getParam("param",FALSE);
        //echo "<pre>";print_r($volta);die();
        if($volta==1) $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");

        $busca = $this->_request->getPost("busca",FALSE);
        $tbPac = new Application_Model_FichaRaas;
        $ras_prontuario = $tbPac->buscaListaRaas($busca);

        $this->view->dados = $tbRas->listaFichaRaas($busca, $ras_prontuario);
/*        $recuperaMotivo= $tbRas->pegaMotivo($ras_prontuario);
        $this->view->recuperaMotivo = $recuperaMotivo;
*/
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;

        //return $this->render("index-ficha-raas");

    }

    public function listaAcoesRaasAction(){
        $this->view->title = "Lista Ações";
        $tbAcao = new Application_Model_Acoes();
        $tbPac = new Application_Model_FichaRaas();
        $ras_prontuario = $this->_getParam("prontuario",FALSE);

        //echo "<pre>";print_r($salvaprontuario);die();
        $dados = $tbAcao->listaAcoesRaas($ras_prontuario);

        //echo "<pre>";print_r($salvaprontuario);die();
        $this->view->dados = $dados;
        $this->view->message = $ras_prontuario;
        $this->view->msg = $ras_prontuario;

    }

    public function adicionarAcoesRaasAction(){
        $this->view->title = "Adicionar Ações RAAS";
        $prontuario = $this->_getParam("prontuario",FALSE);
        $tbAcoes = new Application_Model_Acoes();

        $x = substr($prontuario,0,4);
        $y = substr($prontuario,4);
        $z = $y . "/" . $x;

        $this->view->message = $prontuario;
        $this->view->msg = $z;

        if($acaoId =  $this->_request->getParam("acaoid")){
            $x = $tbAcoes->recuperaAcao($acaoId);

            $xEditar = substr($x[0][ras_prontuario],0,4);
            $yEditar = substr($x[0][ras_prontuario],4);
            $zEditar = $yEditar . "/" . $xEditar;

            $this->view->message = $x[0][ras_prontuario];
            $this->view->msg = $zEditar;

            $acao = $tbAcoes->pegaAcao($x[0][ras_acao]); //pega dados da ação nome id codsus

            $this->view->recuperaProc = $acao;
            $this->view->recuperaAcoes = $x;

        }

    }

    public function salvarFichaRaasAction(){
        //echo "<pre>";print_r($_POST);die();
        $this->view->title = "Ficha RAAS";

        $tbRaas = new Application_Model_FichaRaas();
        $tbAcoes = new Application_Model_Acoes();

        $aco = $this->_request->getPost("alcool",FALSE).$this->_request->getPost("crack",FALSE).$this->_request->getPost("outros",FALSE);
        $cida = $this->_request->getPost("cid_codigo",FALSE);
        $coberturaesf = $this->_request->getPost("radio2",FALSE);

        if($coberturaesf == 'N'){
            $esf = 0;
        }
        else{
            // die("teste");
            $esf = $this->_request->getPost("uni_cnes",FALSE);

            if($esf=="[object HTMLInputElement]" || $esf == ""){
                $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas?alert=error");
                return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");
            }
        }

        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();
        $usrcodigo = $usr->usr_codigo;
        $unicod = $usr->uni_codigo;
        //echo "<pre>";print_r($usrcodigo);die();
        $unn = $tbAcoes->pegaUniCnes($unicod);
        $variavel2 = $unn[0][uni_cnes];

        $cidp = $cida[0];
        if($cidp==""){
            $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas?alert=errorcid");
            return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");
        }
        $cids1 = $cida[1];
        $cids2 = $cida[2];
        $cids3 = $cida[3];
        $cidca = $cida[4];

        $datapront = $this->_request->getPost("data_atendimento",FALSE);
        
        $tbProntAno = $datapront = date('Y');
        $tbProntAno2 = $datapront = date('Y');

        $tbProntMes = $datapront = date('m');
        $dataMesAno =  $tbProntAno2 . $tbProntMes ;

        $nascpaciente = $this->_request->getPost("usu_datanasc",FALSE);
        $nasc1 = date('Y', strtotime($nascpaciente));
        $anoatual1 = date('Y');

        $responsavel = $anoatual1 - $nasc1;
        if($responsavel <18){
            $nomeresponsavel = $this->_request->getPost("usu_mae",FALSE);
        }else{
            $nomeresponsavel = $this->_request->getPost("usu_nome",FALSE);
        }

        $usu_sexo = $this->_request->getPost("usu_sexo",FALSE);
        //echo "<pre>";var_dump($usu_sexo);die();
        if($usu_sexo == '1') $usu_sexo = 'F';
        else if($usu_sexo == '0') $usu_sexo = 'M';

        $paciente_nome = $this->_request->getPost("usu_nome",FALSE);
        if($paciente_nome == ""){
            $this->_redirect("atendimento/atendimento-simplificado/form-ficha-raas?alert=errorpaciente");
            return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");
        }
        //echo "<pre>"; print_r($dataMesAno); die();

        $dados = array(

                "ras_codlinha_ad" => 15,

                "ras_uf" => $this->_request->getPost("ras_uf",FALSE), //cod uf ibge 41

                "ras_cnes" => $variavel2,
                "ras_data" => $dataMesAno,
                //paciente
                "ras_cod_paciente" => $this->_request->getPost("usu_codigo",FALSE),
                "ras_cns_paciente" => $this->_request->getPost("usu_cartao_sus",FALSE),
                "ras_val_ini" => ($this->_request->getPost("data_atendimento",FALSE) == "" ? date('Y-m-d') : $this->_request->getPost("data_atendimento",FALSE)),
                "ras_val_fin" => $this->_request->getPost("data_final",FALSE),
                "ras_paciente" => $this->_request->getPost("usu_nome",FALSE),
                "ras_prontuario" => $this->_request->getPost("usu_prontuario",FALSE),
                "ras_nomemae" => $this->_request->getPost("usu_mae",FALSE),
                //logradouro
                "ras_logradouro" =>$this->_request->getPost("rua_nome",FALSE),
                "ras_numero" =>$this->_request->getPost("dom_numero",FALSE),
                "ras_complemento" => $this->_request->getPost("dom_complemento",FALSE),
                "ras_cep" => $this->_request->getPost("rua_cep",FALSE),

                "ras_ibge_mun" => $this->_request->getPost("muni_codigo_ibge_resid",FALSE), //declarar no js
                //paciente
                "ras_datanasc" => $nascpaciente,
                "ras_sexo" => $usu_sexo,
                "ras_raca" =>$this->_request->getPost("rac_codigo",FALSE),
                "ras_responsavel" => $nomeresponsavel,

                "ras_nacionalidade" =>($this->_request->getPost("pais_codigo",FALSE) == "" ? '010' : $this->_request->getPost("pais_codigo",FALSE) ),
                "ras_telefone" => ($this->_request->getPost("dom_telefone",FALSE)=="" ? $this->_request->getPost("usu_fone_2",FALSE) :$this->_request->getPost("dom_telefone",FALSE)) ,
                "ras_celular" =>$this->_request->getPost("usu_celular"),

                //atendimento
                "ras_motivosaida" =>$this->_request->getPost("motivo_saida",FALSE),
                "ras_data_obito_alta" =>$this->_request->getPost("data_motivo_saida",FALSE),
                
                "ras_cidp" => $cidp,
                "ras_cids1" => $cids1,
                "ras_cids2" => $cids2,
                "ras_cids3" => $cids3,
                "ras_cidca" => $cidca,

                "ras_carater" => $this->_request->getPost("carater",FALSE),
                "ras_origem" => $this->_request->getPost("origem_paciente",FALSE),
                "ras_cobertura_esf" => $coberturaesf,
                "ras_cnes_esf" => $esf,

                "ras_total_acoes" => $this->_request->getPost("total_acoes",FALSE), //count em ações com o mesmo ras_prontuario

                "ras_destino" => $this->_request->getPost("destino_paciente",FALSE),
                "ras_org" => $this->_request->getPost("origem_info",FALSE),
                "ras_situacao_rua" => $this->_request->getPost("usu_sit_rua",FALSE),
                "ras_usu_droga" => $this->_request->getPost("usu_drogas",FALSE),
                "ras_usu_tipo_droga" => ($this->_request->getPost("usu_drogas",FALSE) =='N' ? '' : $aco),
                "ras_autorizacao" => $this->_request->getPost("autorizacao",FALSE),
                "ras_usr" => $usrcodigo,
                "ras_filler" => "    "
        );
        //echo "<pre>"; var_dump($dados); die();

        $tbRaasResultado = $tbRaas->salvar($dados);
        $prontuario = $tbProntAno . $tbRaasResultado;

        $tbProntRas = $tbRaas->updateProntuarioRaas($prontuario,$tbRaasResultado);
        $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas?alert=sucess");
        return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");

        // $this->render('index-ficha-raas');
        //echo "<pre>";print_r($dados);die();
    }

    public function excluirFichaRaasAction()
    {
        $tb = new Application_Model_FichaRaas();
        //die("x");
        $recebeCodigoFichaRaas =  $this->_request->getParam("ras_prontuario");

        //echo "<pre>";print_r($recebeCodigoFichaRaas);die();



        $retorno = $tb->excluirFicha($recebeCodigoFichaRaas);
        

        //$this->view->dados = array('status' => TRUE );
        //return $this->render("dados");
        $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");
        //return $this->_redirect("atendimento/atendimento-simplificado/lista-ficha-raas");
    }

    public function salvarEditarAcoesRaasAction(){
        $tbAcoes = new Application_Model_Acoes();

        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();

        $unid = new Application_Model_Unidade();
        $unicod = $usr->uni_codigo;
        $unn = $tbAcoes->pegaUniCnes($unicod);
        $variavel2 = $unn[0][uni_cnes];

        $rascnsusr = $tbAcoes->pegaUsrCns($usr->usr_codigo); //cns do executante
        $rascbousr = $tbAcoes->pegaUsrCbo($usr->esp_codigo); //cbo do executante

        $variavel3 = $rascbousr[0][cod_cbo];
        $variavel4 = $rascnsusr[0][cnes_cod_cns];

        $tbRaas = new Application_Model_FichaRaas();
        
        $salvaprontuario = ($this->_request->getPost("prontuario", FALSE) == "" ? $this->_request->getPost("recebeProntuarioValueHidden", FALSE) : $this->_request->getPost("prontuario", FALSE) );
        $pac = $tbRaas->pegaCns($salvaprontuario); //pega cns do paciente

        $variavel = $pac[0][ras_cns_paciente];

        $dataatual = date('d/m/Y');
        $dataano = date('Y');
        $datames = date('m');

        $dataconc =  $dataano . $datames;

        $data_do_beneficio = $this->_request->getPost("dataDoBeneficio");
        $qnt_total_do_proc = $this->_request->getPost("quantidadeTotalDoProcedimento");
        $qnt_de_procedimentos = $this->_request->getPost("procedimento");
        $proc_codigo_sus = $this->_request->getPost("procedimento_cod_sus");

        // print_r($qnt_de_procedimentos); die();

        if($qnt_de_procedimentos == NULL){
            $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao");
        }

        for ($i=0; $i < count($qnt_de_procedimentos) ; $i++) {
            $dadosacoes = array(
                "ras_acoes_id" => $this->_request->getPost("acoes_id",FALSE),
                "ras_codlinha_ad_acoes" => 16,
                "ras_prontuario" => $salvaprontuario, //salvaprontuario

                "ras_coduf" => $this->_request->getPost("ras_uf",FALSE), //codigo ibge da unidade da federação
                "ras_anomes" => $dataconc, //salva ano e mes yyyymm
                "ras_val_ini" =>($this->_request->getPost("data_inicio",FALSE) == "" ? $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao") : $this->_request->getPost("data_inicio",FALSE) ), //getdate atual
                "ras_cns" => $variavel, //paciente cartao sus
                "ras_cnes" => $variavel2, //unidade cnes
                //"ras_acao" => $qnt_de_procedimentos[$i], //codigo da ação no banco
                //"ras_acao" => $this->_request->getPost("proc_codigo_sus", FALSE), //codigo da ação no sus
                "ras_acao" => $proc_codigo_sus[$i],
                "ras_cbos_usr" => $variavel3, //cbo executante getusratual
                "ras_cns_usr" => $variavel4, //cns executante
                //"ras_dataexe" => $this->_request->getPost("dataexe",FALSE),//$data_do_beneficio[$i],
                "ras_dataexe" => $data_do_beneficio[$i],
                "ras_servico" => 115,
                "ras_class" => $this->_request->getPost("rasclass",FALSE),
                //"ras_qnt" => $this->_request->getPost("quantidade",FALSE),//$qnt_total_do_proc[$i],
                "ras_qnt" => $qnt_total_do_proc[$i],
                "ras_org" => $this->_request->getPost("origem_info",FALSE),
                "ras_local_realizacao" => $this->_request->getPost("local",FALSE),
                "ras_filler" => '    '

            );
            //echo "<pre>"; print_r($dadosacoes); die();
            $tbAcoes->salvar($dadosacoes);
            
            // $this->render('adicionar-acoes-raas');
        }
        $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");
    }
    public function salvarAcoesRaasAction(){
        $tbAcoes = new Application_Model_Acoes();

        $tbUsr = new Application_Model_Usuarios();
        $usr = $tbUsr->getUsrAtual();

        $unid = new Application_Model_Unidade();
        $unicod = $usr->uni_codigo;
        $unn = $tbAcoes->pegaUniCnes($unicod);
        $variavel2 = $unn[0][uni_cnes];

        $rascnsusr = $tbAcoes->pegaUsrCns($usr->usr_codigo); //cns do executante
        $rascbousr = $tbAcoes->pegaUsrCbo($usr->esp_codigo); //cbo do executante

        $variavel3 = $rascbousr[0][cod_cbo];
        $variavel4 = $rascnsusr[0][cnes_cod_cns];

        $tbRaas = new Application_Model_FichaRaas();
        
        $salvaprontuario = ($this->_request->getPost("prontuario", FALSE) == "" ? $this->_request->getPost("recebeProntuarioValueHidden", FALSE) : $this->_request->getPost("prontuario", FALSE) );
        $pac = $tbRaas->pegaCns($salvaprontuario); //pega cns do paciente

        $variavel = $pac[0][ras_cns_paciente];

        $dataatual = date('d/m/Y');
        $dataano = date('Y');
        $datames = date('m');

        $dataconc =  $dataano . $datames;

        $data_do_beneficio = $this->_request->getPost("dataDoBeneficio");
        $qnt_total_do_proc = $this->_request->getPost("quantidadeTotalDoProcedimento");
        $qnt_de_procedimentos = $this->_request->getPost("procedimento");
        $proc_codigo_sus = $this->_request->getPost("procedimento_cod_sus");

        // print_r($qnt_de_procedimentos); die();

        if($qnt_de_procedimentos == NULL){
            $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao");
        }


        $qntacoes = 0;

        for ($i=0; $i < count($qnt_de_procedimentos) ; $i++) {
            $dadosacoes = array(

                "ras_codlinha_ad_acoes" => 16,
                "ras_prontuario" => $salvaprontuario, //salvaprontuario

                "ras_coduf" => $this->_request->getPost("ras_uf",FALSE), //codigo ibge da unidade da federação
                "ras_anomes" => $dataconc, //salva ano e mes yyyymm
                "ras_val_ini" => ($this->_request->getPost("data_inicio",FALSE) == "" ? $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao") : $this->_request->getPost("data_inicio",FALSE) ), //getdate atual
                "ras_cns" => $variavel, //paciente cartao sus
                "ras_cnes" => $variavel2, //unidade cnes
                //"ras_acao" => $qnt_de_procedimentos[$i], //codigo da ação no banco
                //"ras_acao" => $this->_request->getPost("proc_codigo_sus", FALSE), //codigo da ação no sus
                "ras_acao" => $proc_codigo_sus[$i],
                "ras_cbos_usr" => $variavel3, //cbo executante getusratual
                "ras_cns_usr" => $variavel4, //cns executante
                //"ras_dataexe" => $this->_request->getPost("dataexe",FALSE),//$data_do_beneficio[$i],
                "ras_obs" => $this->_request->getPost("ras_obs",FALSE),
                "ras_dataexe" => ($data_do_beneficio[$i] =="" ? $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao") : $data_do_beneficio[$i]),
                "ras_servico" => 115,
                "ras_class" => $this->_request->getPost("rasclass",FALSE),
                //"ras_qnt" => $this->_request->getPost("quantidade",FALSE),//$qnt_total_do_proc[$i],
                "ras_qnt" => $qnt_total_do_proc[$i] =="" ? $this->_redirect("atendimento/atendimento-simplificado/adicionar-acoes-raas?alert=errorfaltaacao") : $qnt_total_do_proc[$i] ,
                "ras_org" => $this->_request->getPost("origem_info",FALSE),
                "ras_local_realizacao" => $this->_request->getPost("local",FALSE),
                "ras_filler" => '    '

            );
            //echo "<pre>"; print_r($dadosacoes); die();
            $tbAcoes->salvar($dadosacoes);
            $qntacoes = $qntacoes + 1;
            // $this->render('adicionar-acoes-raas');
            $tbRaas->updateQntAcoes($salvaprontuario, $qntacoes);
        }
        //$tbRaas->updateQntAcoes($salvaprontuario, $qntacoes);
        $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");
            //return $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas");
    }


    public function excluirAcaoRaasAction(){
        $tbAcoes = new Application_Model_Acoes();

        $recebeIdAcao = $this->_request->getParam("acaoid");
        $x = $tbAcoes->deletaAcao($recebeIdAcao);

        $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2");
    }

    public function exportacaoRaasAction()
    {
        $this->view->title = "Exportação";

    }
    public function exportaRaasAction(){
        $volta = $this->_getParam("param",FALSE);
        //echo "<pre>";print_r($volta);die();
        if($volta==1) $this->_redirect("atendimento/atendimento-simplificado/index-ficha-raas#tabs3-3");
        $fichas = new Application_Model_FichaRaas();
        $usr = new Application_Model_Usuarios();
        $acoes = new Application_Model_Acoes();
        $exporta = new Application_Model_ExportacaoRaas();
        $data = $this->_request->getPost("competencia",FALSE);
        $explode = explode("/", $data);
        $tbProntMes = $explode[0];
        $tbProntAno = $explode[1];
        $rasdata = $tbProntAno . $tbProntMes;
        $dataexp = $tbProntAno . '-' . $tbProntMes;
        //$pegafichas = $fichas->pegaFichas($rasdata);    
        $pegaacoesexp = $acoes->contaProntuariosAcoes($dataexp);
        //$pegafichas = $fichas->pegaFichas($pegaacoesexp);
        //for ($i=0; $i <count($pegafichas) ; $i++) { 

            $atual = $usr->getUsrAtual();
            $rsp = $atual->uni_desc;
            $cgc = $atual->uni_cnpj;
            $sgl = $atual->uni_codigo;
            $temp = 0;
            $result1 = 0;
            $controle1 = $acoes->somaProcQnt($dataexp);

            $result2 = 0;
            foreach( $pegaacoesexp as $pront){
                $controle2 = $fichas->somaCnesCns($pront);
                $result2 = $result2 + $controle2[0][ras_cnes] + $controle2[0][ras_cns_paciente];
            }

            //$result2 = $controle2[0][ras_cnes] + $controle2[0][ras_cns_paciente];
            $sumResult = $result1 + $result2;
            $restoResult = $sumResult % 1111;
            $campocontrole = $restoResult + 1111;
            //echo "<pre>";print_r($todasacoes);die();

            $cabeca = array(
                "cbccodlinha" => '01',
                "cbchdr" => "#RAS#",
                "cbcmvm" => $rasdata,
                "cbclin" => count($pegaacoesexp),
                "cbcsmtvrf" => $campocontrole,
                "cbcrsp" => $rsp,
                "cbcsgl" => $sgl,
                "cbccgccpf" => $cgc,
                "cbcdst" => "SECRETARIA MUNICIPAL DE PALOTINA        ",
                "cbcdstin" => "M",
                "cbcdtger" => date('Ymd'),
                "cbcversao" => ($this->_request->getPost("versao",FALSE) =="" ? $this->_redirect("atendimento/atendimento-simplificado/exportacao-raas?alert=errorversao") : $this->_request->getPost("versao",FALSE) ), //errorversao
                "cbcbdversao" => ($this->_request->getPost("versaosia",FALSE) =="" ? $this->_redirect("atendimento/atendimento-simplificado/exportacao-raas?alert=errorbdsia") : $this->_request->getPost("versao",FALSE)) //errorbdsia
            );
            $montacabe = $exporta->montaCabecalho($cabeca);
            
        for ($i=0; $i <count($pegaacoesexp) ; $i++) { 
            $pegafichas = $fichas->pegaFichas($pegaacoesexp[$i]);
            //echo "<pre>"; print_r($pegafichas);die();
            $prontuario = $pegaacoesexp[$i][ras_prontuario];
            $contaacoes = $acoes->contaAcoes($prontuario,$dataexp);
            //die($contaacoes);
            $atual = $usr->getUsrAtual();
            $rsp = $atual->uni_desc;
            $cgc = $atual->uni_cnpj;
            $sgl = $atual->uni_codigo;
            $temp = 0;
            $result = 0;
            $controle1 = $acoes->somaProcQnt($prontuario);

            $todasacoes = $acoes->pegaTodasAcoes($prontuario,$dataexp);
            $result1 = 0;
            for ($j=0; $j < count($controle1) ; $j++) { 
                $temp = $controle1[$j][ras_qnt] + $controle1[$j][ras_acao];
                $result1 = $temp + $result1;
            }


            //echo "<pre>";print_r($cabeca);die();

            $salvaMsg[$i] = $exporta->montaTextos($pegafichas,$todasacoes);
            

            //echo "<pre>";print_r($salvaMsg);die();
        }
        //echo "<pre>";print_r($salvaMsg);die();
        $arq = $exporta->geraArquivo($montacabe,$salvaMsg,$data);

        $this->render('index-ficha-raas');
    }



}
