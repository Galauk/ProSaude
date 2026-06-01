    <?php

class Prontuario_AtendimentoController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->acl->copiarPermissao("zf/prontuario/index");
        Zend_Layout::getMvcInstance()->setLayout("prontuario");

    }

    public function indexAction() {
    	$obs = $this->_getParam("obs", FALSE);
        $io_codigo = $this->_getParam("io_codigo", FALSE);
        $ate_codigo = $this->_getParam("ate_codigo", FALSE);
        $usu_codigo_leito = $this->_getParam("usu_codigo", FALSE);
        $tbAte = new Application_Model_Atendimento();
        $tbUsr = new Application_Model_Usuarios();
        $tbLocal = new Application_Model_TbLocalAtend();
        $tbProc = new Application_Model_Procedimento();
        $tbTipoCond = new Application_Model_TbCdsTipoConduta();
        $tbUni = new Application_Model_Unidade();
         //echo "<pre>".print_r($_COOKIE,1).$ate_codigo."-".$io_codigo;die();
        if(empty($ate_codigo) && !empty($io_codigo)){ // CASO SEJA PRIMEIRA TELA DA INTERNAÇÃO ENTRA AQUI PARA LIMPAR OS COOKIES
            $_COOKIE['ate_reclamacao'] = "";
        }
        if (!$obs) {
            // traz o ultimo atendimento realizado e só é utilizado se não for um retorno
            $ate_dados = $tbAte->buscar();
            // traz o primeiro atendimento para verificar se é um retorno
            $ate_origem = $tbAte->buscaRetornoOrigem();
            // se nao for retorno carrega as informacoes do atendimento anterior na tela
            if ($ate_origem->ate_encaminhamento != "S") {
                $this->view->dados = $ate_dados;
                /*
                 * CASO NAO FOR RETORNO ELE MANDA TODAS INFORMACOES DO ATENDIMENTO PRA VIEW (UPDATE OU INSERT)
                 */
            } else {//senao manda a informacao para a view de que é um retorno
                /*
                 * PEGA O ATENDIMENTO QUE GEROU O RETORNO E COLOCA NO HIDDEN DA VIEW
                 * O SALVAR PEGA A INFORMAÇÃO DO HIDDEN POR POST E COLOCA NO ATe_CODIGO_ORIGEM
                 */
                $this->view->dados = (object) array("ate_encaminhamento" => $ate_dados->ate_encaminhamento,
                            "usu_nome" => $ate_dados->usu_nome,
                            "ate_hora" => date("H:i"));
            }
        } else {
            if ($ate_codigo) {
                $dados = (object) $tbAte->buscarInternacao($ate_codigo)->toArray();
                $this->view->dados = $dados;
            } else {
                $this->view->ate_hora = date("H:i");
            }
        }
        $this->view->usu_codigo_leito = $usu_codigo_leito;
        $this->view->obs = $obs;
        $this->view->io_codigo = $io_codigo;
        $tbConf = new Application_Model_Configuracao();
        if($tbConf->getConfig("GRUPO_DOENCAS") == 1){
            $tbGruDoen = new Application_Model_GrupoDeDoencas();
            $this->view->grupo_doencas = $tbGruDoen->listaGrupoDeDoencasCid();
        }
        // Válida CID10
        $tbConf = new Application_Model_Configuracao();
        if($tbConf->getConfig("CID_OBRIGATORIO")) {
            $this->view->cid_obrigatorio = true;
        }
         
        $tbCiap = new Application_Model_TbCiap();
        $rlAteCiap = new Application_Model_RlCdsAtendIndividualCiap();
        //echo "<pre>".print_r($ate_dados,1);die();
        $ate_dados;

        if($ate_dados->ate_codigo){
            $this->view->ciap_selecionados = $rlAteCiap->getCiapAtendimento($ate_dados->ate_codigo);
        }
        
        $this->view->usr_tipo_medico = $tbUsr->getUsrAtual()->usr_tipo_medico;
        $this->view->uni_tipo = $tbUsr->getUsrAtual()->uni_tipo;
        $this->view->ciap = $tbCiap->getCiaps($ate_dados);
        // Validação Dados Ficha Odontologica E-SUS
        if ($tbUsr->getUsrAtual()->usr_tipo_medico=="D") {
            $this->view->usr_tipo_medico = $tbUsr->getUsrAtual()->usr_tipo_medico;
            $tbTipVig = new Application_Model_TbCdsTipoVigSaudeBucal();
            $tbTipCond = new Application_Model_TbCdsTipoEncamOdonto();
            $this->view->vigilancia = $tbTipVig->getDados();
            $this->view->conduta = $tbTipCond->getDados();
            if ($ate_dados->ate_codigo) {
                $tbRlTipCond = new Application_Model_RlCdsAtendOdontoTipoEncam();
                $tbRlTipVig = new Application_Model_RlCdsAtendOdontTipVigBuc();
                $this->view->tipCond = $tbRlTipCond->getDadosPorAtendimento($ate_dados->ate_codigo)->toArray();
                $this->view->tipVig = $tbRlTipVig->getDadosPorAtendimento($ate_dados->ate_codigo)->toArray();
            }
        }
        $uni = $tbUni->getUnidade($tbUsr->getUsrAtual()->uni_codigo)->toArray();
        $this->view->cnes_tp_unid_id = $uni[0]['cnes_tp_unid_id'];
        $this->view->selectProcedimento = $tbProc->selectTagProcEsp($ate_dados->ate_codigo);
        // Inclusão select Locais e Condutas
        $this->view->selectLocais = $tbLocal->selectTag($ate_dados->co_local_atend);
        $this->view->conduta_ind = $tbTipoCond->getDados();
        $this->view->encaminhamentos = $tbTipoCond->getDadosEncaminhamento();
        if ($ate_dados->ate_codigo) {
            $tbRlAtenInd = new Application_Model_RlCdsAtendIndividualCondut();
            $this->view->tipCondInd = $tbRlAtenInd->getDadosPorAtendimento($ate_dados->ate_codigo)->toArray();
        }
        //die(var_dump("index: ".$ate_dados->usu_codigo));
        $isGest = $tbAte->getIdadeGestacional($ate_dados->usu_codigo);
        $tbUsu = new Application_Model_Usuario();
        $usuDados = $tbUsu->listaDadosUsuario($ate_dados->usu_codigo)->toArray();
        //die(var_dump($usuDados[0][pep_sexo]));
        $this->view->sexo = $usuDados[0][pep_sexo];
        //$this->view->isGest = $isGest;
        //die(var_dump("index: ".$isGest->ate_idade_gest));
        //die(var_dump($isGest->ate_idade_gest));
        if($isGest->ate_idade_gest != null && $isGest->ate_idade_gest != ""){
            $data1 = new DateTime( $isGest->ate_data );
            $data2 = new DateTime( date("Y-m-d") );
            
            $intervalo = $data1->diff( $data2 );
            if($intervalo->days >= 7){
                $idade_gest = $isGest->ate_idade_gest + ((int)($intervalo->days/7));
                if($idade_gest <= 44){
                    $this->view->idade_gest = $idade_gest;
                    $this->view->ate_gravida = 'S';
                } else {
                    $this->view->idade_gest = -1; //idade gestacional acabou = -1
                    $this->view->ate_gravida = 'N'; 
                }
            } else {
                $this->view->idade_gest = $isGest->ate_idade_gest;
                $this->view->ate_gravida = 'S';
            }

        } else {
            $this->view->idade_gest = 0; //não está grávida
            $this->view->ate_gravida = 'N';
        }

        // echo "<pre>"; print_r($ate_dados); exit;

        $this->view->ate_hipotese_diagnostico = $ate_dados->ate_hipotese_diagnostico;

        $tbEr = new Application_Model_EstratificacaoRisco();
        //die(var_dump("here"));
        $this->view->grupo1 = $tbEr->getValoresPorGrupo(1);
        $this->view->grupo2 = $tbEr->getValoresPorGrupo(2);
        $this->verificaEstratificacaoRisco($ate_dados->usu_codigo);
        $this->render($tbAte->textareaUnico() ? "unico" : "multiplo");

    }

    public function verificaEstratificacaoRisco($usu_codigo){
        $tbUsu = new Application_Model_Usuario();
        $tbAte = new Application_Model_Atendimento();
        $usu_dados = $tbUsu->getDados($usu_codigo);
        $from = new DateTime($usu_dados->usu_datanasc);
        $to   = new DateTime('today');
        $idade = $from->diff($to)->y;
        $est_risco = $tbAte->getEstratificacaoRisco($usu_codigo);
        //die(var_dump($from->diff($to)->y));
        $sit_desc = "";
        //VERIFICA SE PACIENTE ESTA ACIMA DE 65ANOS
        if(($from->diff($to)->y) >= 65 ){
            //die(var_dump("here"));
            $this->view->is_grupo_1 = 1;
            $sit_desc = "Idoso";
        } else {
            $this->view->is_grupo_1 = 0;
        } 
        //VERIFICA SE PACIENTE GRUPOS 2
        if(($from->diff($to)->y) <= 1) {
            //die(var_dump("here"));
            //die(var_dump($from->diff($to)->y));
            $this->view->is_grupo_2 = 1;
            if($sit_desc != "") $sit_desc = $sit_desc . ', ';
            $sit_desc  = $sit_desc . "Abaixo de 1 ano";
        } else if($tbAte->isGestante($usu_codigo) == true) {
            $this->view->is_grupo_2 = 1;
            if($sit_desc != "") $sit_desc = $sit_desc . ', ';
            $sit_desc  = $sit_desc . "Gravida";
        } else {
            $this->view->is_grupo_2 = 0;
        } 

        if($tbAte->verificaDoencaCronica($usu_codigo) == true) {
            $this->view->is_grupo_2 = 1;
            if($sit_desc != "") $sit_desc = $sit_desc . ', ';
            $dcs = $tbAte->getDoencaCronica($usu_codigo);
            //die(var_dump(sizeof($dcs)));
            if(sizeof($dcs) <= 1){
                $sit_desc  = $sit_desc . $dcs->ds_ciap;
            } else {
                $i = 0;
                foreach ($dcs as $key => $dc) {
                    if($i == 0){
                        $sit_desc  = $sit_desc . $dc->ds_ciap;
                    } else {
                        $sit_desc  = $sit_desc . ", " . $dc->ds_ciap;
                    }
                    $i++;
                }
            }
           // $sit_desc  = $sit_desc . "Doença Cronica";
            
        }

        $this->view->sit_desc = $sit_desc ;

        if($est_risco != "" && $est_risco != null){
            if($est_risco->ate_estratificacao_risco_g1 != null && $est_risco->ate_estratificacao_risco_g1 != ""){
               $this->view->grupo_val_g1 = $est_risco->ate_estratificacao_risco_g1;
            }
            if($est_risco->ate_estratificacao_risco_g2 != null && $est_risco->ate_estratificacao_risco_g2 != ""){
               $this->view->grupo_val_g2 = $est_risco->ate_estratificacao_risco_g2;
            }
        }
        // echo $from->diff($to)->y;
        //die(var_dump($from->diff($to)->y));
    }
    
    public function listaCidsAtendimentoAction(){
        $tbAte = new Application_Model_Atendimento();
        $codAtend = $this->_request->getPost("codAtend");
        $this->view->dados = $tbAte->listaCidsAtendimento($codAtend)->toArray();
        return $this->render("dados",NULL,TRUE);
    }
    
    public function getCidAtendimentoAction(){
        $age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
        $tbAte = new Application_Model_Atendimento();
        $codAtend = $tbAte->getCodigoAtendimentoPorAgendamento($age_codigo)->ate_codigo;
        $cidCodigo = $tbAte->listaCidsAtendimento($codAtend)->cd10_codigo;
        $cidCodigo_novo = $tbAte->listaCidsAtendimento($codAtend)->cd10_codigo_cid;
        $cidDesc = $tbAte->listaCidsAtendimento($codAtend)->cd10_codigo_desc;
        $dadosCid = array (
            "cidCodigo" => $cidCodigo,
            "cidDesc" => $cidDesc ,
            "cidCodigoNovo" => $cidCodigo_novo 
        );
        $this->view->dados = $dadosCid;
        return $this->render("dados",NULL,TRUE);
    }
    
    public function atualizarCidsAction(){
        $tbAte = new Application_Model_Atendimento();
        $contCid = 0;
        $dadosAtuCids = array(
            "ate_codigo" =>  $this->_request->getPost("ate_codigo"),
        );
        $dadosAte = $tbAte->verificaCidsLivres($this->_request->getPost("ate_codigo"));
        //Verifica qual campo cid está livre e atualiza apenas o primeiro que estiver
        if (empty($dadosAte->cd10_codigo)){
            if ($contCid == 0){
                $dadosAtuCids["cd10_codigo"] = $this->_request->getPost("cd10_codigo"); 
                $tbAte->atualizaCids($dadosAtuCids);
                $contCid++;
            }
        }
        if (empty($dadosAte->cd10_codigos)){
            if ($contCid == 0){
                $dadosAtuCids["cd10_codigos"] = $this->_request->getPost("cd10_codigo"); 
                $tbAte->atualizaCids($dadosAtuCids);
                $contCid++;
            }
        }
        if (empty($dadosAte->cd10_codigot)){
            if ($contCid == 0){
                $dadosAtuCids["cd10_codigot"] = $this->_request->getPost("cd10_codigo"); 
                $tbAte->atualizaCids($dadosAtuCids);
                $contCid++;
            }
        }
        return $this->render("dados",NULL,TRUE);
    }
    
    public function excluirCidsAction(){
        $tbAte = new Application_Model_Atendimento();
        $codAte = $this->_request->getPost("ate_codigo");
        $codCid = $this->_request->getPost("cd10_codigo");
        $qtdRegCid10 = $tbAte->getQtdRegistrosAtendCid10($codAte,$codCid)->qtd_reg_cid10;
        $qtdRegCid10s = $tbAte->getQtdRegistrosAtendCid10s($codAte,$codCid)->qtd_reg_cid10s;
        $qtdRegCid10t = $tbAte->getQtdRegistrosAtendCid10t($codAte,$codCid)->qtd_reg_cid10t;
        if ($qtdRegCid10 == 1){
            $dadosAtuCids = array(
                "ate_codigo" =>  $codAte,
                "cd10_codigo" => NULL
            );
        }
        if ($qtdRegCid10s == 1){
            $dadosAtuCids = array(
                "ate_codigo" =>  $codAte,
                "cd10_codigos" => NULL
            );
        }
        if ($qtdRegCid10t == 1){
            $dadosAtuCids = array(
                "ate_codigo" =>  $codAte,
            "cd10_codigot" =>  NULL
            );
        }
        $tbAte->atualizaCids($dadosAtuCids);
        return $this->render("dados",NULL,TRUE);
    }


    public function atendimentoInternacaoAction() {
        $obs = $this->_getParam("obs", FALSE);
        $tbAte = new Application_Model_Atendimento();
        $this->view->dados = $tbAte->buscar();
        $this->view->obs = $obs;
        $this->render($tbAte->textareaUnico() ? "unico" : "multiplo");
    }

    public function salvarAction() {

        if ($this->_request->isPost()) {
            $tbConf = new Application_Model_Configuracao();

            if($tbConf->getConfig("GRUPO_DOENCAS") == 1){
                $tbGruDoen = new Application_Model_GrupoDeDoencas();
                $this->view->grupo_doencas = $tbGruDoen->listaGrupoDeDoencasCid();
            }
            
            $obs = $this->_getParam("obs", FALSE);
            $json = $this->_request->getPost("json", FALSE);
            
            $dadosCid = $this->_request->getPost("cid_codigo", NULL);
            //cd10_codigo, cd10_codigos, cd10_codigot 
            
            $dados = array(
                "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),

                "ate_hora" => $this->_request->getPost("ate_hora", NULL),
                "ate_reclamacao" => $this->_request->getPost("ate_reclamacao", NULL),
                "ate_acidentetrab" => $this->_request->getPost("ate_acidentetrab", FALSE),
                "cd10_codigo" => $dadosCid[0],
                "cd10_codigos" => $dadosCid[1],
                "cd10_codigot" => $dadosCid[2],
                "gd_codigo" => $this->_request->getPost("grupo_doencas",FALSE),
                //"cd10_codigos" => '5366',
                //"cd10_codigot" => '5366',
                "ate_exame_fisico" => $this->_request->getPost("ate_exame_fisico", NULL),
                "ate_diagnostico" => $this->_request->getPost("ate_diagnostico", NULL),
                "ate_tratamento" => $this->_request->getPost("ate_tratamento", NULL),
                "ate_curativo" => $this->_request->getPost("ate_curativo", NULL),
                // tipo de atendimento
                "ate_puericultura" => $this->_request->getPost("ate_puericultura", 'F'),
                "ate_pre_natal" => $this->_request->getPost("ate_pre_natal", 'F'),
                "ate_cancer" => $this->_request->getPost("ate_cancer", 'F'),
                "ate_dst" => $this->_request->getPost("ate_dst", 'F'),
                "ate_diabetes" => $this->_request->getPost("ate_diabetes", 'F'),
                "ate_hipertensao" => $this->_request->getPost("ate_hipertensao", 'F'),
                "ate_hanseniase" => $this->_request->getPost("ate_hanseniase", 'F'),
                "ate_tuberculose" => $this->_request->getPost("ate_tuberculose", 'F'),
                "ate_outros" => $this->_request->getPost("ate_outros", 'F'),
                "age_codigo" => ($this->_request->getPost("age_codigo") != NULL ? $this->_request->getPost('age_codigo', NULL) : ""),
                "ate_data" => ($this->_request->getPost("ate_data") != NULL ? $this->_request->getPost('ate_data', NULL) : "NOW()"),
                "co_local_atend" => $this->_request->getPost("co_local_atend", FALSE),
                "ate_somente_procedimento" => $this->_request->getPost("ate_somente_procedimento", FALSE),
                "ate_hipotese_diagnostico" => $this->_request->getPost("ate_hipotese_diagnostico", FALSE)
                //"ate_data" => '14/02/2014'
            );

            if($this->_request->getPost("is_grupo_1") == 1) {
                $dados["ate_estratificacao_risco_g1"] = $this->_request->getPost("er_grupo_1", FALSE);
            } else {
                $dados["ate_estratificacao_risco_g1"] = "";
            }
            if($this->_request->getPost("is_grupo_2") == 1) {
                //die(var_dump($this->_request->getPost("er_grupo_2")));
                $dados["ate_estratificacao_risco_g2"] = $this->_request->getPost("er_grupo_2", FALSE);
            } else {
                $dados["ate_estratificacao_risco_g1"] = "";
            }

            /*Definição se gestante ou não para salvar idade gestacional válida no atendimento*/
            if($this->_request->getPost("is_gest_status") == 'S' && $this->_request->getPost("ate_gest_intrp") == '0'){
               // die(var_dump($this->_request->getPost("is_gest")));
                //array_push($dados, ["ate_idade_gest" => $this->_request->getPost("is_gest")]);
                $dados["ate_idade_gest"] = $this->_request->getPost("ate_idade_gest");
            } else {
                //array_push($dados, ["ate_idade_gest" => ""]);
                //die(var_dump("here"));
                $dados["ate_idade_gest"] = null;
                // if($tbAte->verificaDoencaCronica($usu_codigo) == false){
                //     $dados["ate_estratificacao_risco_g2"] = "";
                // }
                //$dados["ate_estratificacao_risco_g2"] = "";
                //die(var_dump($dados));
            } 

            //die(var_dump($dados));
            /*
             * se nao for um retorno pode colocar ate_codigo e dar update
             * o if ta duplicado pois, primeiramente é necessário veriricar se tem o retorno ou nao para
             * colocar o ate_codigo no campo hidden, caso colocar ele vai considerar como um update e como o retorno
             * precisa de um novo atendimento é necessário que o mesmo não vá para a view
             * 
             * O segundo if tem que ser depois do método SALVAR do atendimento pois precisa do ate_codigo após salvar para
             * salvar um retorno.
             */
            $tbAte = new Application_Model_Atendimento();
            //
            $ate_origem = $tbAte->buscaRetornoOrigem();
            if ($ate_origem->ate_encaminhamento != "S") {
                $ate_codigo_array = array("ate_codigo" => $this->_request->getPost("ate_codigo", NULL));
                $dados = array_merge($ate_codigo_array, $dados);
            }
            /**
             * terá que ter um if igual no final do metodo pois se o else fosse aqui ele nao salvaria caso nao fosse retorno.
             * esse if é necessário para saber se eu coloco a PK ate_codigo ou não na hora de salvar.
             * By: Victor Marques
             */
            try {
                $tbRet = new Application_Model_Retorno();

                /*
                 * antes de salvar um novo atendimento ele fexa o retorno do atendimento anterior
                 * caso tenha um novo retorno deve-se finalizar como um retorno
                 */
                $ate_codigo = $tbAte->salvar($dados, $obs);
                $this->salvaCiap($this->_request->getPost("ciap-selecionados", 'F'),$ate_codigo);
                $this->salvarVigilanciaBucal($this->_request->getPost("vigilancia"),$ate_codigo);
                $this->salvarCondutas($this->_request->getPost("conduta"),$ate_codigo);

                // Salva procedimento_atendimento {
                    $tbAteProc = new Application_Model_ProcedimentoAtendimento();
//die("asdfasdf");
                    // Deleta todos os atendimentos do atendimento evitando duplicar procedimento
                    $tbAteProc->excluirProcedimentosAtendimento($ate_codigo);
                    
                    $proc_codigo = $_POST["proc_codigo"];
                    $logon = new Zend_Session_Namespace("logon");
                    $usr = $logon->usr->usr_codigo;

                    $controleCid = 0;
            if($dadosCid) {
                    foreach ($dadosCid as $codigoCid) {
                        
                        $codigoCid = $dadosCid[$controleCid];
                        $controleCid += 1;
                        
                        $dadosProcAte = array(
                            "ate_codigo" => $ate_codigo,
                            "proc_codigo" => $proc_codigo, 
                            "usr_codigo" => $usr,
                            "cd10_codigo" => $codigoCid
                        );
                    //var_dump($dadosProcAte); die();
                        $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);
                    }
             } else {
                        $dadosProcAte = array(
                            "ate_codigo" => $ate_codigo,
                            "proc_codigo" => $proc_codigo, 
                            "usr_codigo" => $usr
                        );
                    //var_dump($dadosProcAte); die();
                        $tbAteProc->salvarProcedimentosAtendimento($dadosProcAte);

             }


                // Salva dados conduta
                if ($this->_request->getPost("conduta_ind","F")) {
                    $this->salvarCondutasInd($this->_request->getPost("conduta_ind","F"),$ate_codigo);
                }
                $_COOKIE[ate_reclamacao] = "";
                $tbAge = new Application_Model_Agendamento();
                if ($ate_origem->ate_encaminhamento == "S") {
                    $dados_retorno = array("ate_codigo_origem" => $ate_origem->ate_codigo,
                        "ate_codigo" => $ate_codigo);
                    $tbRet->salvar($dados_retorno);
                    $_COOKIE[ate_reclamacao] = "";
                }
                if ($obs) {
                    $dadosInternacao = array(
                        "ate_codigo" => $ate_codigo,
                        "io_codigo" => $this->_getParam("io_codigo", FALSE)
                    );
                    $tbAti = new Application_Model_AtendimentoInternacao();
                    $atin_codigo = $tbAti->salvar($dadosInternacao);
                    $this->view->dados = $tbAte->buscarInternacao($ate_codigo);
                } else {
                    $this->view->dados = $tbAte->buscar();
                }
                if ($json) {
                   return $this->jsonFicha($ate_codigo);
                }
                $this->view->dialog = array("Confirmação", "Atendimento salvo com sucesso!", 300, 140);
                $this->view->obs = $obs;
                if($this->_request->getPost("ate_somente_procedimento", FALSE) == "f" || $this->_request->getPost("ate_somente_procedimento", FALSE) == "" || $this->_request->getPost("uni_tipo", FALSE) == "H")
                    $this->_redirect("/prontuario/atendimento");
                else
                    $this->_redirect("/prontuario/procedimento");
                
            } catch (Zend_Validate_Exception $exc) {
                if ($json) {
                    $this->view->dados = array("error" => TRUE, "mensagem" => $exc->getMessage());
                    return $this->render("dados", NULL, TRUE);
                }
                $this->view->erro = $exc->getMessage();
                $this->view->dados = $dados;
                $this->render($tbAte->textareaUnico() ? "unico" : "multiplo");
            }
        } else {
            $this->_redirect("/prontuario/atendimento");
        }
    }
    
    private function salvarCondutasInd($post=FALSE,$ateCod=FALSE){
        $tbCond = new Application_Model_RlCdsAtendIndividualCondut();
        $tbCond->excluirPorAtendimento($ateCod);
        foreach ($post as $val) {
            $dados = "";
            $dados = array(
                "ate_codigo" => $ateCod,
                "tp_cds_conduta" => $val 
            ); 
            try{
                $tbCond->salvar($dados);
                return true;
            } catch (Exception $exc) {
                return $exc->getMessage();
            }
        }
    }
    
    private function salvarCondutas($post=FALSE,$ateCod=FALSE){
        $tbTipEnc = new Application_Model_RlCdsAtendOdontoTipoEncam();
        $tbTipEnc->excluirPorAtendimento($ateCod);
        foreach ($post as $val) {
            $condutas = "";
            $condutas = array(
                "ate_codigo" => $ateCod,
                "tp_cds_encam_odonto" => $val 
            ); 
            try{
                $tbTipEnc->salvar($condutas);
            } catch (Exception $exc) {
                return $exc->getMessage();
            }
        }
    }
    
    private function salvarVigilanciaBucal($post=FALSE,$ateCod=FALSE){
        $tbRlVig = new Application_Model_RlCdsAtendOdontTipVigBuc();
        $tbRlVig->excluirPorAtendimento($ateCod);
        foreach ($post as $val) {
            $vigilancia = "";
            $vigilancia = array(
                "ate_codigo" => $ateCod,
                "tp_cds_vig_saude_bucal" => $val 
            );
            try{
                $tbRlVig->salvar($vigilancia);
            } catch (Exception $exc) {
                return $exc->getMessage();
            }
        }
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
    
        private function jsonFicha($id) {
		$tbAte = new Application_Model_Atendimento();
		
                $ate = $tbAte->getDetalhes($id);

		if (!$ate)
			return $this->_redirect("/prontuario/atendimento");

		$this->view->dados = $ate->toArray();
		$this->render("dados", NULL, TRUE);
	}

    public function salvarInternacaoAction() {

        if ($this->_request->isPost()) {
            
            $obs = $this->_getParam("obs", FALSE);

            $dados = array(
                "ate_codigo" => $this->_request->getPost("ate_codigo", NULL),
                "ate_hora" => $this->_request->getPost("ate_hora", NULL),
                "ate_reclamacao" => $this->_request->getPost("ate_reclamacao", NULL),
                "cd10_codigo" => $this->_request->getPost("cd10_codigo", NULL),
                "ate_atendido" => $this->_request->getPost("ate_atendido", "S")
            );
            $tbAte = new Application_Model_Atendimento();
            $io_codigo = $this->_getParam("io_codigo", FALSE);

            $ate_codigo = $tbAte->salvar($dados, $obs, $io_codigo);
            
            if (!$this->_request->getPost("ate_codigo")) {
                $dadosInternacao = array(
                    "ate_codigo" => $ate_codigo,
                    "io_codigo" => $io_codigo
                );
                $tbAti = new Application_Model_AtendimentoInternacao();
                $atin_codigo = $tbAti->salvar($dadosInternacao);
            }
            $this->view->dados = $tbAte->buscarInternacao($ate_codigo);

            $this->view->dialog = array("Confirmação", "Atendimento salvo com sucesso!", 300, 140);
            $this->view->obs = $obs;

            $this->_redirect("/leito/atendimento/index/cod/$io_codigo/ate_codigo/$ate_codigo");
        } else {
            $this->_redirect("/prontuario/atendimento");
        }
    }

    public function historicoAction() {
        $tbAte = new Application_Model_Atendimento();

        // filtrar 
        $this->view->term = $this->_getParam("term", FALSE);
        $this->view->itens = $tbAte->getHistorico($this->view->term);
    }

    /**
     * Esta é a página com as abas do atendimento (exames, atendimento, medicamento...)
     */
    public function verAction() {
        $ate_codigo = $this->_getParam("id", FALSE);
        $age_codigo = $this->_getParam("age", FALSE);
        //die($age_codigo. "zz".$ate_codigo);
        $tbAte = new Application_Model_Atendimento();

        if ($age_codigo) {
            if ($ate_codigo) {
                $this->view->ate = $tbAte->buscar($ate_codigo);
                $this->view->ate_codigo = $ate_codigo;
            } else {
                $ate = $tbAte->temAtendimento($age_codigo);
                if ($ate) {
                    $ate_codigo = $ate->ate_codigo;
                    $this->view->ate = $tbAte->buscar($ate_codigo);
                    $this->view->ate_codigo = $ate_codigo;
                } else {
                    $tbPre = new Application_Model_PreConsulta();
                    $this->view->ate = $tbPre->buscar($age_codigo);
                }
            }
        } else {
            return $this->_redirect("/prontuario");
        }
    }

    /**
     * Esta é a página com os dados preenchidos na guia atendimento
     */
    public function detalhesAction() {
        $ate_codigo = $this->_getParam("id", FALSE);
        if (!$ate_codigo)
            return $this->_redirect("/prontuario");

        $tbAte = new Application_Model_Atendimento();
        $this->view->dados = $tbAte->getDetalhes($ate_codigo);
        $this->render($tbAte->textareaUnico() ? "detalhes-unico" : "detalhes-multiplo");
    }

    /**
     * Busca os dados do atendimento e retorna em json
     * Usado no modulo agenda para preencher a tela com os dados do atendimento
     */
    public function jsonAction() {
        $ate_codigo = $this->_getParam("ate", FALSE);
        if (!$ate_codigo)
            return $this->_redirect("/prontuario/atendimento");

        $tbAte = new Application_Model_Atendimento();
        $age = $tbAte->getDadosCabecalho($ate_codigo);

        if (!$age) {
            $this->view->dados = array("success" => FALSE, "mensagem" => "Código inválido");
            return $this->render("dados", NULL, TRUE);
        }

        $age = $age->toArray();

        $tbReq = new Application_Model_RequisicaoExame();
        $exames = $tbReq->getItens(FALSE, $ate_codigo)->toArray();

        // monta resultados
        $this->view->dados = array_merge(
                array("success" => TRUE), $age, array("exames" => $exames)
        );
        return $this->render("dados", NULL, TRUE);
    }

    public function verificaseestaematendimentoAction() {
		setcookie("ate_reclamacao", ' ') ;
		unset($_COOKIE["ate_reclamacao"]);
		setcookie("ate_exame_fisico", ' ') ;
		unset($_COOKIE["ate_exame_fisico"]);
		setcookie("ate_diagnostico", ' ') ;
		unset($_COOKIE["ate_diagnostico"]);
		setcookie("ate_tratamento", ' ') ;
		unset($_COOKIE["ate_tratamento"]);
		setcookie("ate_curativo", ' ') ;
		unset($_COOKIE["ate_curativo"]);
        $tbUsr = new Application_Model_Usuarios;
        $age_codigo = $this->_getParam("age_codigo", FALSE);
        $tbAge = new Application_Model_Agendamento();

        if ($tbUsr->isMedico()) {
            $tbAte = new Application_Model_Atendimento();

            //$ate_codigo = $this->_getParam("ate_codigo", FALSE);
            if ($age_codigo) {
                $ate = $tbAte->estaEmAtendimento($age_codigo);

                if ($ate) {
                    $med_codigo = $ate->med_codigo;
                }
            }

            //if($med_codigo == 99999 || $med_codigo == 99998){

            $tbUsr = new Application_Model_Usuarios();
            $usr = $tbUsr->getUsrAtual();

            $tbAge->alteraMedico($age_codigo, $usr->usr_codigo, "E");
            die(TRUE);
            //prontuario/index/iniciar/cod/"+cod;
            //}else{
            //die(TRUE);
            //}
        } else {
            $tbAge->alteraSituacao("E", $age_codigo);
            die(true);
        }
    }
    
    public function excluirAction(){
        $id = (int) $this->_getParam("id", 0);
                
		$tbAte = new Application_Model_Atendimento();
		$tbAte->excluir($id);

		if ($this->_getParam("json", FALSE)) {
			$this->view->dados = array("success" => TRUE);
			return $this->render("dados", NULL, TRUE);
		}

		return $this->render("dados", NULL, TRUE);
    }
        
    public function buscarCiapAction(){
        $term = $this->_getParam("term", FALSE);
        $tbCiap = new Application_Model_TbCiap();
        if($term){
            $this->view->dados = $tbCiap->buscar($term);
        }
        
        return $this->render("dados",null,true);
    }

    #novaBUsca
    public function novoBuscarCiapAction() {
        $ciapSelecionados = $this->_getParam("selecionados", FALSE);

        $term = $this->_getParam("term", FALSE);
        $tbCiap = new Application_Model_TbCiap();
        $out = $tbCiap->buscarCiapDescricoes($term,$ciapSelecionados);

        $this->view->dados = $out;
        return $this->render("dados",NULL,TRUE);
    }
    
}