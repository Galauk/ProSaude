<?php

class PacienteController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->acl->allow(NULL,array("buscar-domicilio","buscar-usuarios","buscar-rua","buscar-cep","buscar","buscar-usuario-relatorio","buscar-ocupacao","busca-estado-por-pais","busca-cidade-por-estado","esus-form-paciente-cns"));
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.maskedinput-1.3.min.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/default/paciente/form-paciente.js');
    }

    public function buscarDomicilioAction() {
        $term = $this->_getParam("term", FALSE);
        $tbDom = new Application_Model_Domicilio();
        $this->view->dados = $tbDom->buscaDomicilio($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarUsuariosAction() {
        $tbUsu = new Application_Model_Usuario();
        $term = $this->_getParam("term", FALSE);
        
        $this->view->dados = $tbUsu->buscar($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function getEnderecoUsuarioAction() {
        $tbDom = new Application_Model_Domicilio();
        $cod = $this->_request->getPost("cod", FALSE);
        // die($cod);
        $this->view->dados = $tbDom->getEnderecoPorUsuario($cod);
        return $this->render("dados", NULL, TRUE);
    }

    public function recuperaDadosDaGestacaoAction(){
        $this->_helper->layout->disableLayout();
        $tbUsu = new Application_Model_Usuario();
        $id = $this->_getParam("id", FALSE);
        $resultadoDasColunasDoPreNatal = $tbUsu->recuperaDadosDaGestacao($id);
        if ($resultadoDasColunasDoPreNatal != NULL) {
            // echo "<pre>";print_r($resultadoDasColunasDoPreNatal);die();
            echo json_encode($resultadoDasColunasDoPreNatal);
            exit();
        } else{
            http_response_code(404);
            exit();
        }
        return $this->render("dados", NULL, TRUE);

    }

    public function buscarIdadeSexoAction() {
        $this->_helper->layout->disableLayout();
        $tbUsu = new Application_Model_Usuario();
        $id = $this->_getParam("idUsuario", FALSE);
        $datanascimento = $this->_getParam("dataNascimento", FALSE);
        $idade = null;
        // $resultado = $tbUsu->validaSexoFeminino($id);
        // echo "<pre>";print_r($resultado);die();
        
        // Separa em dia, mês e ano
        list($dia, $mes, $ano) = explode('/', $datanascimento);
       
        // Descobre que dia é hoje e retorna a unix timestamp
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
       
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        // echo "<pre>";print_r($idade);die();
        if ($idade > 9 && $idade <= 60) {
            $resultadoGestante = $tbUsu->checaGestante($id);
            // echo "<pre>";print_r($resultado);die();
            if ($resultadoGestante[usu_esta_gestante] == 1) {
                $resultadoColunas = $tbUsu->recuperaDadosDaGestacao($id);
                echo json_encode($resultadoColunas);
            } else{
                echo json_encode(FALSE);
            }
        } else{
            echo json_encode(FALSE);
        }
    
        exit();
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarNumerosDeDomicilioPorEnderecoAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $rua_codigo = $this->_getParam("rua_codigo", FALSE);
        $rua_cep = $this->_getParam("rua_cep", FALSE);
        $rua_bairro = $this->_getParam("rua_bairro", FALSE);
        $dom_numero = $this->_getParam("dom_numero", FALSE);
        $cid_codigo = $this->_getParam("cid_codigo", FALSE);
        $rua_nome = $this->_getParam("rua_nome", FALSE);
        $usu_codigo_responsavel = $this->_getParam("usu_codigo_responsavel", FALSE);
        $rua_cep = str_replace("-", "", str_replace(".", "", $rua_cep));
        $co_tipo_logradouro = $this->_getParam("co_tipo_logradouro", FALSE);
        $tbDom = new Application_Model_Domicilio();
        $this->view->dados = $tbDom->buscarNumerosDeDomicilioPorEndereco($rua_codigo, $rua_cep, $rua_bairro, $dom_numero, $co_tipo_logradouro,$cid_codigo,$rua_nome,$usu_codigo_responsavel)->toArray();
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarRuaAction() {
        $tbConf = new Application_Model_Configuracao();
        $tbRua = new Application_Model_Rua();
        $term = $this->_getParam("term", FALSE);
        $this->view->dados = $tbRua->buscarRua($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarCepAction() {
        $tbConf = new Application_Model_Configuracao();
        $tbRua = new Application_Model_Rua();
        $term = $this->_getParam("term", FALSE);
        $this->view->dados = $tbRua->buscarCep($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function getProntuarioDuplicadoAction() {
        $prontuario = $this->_getParam("prontuario", FALSE);
        $tbPep = new Application_Model_Usuario();
        $num = $tbPep->getProntuarioDuplicado($prontuario);
        $this->view->dados = $num->num;
        return $this->render("dados", NULL, TRUE);
    }

    public function buscaEstadoPorPaisAction() {
        $pais_codigo = $this->_request->getPost("pais_codigo");
        //$pais_codigo = $this->_getParam("pais_codigo",FALSE);
        $tbEst = new Application_Model_Estado();
        $this->view->dados = $tbEst->listaEstadoPorPais($pais_codigo)->toArray();


        return $this->render("dados", NULL, TRUE);
    }

    public function buscaCidadePorEstadoAction() {
        $ufCodigo = $this->_getParam("uf_codigo", FALSE);
        $tbCid = new Application_Model_Cidade();
        //$this->view->dados = $tbCid->fetchAll("uf_codigo = '$uf_codigo'","cid_nome")->toArray();
        $this->view->dados = $tbCid->listaCidadePorEstadoCodigo($ufCodigo)->toArray();
        return $this->render("dados", NULL, TRUE);
    }

    public function validaCnsDuplicadoAction(){
        $tbUsu = new Application_Model_Usuario();
        $cns = $this->_request->getPost("cns");
        $this->view->dados = $tbUsu->validaCnsDuplicado($cns)->qtd_sus;
        return $this->render("dados",NULL,TRUE);
    }

    public function formPacienteAction() {
        $this->view->title = "Cadastro de Pessoa";
        $this->_helper->layout->setLayout("simples");
        
        $tbEsC = new Application_Model_EstadoCivil();
        $tbPais = new Application_Model_Pais();
        $tbEst = new Application_Model_Estado();
        $tbCid = new Application_Model_Cidade();
        $tbUni = new Application_Model_Unidade();
        $tbRac = new Application_Model_Raca();

        $tbOcu = new Application_Model_Ocupacao();
        $tbEsco = new Application_Model_Escolaridade();
        $tbSitF = new Application_Model_SituacaoFamiliar();
        $tbPes = new Application_Model_PessoaPaciente();
        $tbConf = new Application_Model_Configuracao();
        $tbRua = new Application_Model_Rua();
        $tbUsu = new Application_Model_Usuario();
        $tbUsr = new Application_Model_Usuarios();
        $tbPergDet = new Application_Model_TbPerguntaDetalhe();

        $tbUsuDef = new Application_Model_UsuarioDeficiencias();
        $tbUsuDoenca = new Application_Model_UsuarioDoencas();

        $codIbge = $tbConf->getConfig("CID_CODIGO_IBGE");
        $tbTpLogr = new Application_Model_TbMsTipoLogradouro();
        // Valida se o cadastro é do aise ou de usuário
        $aise = $tbConf->getDadosConfigPelaChave("CADASTRO_AISE")->conf_valor_bool;
        $this->view->aise = $aise;
        $prontuarioObrigatorio = $tbConf->getDadosConfigPelaChave("PRONTUARIO_OBRIGATORIO")->conf_valor_bool;
        $this->view->prontuarioObrigatorio = $prontuarioObrigatorio;
        $this->view->exibirCiscomcam = $tbConf->getDadosConfigPelaChave("EXIBIR_CISCOMCAM")->conf_valor_bool;
        $pessoa = $this->_getParam("pessoa", FALSE);
        // die($pessoa);
        $dadosEstCid = array(
            "codCid" => $tbRua->getDadosCidadeEstado($codIbge)->cid_codigo,
            "ufSigla" => $tbRua->getDadosCidadeEstado($codIbge)->uf_sigla
        );
        $this->view->dadosEstCid = $dadosEstCid;
        $this->view->poupup = $this->_getParam("poupup", FALSE);
        if ($this->view->poupup == 1) {
            $pessoa_paciente = 1; //isso faz com que o lista dados faça busca pelo código de pessoa paciente e não de pessoa
        }
        // Lista dados para edição ou visualização Usuario ou Pessoa Aise
        if (!empty($pessoa)) {
            if ($aise) {
                $this->view->dados = $tbPes->listaDadosPessoa($pessoa, $pessoa_paciente);
            } else {
                $this->view->dados = $tbUsu->listaDadosUsuario($pessoa);
                $this->view->deficienciasEdit = $tbUsuDef->getDadosPorUsuario($pessoa)->toArray();
                $this->view->doencasEdit = $tbUsuDoenca->getDadosPorUsuario($pessoa)->toArray();
            }
        }
        $this->view->tp_lograd = $tbTpLogr->getTiposLogradouro();
        //$this->view->unidade = $tbUni->getUnidades();
        $this->view->usuarios = $tbUsr->getUsuariosModulo();

        $this->view->estadoCivil = $tbEsC->fetchAll();
        $this->view->pais = $tbPais->fetchAll();
        $this->view->estado = $tbEst->fetchAll();
        $this->view->cidade = $tbCid->listaCidadePorEstado($dadosEstCid["ufSigla"]);
        $this->view->raca = $tbRac->fetchAll();
        $this->view->ocupacao = $tbOcu->fetchAll();
        $this->view->escolaridade = $tbEsco->fetchAll();
        $this->view->situacaoFamiliar = $tbSitF->fetchAll();
        $this->view->usuario_logado = $tbUsr->getUsrAtual()->usr_codigo;
        $this->view->deficiencias = $tbPergDet->getPerguntaDetalhe("10");
        $this->view->doencas = $tbPergDet->getPerguntaDetalhe("1003");
        // return $this->render("paciente/form-paciente",NULL,TRUE);
    }

    // Método responsavél o cadastro de pessoa no aise, paciente, rua, domicilio no social
    public function salvarAction() {
        $this->_helper->layout->disableLayout();
        $this->view->title = "Cadastro de Pessoa";
        if ($this->_request->isPost()) {
            // Array de Dados Pessoa Aise
            $pessoa = array(
                "tipopessoa" => "F",
                "nome" => ($this->_request->getPost("nome", "") ? mb_strtoupper($this->_request->getPost("nome", ""), "UTF-8") : NULL),
                "nomefantasia" => ($this->_request->getPost("nome", "") ? mb_strtoupper($this->_request->getPost("nome", ""), "UTF-8") : NULL),
                "datanascimento" => ($this->_request->getPost("datanascimento", "") ? strtoupper($this->_request->getPost("datanascimento", "")) : NULL),
                "datainclusao" => date("Y-m-d"),
                "rg" => ($this->_request->getPost("rg", "") ? $this->_request->getPost("rg", "") : NULL),
                "orgaoemissor" => ($this->_request->getPost("orgaoemissor", "") ? $this->_request->getPost("orgaoemissor", "") : NULL),
                "dataemissao" => ($this->_request->getPost("dataemissao", "") ? $this->_request->getPost("dataemissao", "") : NULL),
                "estadoemissor" => ($this->_request->getPost("estadoemissor", "") ? $this->_request->getPost("estadoemissor", "") : NULL),
                "pispasep" => ($this->_request->getPost("pispasep", "") ? $this->_request->getPost("pispasep", "") : NULL),
                "cnpj_cpf" => preg_replace('#[^0-9]#','',$this->_request->getPost("cnpj_cpf"))
            );
            // Array de Dados Pessoa Paciente Social
            $pessoa_paciente = array(
                "cid_codigo" => ($this->_request->getPost("cid_codigo", "") ? $this->_request->getPost("cid_codigo", "") : NULL),
                "pep_bloqueado" => ($this->_request->getPost("pep_bloqueado", "") ? $this->_request->getPost("pep_bloqueado", "") : NULL),
                "pep_sexo" => ($this->_request->getPost("pep_sexo", "") ? $this->_request->getPost("pep_sexo", "") : NULL),
                "pep_mae" => ($this->_request->getPost("pep_mae", "") ? mb_strtoupper($this->_request->getPost("pep_mae", ""), "UTF-8") : NULL),
                "pep_pai" => ($this->_request->getPost("pep_pai", "") ? mb_strtoupper($this->_request->getPost("pep_pai", ""), "UTF-8") : NULL),
                "pep_email" => ($this->_request->getPost("pep_email", "") ? $this->_request->getPost("pep_email", "") : NULL),
                "pep_celular" => ($this->_request->getPost("pep_celular", "") ? $this->_request->getPost("pep_celular", "") : NULL),
                "pep_telefone" => ($this->_request->getPost("pep_telefone", "") ? $this->_request->getPost("pep_telefone", "") : NULL),
                "pep_responsavel" => ($this->_request->getPost("pep_responsavel", "") ? mb_strtoupper($this->_request->getPost("pep_responsavel", ""), "UTF-8") : NULL),
                "estc_codigo" => ($this->_request->getPost("estc_codigo", "") ? $this->_request->getPost("estc_codigo", "") : NULL),
                "pep_conjuge" => ($this->_request->getPost("pep_conjuge", "") ? $this->_request->getPost("pep_conjuge", "") : NULL),
                "pep_obito" => ($this->_request->getPost("pep_obito", "") ? $this->_request->getPost("pep_obito", "") : NULL),
                "pep_data_obito" => ($this->_request->getPost("pep_data_obito", "") ? $this->_request->getPost("pep_data_obito", "") : NULL),
                "pais_codigo" => ($this->_request->getPost("pais_codigo", "") ? $this->_request->getPost("pais_codigo", "") : NULL),
                "pep_cartao_sus" => ($this->_request->getPost("pep_cartao_sus", "") ? $this->_request->getPost("pep_cartao_sus", "") : NULL),
                "pep_cartorio_nasc" => ($this->_request->getPost("pep_cartorio_nasc", "") ? $this->_request->getPost("pep_cartorio_nasc", "") : NULL),
                "pep_livro_nasc" => ($this->_request->getPost("pep_livro_nasc", "") ? $this->_request->getPost("pep_livro_nasc", "") : NULL),
                "pep_folha_nasc" => ($this->_request->getPost("pep_folha_nasc", "") ? $this->_request->getPost("pep_folha_nasc", "") : NULL),
                "pep_termo_nasc" => ($this->_request->getPost("pep_termo_nasc", "") ? $this->_request->getPost("pep_termo_nasc", "") : NULL),
                "uni_codigo" => ($this->_request->getPost("uni_codigo", "") ? $this->_request->getPost("uni_codigo", "") : NULL),
                "rac_codigo" => ($this->_request->getPost("rac_codigo", "") ? $this->_request->getPost("rac_codigo", "") : NULL),
                "co_ocupacao" => ($this->_request->getPost("co_ocupacao", "") ? $this->_request->getPost("co_ocupacao", "") : NULL),
                "pep_cnh" => ($this->_request->getPost("pep_cnh", "") ? $this->_request->getPost("pep_cnh", "") : NULL),
                "pep_categoria_cnh" => ($this->_request->getPost("pep_categoria_cnh", "") ? $this->_request->getPost("pep_categoria_cnh", "") : NULL),
                "pep_carteira_trabalho" => ($this->_request->getPost("pep_carteira_trabalho", "") ? $this->_request->getPost("pep_carteira_trabalho", "") : NULL),
                "pep_carteira_trabalho_serie" => ($this->_request->getPost("pep_carteira_trabalho_serie", "") ? $this->_request->getPost("pep_carteira_trabalho_serie", "") : NULL),
                "pep_carteira_trabalho_data" => ($this->_request->getPost("pep_carteira_trabalho_data", "") ? $this->_request->getPost("pep_carteira_trabalho_data", "") : NULL),
                "pep_titulo_eleitor" => ($this->_request->getPost("pep_titulo_eleitor", "") ? $this->_request->getPost("pep_titulo_eleitor", "") : NULL),
                "pep_titulo_zona" => ($this->_request->getPost("pep_titulo_zona", "") ? $this->_request->getPost("pep_titulo_zona", "") : NULL),
                "pep_titulo_secao" => ($this->_request->getPost("pep_titulo_secao", "") ? $this->_request->getPost("pep_titulo_secao", "") : NULL),
                "pep_transporte_publico" => ($this->_request->getPost("pep_transporte_publico", "") ? $this->_request->getPost("pep_transporte_publico", "") : NULL),
                "pep_frenquencia_escolar" => ($this->_request->getPost("pep_frenquencia_escolar", "") ? $this->_request->getPost("pep_frenquencia_escolar", "") : NULL),
                "pep_portaria_naturalizacao" => ($this->_request->getPost("pep_portaria_naturalizacao", "") ? $this->_request->getPost("pep_portaria_naturalizacao", "") : NULL),
                "pep_data_naturalizacao" => ($this->_request->getPost("pep_data_naturalizacao", "") ? $this->_request->getPost("pep_data_naturalizacao", "") : NULL),
                "pep_data_entrada_pais" => ($this->_request->getPost("pep_data_entrada_pais", "") ? $this->_request->getPost("pep_data_entrada_pais", "") : NULL),
                "pep_bolsa_alimentacao" => ($this->_request->getPost("pep_bolsa_alimentacao", "") ? $this->_request->getPost("pep_bolsa_alimentacao", "") : NULL),
                "pep_bolsa_familia" => ($this->_request->getPost("pep_bolsa_familia", "") ? $this->_request->getPost("pep_bolsa_familia", "") : NULL),
                "pep_plano_saude" => ($this->_request->getPost("pep_plano_saude", "") ? $this->_request->getPost("pep_plano_saude", "") : NULL),
                "pep_renda" => ($this->_request->getPost("pep_renda", "") ? $this->_request->getPost("pep_renda", "") : NULL),
                "pep_observacao" => ($this->_request->getPost("pep_observacao", "") ? $this->_request->getPost("pep_observacao", "") : NULL),
                "pep_ecd_codigo" => ($this->_request->getPost("pep_ecd_codigo", "") ? $this->_request->getPost("pep_ecd_codigo", "") : NULL),
                "pep_situacao_familiar" => ($this->_request->getPost("pep_situacao_familiar", "") ? $this->_request->getPost("pep_situacao_familiar", "") : NULL)
            );
            // Array de dados de rua
            $dadosRua = array(
                "rua_nome" => ($this->_request->getPost("rua_nome", "") ? mb_strtoupper($this->_request->getPost("rua_nome", ""), "UTF-8") : NULL),
                "cid_codigo" => ($this->_request->getPost("cid_codigo", "") ? $this->_request->getPost("cid_codigo", "") : NULL),
                "co_tipo_logradouro" => ($this->_request->getPost("co_tipo_logradouro", "") ? $this->_request->getPost("co_tipo_logradouro", "") : NULL),
                "rua_cep" => ($this->_request->getPost("rua_cep", "") ? $this->_request->getPost("rua_cep", "") : NULL),
                "rua_bairro" => ($this->_request->getPost("rua_bairro", "") ? mb_strtoupper($this->_request->getPost("rua_bairro", ""), "UTF-8") : NULL),
            );
            // Array de dados de domicilio
            $dadosDomicilio = array(
                "dom_data_cadastro" => date("Y-m-d"),
                "dom_numero" => ($this->_request->getPost("dom_numero", "") ? $this->_request->getPost("dom_numero", "") : NULL),
                "dom_complemento" => ($this->_request->getPost("dom_complemento", "") ? $this->_request->getPost("dom_complemento", "") : NULL),
                "dom_ponto_referencia" => ($this->_request->getPost("dom_ponto_referencia", "") ? $this->_request->getPost("dom_ponto_referencia", "") : NULL),
                "co_tipo_domicilio" => "6",
                "dom_telefone" => ($this->_request->getPost("dom_telefone", "") ? $this->_request->getPost("dom_telefone", "") : NULL),
                "usu_codigo_responsavel" => ($this->_request->getPost("usu_codigo_responsavel", "") ? $this->_request->getPost("usu_codigo_responsavel", "") : NULL),
                "tipo_imovel" => ($this->_request->getPost("tipo_imovel", "") ? $this->_request->getPost("tipo_imovel", "") : 1),
            );
            if ($this->_request->getPost("pep_prontuario")) {
                $pessoa_paciente["pep_prontuario"] = $this->_request->getPost("pep_prontuario");
            }
            // Caso o ID seja informado o cadastro é editado
            if ($this->_getParam("pessoa", FALSE)) {
                $pessoa["pessoa"] = $this->_getParam("pessoa", FALSE);
            }
            // Inicio da transação
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try {
                /* -----------------------------------------------------/
                 * INSERÇÃO DOMICILIO                                   /
                 * ---------------------------------------------------- */
                // Validação de Inserção do cadastro de domicilio
                $rua = $this->_request->getPost("rua_nome");
                // Realiza validações se a rua foi informada
                if (!empty($rua)) {
                    // Selecionada rua, usa o código e atualiza os dados
                    $tbRua = new Application_Model_Rua();
                    if ($this->_request->getPost("rua_codigo")) {
                        $dadosRua["rua_codigo"] = $this->_request->getPost("rua_codigo");
                        $codRua = $tbRua->salvarRua($dadosRua);
                    } else {
                        // Validação de rua
                        //die("Rua: ".$tbRua->getQtdCodRuaDuplicada($dadosRua)->rua_codigo);
                        if ($tbRua->getQtdCodRuaDuplicada($dadosRua)->rua_codigo != "") {
                            $codRua = $tbRua->getQtdCodRuaDuplicada($dadosRua)->rua_codigo;
                        } else {
                            $codRua = $tbRua->salvarRua($dadosRua);
                        }
                    }
                    // Inserindo código de rua no array de domicilio
                    $dadosDomicilio["rua_codigo"] = $codRua;
                    // Já existe um domicilio cadastrado, não faz nada

                    $tbDom = new Application_Model_Domicilio();
                    if ($this->_request->getPost("dom_codigo")) {
                        $dadosDomicilio["dom_codigo"] = $this->_request->getPost("dom_codigo");
                        $codDom = $tbDom->salvarDomicilio($dadosDomicilio);
                    } else {

                        if ($tbDom->getQtdCodDomicilioDuplicado($dadosDomicilio)->dom_codigo != "") {
                            $codDom = $tbDom->getQtdCodDomicilioDuplicado($dadosDomicilio)->dom_codigo;
                        } else {
                            $codDom = $tbDom->salvarDomicilio($dadosDomicilio);
                        }
                    }
                }
                /* -----------------------------------------------------/
                 * INSERÇÃO PESSOA AISE                                 /
                 * ---------------------------------------------------- */
                // Registrar o log de inserção do aise
                $dadosLogAise = $this->getDadosLogAise($pessoa);
                // Inserção de Pessoa
                $tbPes = new Application_Model_Pessoa();
                $codPes = $tbPes->salvar($pessoa);
                $confCodPes = $tbPes->confereInsPessoa($codPes);
                // Depois que inseriu pessoa pega o id e encaminha pro log
                $dadosLogAise["primarykeyvalues"] = $codPes;
                /* -----------------------------------------------------/
                 * INSERÇÃO PESSOA PACIENTE                             /
                 * ---------------------------------------------------- */
                // Associando código de pessoa na tabela de pessoa paciente
                $pessoa_paciente["pessoa"] = $codPes;
                // Cadastro de domicilio feito, vincula paciente/donicilio
                if (!empty($rua)) {
                    $pessoa_paciente["dom_codigo"] = $codDom;
                }
                // Inserção de Pessoa Paciente
                $tbPesPac = new Application_Model_PessoaPaciente();
                // Verifica se pessoa já não foi cadatrada
                if ($tbPesPac->confereInsPesPaciente($codPes)->pep_codigo != "") {
                    $pessoa_paciente["pep_codigo"] = $tbPesPac->confereInsPesPaciente($codPes)->pep_codigo;
                }
                $codPesPac = $tbPesPac->salvar($pessoa_paciente);
                //Se salvou tudo, registra o log da operação
                $tbLog = new Application_Model_Syslog();
                $tbLog->salvar($dadosLogAise);
                // Realizando a inserção dos de dados, se não deu nenhum problema
                Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $exc) {
                Zend_Db_Table::getDefaultAdapter()->rollBack();
                $this->view->dados = $exc->getMessage();
                //$this->view->dados = (object) array_merge($pessoa,$pessoa_paciente);
                //$this->view->dados = array("success"=>FALSE, "titulo"=>"Erro", "mensagem"=>$exc->getMessage(), "code"=>$exc->getCode());
                return $this->render("dados", NULL, TRUE);
            }
            $this->view->dados = array("msg" => "Dados cadastrados com sucesso", "id" => $codPes);
            return $this->render("dados", NULL, TRUE);
            //$this->view->dados = $codPes;
            //return $this->render("form-paciente");
            //$this->_helper->redirector("form-paciente", "default", "paciente", $this->view->erro);
            //return $this->_redirect("/default/paciente/form-paciente",$this->view->erro,$this->view->dados);
        } else {
            return $this->_redirect("/default/paciente/form-paciente");
        }
    }

    public function salvarUsuarioAction() {
        $this->_helper->layout->disableLayout();
        $this->view->title = "Cadastro de Pessoa";
        if ($this->_request->isPost()) {
            // Array de Dados Usuario
            $pessoa = array(
                "usr_esp_codigo" => ($this->_request->getPost("profs_part_esp", "") ? $this->_request->getPost("profs_part_esp", "") : NULL),

                "usr_equipe_codigo" => ($this->_request->getPost("cod_cnes_uni", "") ? $this->_request->getPost("cod_cnes_uni", "") : NULL),

                "usu_microarea" => ($this->_request->getPost("cod_equipe", "") ? $this->_request->getPost("cod_equipe", "") : NULL),

                "usu_nome" => ($this->_request->getPost("nome", "") ? mb_convert_case($this->_request->getPost("nome", ""), MB_CASE_UPPER, "UTF-8") : NULL),
            // );die('aq');$pessoa = array(
                "usu_datanasc" => ($this->_request->getPost("datanascimento", "") ? strtoupper($this->_request->getPost("datanascimento", "")) : NULL),
                "usu_data_cad" => date("Y-m-d"),
                "usu_rg" => ($this->_request->getPost("rg", "") ? $this->_request->getPost("rg", "") : NULL),
                "usu_ciscomcam" => ($this->_request->getPost("usu_ciscomcam", "") ? $this->_request->getPost("usu_ciscomcam", "") : NULL),
                "usu_rg_emissor" => ($this->_request->getPost("orgaoemissor", "") ? $this->_request->getPost("orgaoemissor", "") : NULL),
                "usu_rg_dt_emissao" => ($this->_request->getPost("dataemissao", "") ? $this->_request->getPost("dataemissao", "") : NULL),
                "usu_pis_pasep" => ($this->_request->getPost("pispasep", "") ? $this->_request->getPost("pispasep", "") : NULL),
                "usu_cpf" => preg_replace('#[^0-9]#','',$this->_request->getPost("cnpj_cpf")),
                "cid_codigo_nasc" => ($this->_request->getPost("cid_codigo_nasc", "") ? $this->_request->getPost("cid_codigo_nasc", "") : NULL),
                "usu_bloqueado" => ($this->_request->getPost("pep_bloqueado", "") ? $this->_request->getPost("pep_bloqueado", "") : NULL),
                "usu_sexo" => ($this->_request->getPost("pep_sexo", "") ? $this->_request->getPost("pep_sexo", "") : NULL),
                "usu_mae" => ($this->_request->getPost("pep_mae", "") ? mb_convert_case($this->_request->getPost("pep_mae", ""), MB_CASE_UPPER, "UTF-8") : NULL),
                "usu_pai" => ($this->_request->getPost("pep_pai", "") ? mb_convert_case($this->_request->getPost("pep_pai", ""), MB_CASE_UPPER, "UTF-8") : NULL),
                "usu_email" => ($this->_request->getPost("pep_email", "") ? $this->_request->getPost("pep_email", "") : NULL),
                "usu_celular" => ($this->_request->getPost("pep_celular", "") ? $this->_request->getPost("pep_celular", "") : NULL),
                "usu_fone" => ($this->_request->getPost("pep_telefone", "") ? $this->_request->getPost("pep_telefone", "") : NULL),
                "usu_conjuge" => ($this->_request->getPost("pep_conjuge", "") ? $this->_request->getPost("pep_conjuge", "") : NULL),
                "usu_obito" => ($this->_request->getPost("pep_obito", "") ? $this->_request->getPost("pep_obito", "") : NULL),
                "usu_dt_obito" => ($this->_request->getPost("pep_data_obito", "") ? $this->_request->getPost("pep_data_obito", "") : NULL),
                "pais_codigo" => ($this->_request->getPost("pais_codigo", "") ? $this->_request->getPost("pais_codigo", "") : NULL),
                "usu_cartao_sus" => ($this->_request->getPost("pep_cartao_sus", "") ? $this->_request->getPost("pep_cartao_sus", "") : NULL),
                "usu_cert_cartorio_nasc" => ($this->_request->getPost("pep_cartorio_nasc", "") ? $this->_request->getPost("pep_cartorio_nasc", "") : NULL),
                "usu_cert_livro_nasc" => ($this->_request->getPost("pep_livro_nasc", "") ? $this->_request->getPost("pep_livro_nasc", "") : NULL),
                "usu_cert_lv_fls_nasc" => ($this->_request->getPost("pep_folha_nasc", "") ? $this->_request->getPost("pep_folha_nasc", "") : NULL),
                "usu_cert_termo_nasc" => ($this->_request->getPost("pep_termo_nasc", "") ? $this->_request->getPost("pep_termo_nasc", "") : NULL),
                "uni_codigo" => ($this->_request->getPost("uni_codigo", "") ? $this->_request->getPost("uni_codigo", "") : NULL),
                "rac_codigo" => ($this->_request->getPost("rac_codigo", "") ? $this->_request->getPost("rac_codigo", "") : NULL),
                "usu_cbo_r" => ($this->_request->getPost("co_ocupacao", "") ? $this->_request->getPost("co_ocupacao", "") : NULL),
                "usu_cnh_numero" => ($this->_request->getPost("pep_cnh", "") ? $this->_request->getPost("pep_cnh", "") : NULL),
                "usu_cnh_categoria" => ($this->_request->getPost("pep_categoria_cnh", "") ? $this->_request->getPost("pep_categoria_cnh", "") : NULL),
                "usu_ctps" => ($this->_request->getPost("pep_carteira_trabalho", "") ? $this->_request->getPost("pep_carteira_trabalho", "") : NULL),
                "usu_ctps_serie" => ($this->_request->getPost("pep_carteira_trabalho_serie", "") ? $this->_request->getPost("pep_carteira_trabalho_serie", "") : NULL),
                "usu_ctps_dt_emissao" => ($this->_request->getPost("pep_carteira_trabalho_data", "") ? $this->_request->getPost("pep_carteira_trabalho_data", "") : NULL),
                "usu_tit_eleitor" => ($this->_request->getPost("pep_titulo_eleitor", "") ? $this->_request->getPost("pep_titulo_eleitor", "") : NULL),
                "usu_tit_eleitor_zona" => ($this->_request->getPost("pep_titulo_zona", "") ? $this->_request->getPost("pep_titulo_zona", "") : NULL),
                "usu_tit_eleitor_secao" => ($this->_request->getPost("pep_titulo_secao", "") ? $this->_request->getPost("pep_titulo_secao", "") : NULL),
                "usu_transporte_publico" => ($this->_request->getPost("pep_transporte_publico", "") ? $this->_request->getPost("pep_transporte_publico", "") : NULL),
                "usu_freq_escolar" => ($this->_request->getPost("pep_frenquencia_escolar", "") ? $this->_request->getPost("pep_frenquencia_escolar", "") : NULL),
                "nr_portaria_naturalizacao" => ($this->_request->getPost("pep_portaria_naturalizacao", "") ? $this->_request->getPost("pep_portaria_naturalizacao", "") : NULL),
                "dt_naturalizacao" => ($this->_request->getPost("pep_data_naturalizacao", "") ? $this->_request->getPost("pep_data_naturalizacao", "") : NULL),
                "usu_dt_entrada_pais" => ($this->_request->getPost("pep_data_entrada_pais", "") ? $this->_request->getPost("pep_data_entrada_pais", "") : NULL),
                "usu_bolsa_alimentacao" => ($this->_request->getPost("pep_bolsa_alimentacao", "") ? $this->_request->getPost("pep_bolsa_alimentacao", "") : NULL),
                "usu_bolsa_familia" => ($this->_request->getPost("pep_bolsa_familia", "") ? $this->_request->getPost("pep_bolsa_familia", "") : NULL),
                "usu_plano_saude" => ($this->_request->getPost("pep_plano_saude", "") ? $this->_request->getPost("pep_plano_saude", "") : NULL),
                "usu_renda_media" => ($this->_request->getPost("pep_renda", "") ? $this->_request->getPost("pep_renda", "") : NULL),
                "usu_observacao" => ($this->_request->getPost("pep_observacao", "") ? $this->_request->getPost("pep_observacao", "") : NULL),
                "ecd_codigo" => ($this->_request->getPost("pep_ecd_codigo", "") ? $this->_request->getPost("pep_ecd_codigo", "") : NULL),
                "usu_sit_familiar" => ($this->_request->getPost("pep_situacao_familiar", "") ? $this->_request->getPost("pep_situacao_familiar", "") : NULL),
                "estc_codigo" => ($this->_request->getPost("estc_codigo", "") ? $this->_request->getPost("estc_codigo", "") : NULL),
                "uf_sigla_rg" => ($this->_request->getPost("estadoemissor", "") ? $this->_request->getPost("estadoemissor", "") : NULL),
                "usr_codigo" => ($this->_request->getPost("usr_codigo", "") ? $this->_request->getPost("usr_codigo", "") : NULL),
                "cd_nacionalidade" => $this->_request->getPost("cd_nacionalidade", "") == '' ? 'B' : $this->_request->getPost("cd_nacionalidade", ""),
                "usu_sit_rua" => ($this->_request->getPost("usu_sit_rua", "") ? $this->_request->getPost("usu_sit_rua", "") : NULL),
                // "usu_situacao_rua_tempo" => ($this->_request->getPost("usu_situacao_rua_tempo", "") ? $this->_request->getPost("usu_situacao_rua_tempo", "") : NULL),
                "usu_deficiencia" => ($this->_request->getPost("usu_deficiencia", "")),
                "usu_doenca" => ($this->_request->getPost("usu_doenca", "")),
                "usu_tem_diabete" => ($this->_request->getPost("usu_tem_diabete", "") ? $this->_request->getPost("usu_tem_diabete", "") : NULL),
                "usu_esta_gestante" => ($this->_request->getPost("usu_esta_gestante", "") ? $this->_request->getPost("usu_esta_gestante", "") : NULL),
                "usu_tem_hipertensao" => ($this->_request->getPost("usu_tem_hipertensao", "") ? $this->_request->getPost("usu_tem_hipertensao", "") : NULL),
                "etn_codigo" => ($this->_request->getPost("etn_codigo", "") ? $this->_request->getPost("etn_codigo", "") : NULL),
                "usu_st_responsavel_familiar" => ($this->_request->getPost("proprio_responsavel", "") ? $this->_request->getPost("proprio_responsavel", "") : NULL)
            );
            
            // echo "<pre>";print_r($pessoa);die();

            if ($this->_request->getPost("pep_prontuario") != "") {
                $pessoa['usu_prontuario'] = $this->_request->getPost("pep_prontuario");
            }

            $bai_codigo = $this->_request->getPost("bai_codigo", "");

            if ($this->_request->getPost("sn", "") == 1 ) {
                $dom_numero = "0";
            } else {
                if ($this->_request->getPost("dom_numero") == "S/N") {
                    $dom_numero = "0";
                } else {
                    $dom_numero = $this->_request->getPost("dom_numero", "");
                }
            }
            $dadosDomicilio = array(
                "dom_data_cadastro" => date("Y-m-d"),
                "dom_numero" => $dom_numero ,
                "dom_complemento" => ($this->_request->getPost("dom_complemento", "") ? mb_convert_case($this->_request->getPost("dom_complemento", ""), MB_CASE_UPPER, "UTF-8") : NULL),
                "dom_ponto_referencia" => ($this->_request->getPost("dom_ponto_referencia", "") ? $this->_request->getPost("dom_ponto_referencia", "") : NULL),
                "co_tipo_domicilio" => "6",
                "dom_telefone" => ($this->_request->getPost("dom_telefone", "") ? $this->_request->getPost("dom_telefone", "") : NULL),
                "bai_codigo" => ($bai_codigo ? $bai_codigo : NULL),
                "tipo_imovel" => ($this->_request->getPost("tipo_imovel", "") ? $this->_request->getPost("tipo_imovel", "") : 1),
            );
            // Valida edição
            if ($this->_request->getPost("pessoa-edita")) {
                $pessoa["usu_codigo"] = $this->_request->getPost("pessoa-edita");
            }
            // Inicio da transação
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try {
                /* -----------------------------------------------------/
                 * INSERÇÃO DOMICILIO                                   /
                 * ---------------------------------------------------- */
                // Validação de Inserção do cadastro de domicilio
                $rua = $this->_request->getPost("rua_codigo");
                // Realiza validações se a rua foi informada
                if (!empty($rua)) {
                    // Inserindo código de rua no array de domicilio
                    $dadosDomicilio["rua_codigo"] = $this->_request->getPost("rua_codigo");
                    // Já existe um domicilio cadastrado, não faz nada
                    $tbDom = new Application_Model_Domicilio();
                    if ($this->_request->getPost("dom_codigo")) {
                        $dadosDomicilio["dom_codigo"] = $this->_request->getPost("dom_codigo");
                        $codDom = $tbDom->salvarDomicilio($dadosDomicilio);
                    } else {
                        if ($tbDom->getQtdCodDomicilioDuplicado($dadosDomicilio)->dom_codigo != "") {
                            $codDom = $tbDom->getQtdCodDomicilioDuplicado($dadosDomicilio)->dom_codigo;
                            throw new Zend_Validate_Exception("Já existe um domicílio com as mesmas caracteristicas! Favor verificar");
                        } else {
                            $codDom = $tbDom->salvarDomicilio($dadosDomicilio);
                        }
                    }

                }

                /* -----------------------------------------------------/
                 * INSERÇÃO USUARIO PACIENTE                            /
                 * ---------------------------------------------------- */
                // Cadastro de domicilio feito, vincula paciente/donicilio
                if (!empty($rua)) {
                    $pessoa["dom_codigo"] = $codDom;
                }
                // Salvando dados usuario
                $tbUsu = new Application_Model_Usuario();
                $usu_codigo = $tbUsu->salvar($pessoa);
                // Salvando deficiencias do paciente se houver
                $deficiencias = $this->_request->getPost("deficiencias");
                $this->salvarDeficiencias($usu_codigo,$deficiencias);
                // Salvando doencas do paciente se houver
                $doencas = $this->_request->getPost("doencas");
                $this->salvarDoencas($usu_codigo,$doencas);

                if($this->_request->getPost("proprio_responsavel") == "S" && $codDom != ""){
                    $this->vinculaResponsavelProprio($usu_codigo,$codDom);
                }else{
					if($this->_request->getPost("usu_codigo", "")){
						$this->vinculaResponsavelIntegrante($this->_request->getPost("usu_codigo", ""),$codDom);
					}
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $exc) {
                Zend_Db_Table::getDefaultAdapter()->rollBack();
                $this->view->dados = $exc->getMessage();
                return $this->render("dados", NULL, TRUE);
            }

            $this->view->dados = array("msg" => "Dados cadastrados com sucesso", "id" => $usu_codigo);
            return $this->render("dados", NULL, TRUE);

        } else {
            return $this->_redirect("/default/paciente/form-paciente");
        }
    }

    public function vinculaResponsavelIntegrante($usu_codigo=FALSE,$dom_codigo=FALSE){
        $tbUsu = new Application_Model_Usuario();
        $tbDom = new Application_Model_Domicilio();
        try{
            $tbDom->removeResponsavel($usu_codigo);
        }  catch (Exception $exc){
            die($exc->getMessage());
        }
        $array_salvar = array("usu_codigo"=>$usu_codigo,
                              "dom_codigo" => $dom_codigo);

        try{
            $tbUsu->salvar($array_salvar);
            $this->vinculaResponsavelProprio($usu_codigo,$dom_codigo);

        }catch(Exception $exc){

            die($exc->getMessage());
        }
        return true;

    }

    public function salvarDeficiencias($usuCodigo=FALSE,$deficiencias=FALSE) {
        $tbUsuDef = new Application_Model_UsuarioDeficiencias();
        $tbUsuDef->excluirPorUsuario($usuCodigo);
        foreach ($deficiencias as $value) {
            $dados = "";
            $dados = array(
                "usu_codigo" => $usuCodigo,
                "co_pergunta_detalhe" => $value
            );
            try{
                $tbUsuDef->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }
    public function salvarDoencas($usuCodigo=FALSE,$doencas=FALSE) {
        $tbUsuDoenca = new Application_Model_UsuarioDoencas();
        $tbUsuDoenca->excluirPorUsuario($usuCodigo);
        foreach ($doencas as $value) {
            $dados = "";
            $dados = array(
                "usu_codigo" => $usuCodigo,
                "co_pergunta_detalhe" => $value
            );
            try{
                $tbUsuDoenca->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function vinculaResponsavelProprio($usu_codigo=FALSE,$dom_codigo=FALSE){
        $tbDom = new Application_Model_Domicilio();
        //$verifica_responsavel = $tbDom->verificaSeJaEResponsavel($usu_codigo);
        $dados = array("dom_codigo"=>$dom_codigo,
                       "usu_codigo_responsavel" =>$usu_codigo);
        try{
            $tbDom->salvar($dados);
        }  catch (Exception $exc){
            die($exc->getMessage());
        }
    }

    public function getDadosLogAise($pessoa) {
        $tbUsr = new Application_Model_Usuarios();
        $codPessoa = $pessoa["pessoa"];
        $dadosLogAise = array(
            "datahora" => date("Y/m/d"),
            "usuario" => strtolower($tbUsr->getUsrAtual()->usr_nome),
            "modulo" => 14,
            "activeform" => "TFrm_Pessoa",
            "tipolog" => "3",
            "descricao" => "cdsPessoa",
            "primarykeynames" => "pessoa",
            "activeformorigem" => "TFrm_Pessoa",
            "estadofinal" => $this->getDadosFinalLogAise($pessoa)
        );
        // Em caso de edição
        if ($codPessoa) {
            $dadosLogAise["tipooperacao"] = 2;
            $dadosLogAise["estadoanterior"] = $this->getDadosAnteriorLogAise($codPessoa);
        } else {
            $dadosLogAise["tipooperacao"] = 1;
        }
    }

    public function getDadosAnteriorLogAise($codPessoa) {
        $estadoAnterior = "";
        $tbPes = new Application_Model_Pessoa();
        $dadosPessoa = $tbPes->listaDadosPessoa($codPessoa)->toArray();
        // Listando os dados antigo de pessoa e jogando na variavel de valores anteriores
        foreach ($dadosPessoa as $item => $value) {
            if ($value)
                $estadoAnterior .= $item . "=" . $value . "|";
        }
        // Removendo o útlimo |
        $estadoAnterior = substr($estadoAnterior, 0, -1);
        return $estadoAnterior;
    }

    public function getDadosFinalLogAise($pessoa) {
        // Inserindo na variavel estadofinal os novos dados que serão inseridos
        foreach ($pessoa as $item => $value) {
            if ($value)
                $estadoFinal .= $item . "=" . $value . "|";
        }
        // Removendo o útlimo |
        $estadoFinal = substr($estadoFinal, 0, -1);
        return $estadoFinal;
    }

    public function listaCadastrosDuplicadosAction() {
        if (!$this->_request->getPost("aise", "")) {
            $tbUsu = new Application_Model_Usuario();
            $dadosPessoa = array(
                "usu_datanasc" => ($this->_request->getPost("datanascimento", "") ? $this->_request->getPost("datanascimento", "") : NULL),
                "usu_mae" => ($this->_request->getPost("pep_mae", "") ? $this->_request->getPost("pep_mae", "") : NULL)
            );
            //"nome" => ($this->_request->getPost("nome", "") ? $this->_request->getPost("nome", ""): NULL)

            $nome = explode(" ", $this->_request->getPost("nome", ""));

            try {

                $qtdCadDup = $tbUsu->listaCadastroDuplicado($dadosPessoa, $nome);
            } catch (Exception $exc) {
                $this->view->dados = "";
                return $this->render("dados", NULL, TRUE);
            }
            $this->view->dados = $qtdCadDup;
            //die("stop");
            return $this->render("dados", NULL, TRUE);
        } else {
            $tbPes = new Application_Model_Pessoa();
            $dadosPessoa = array(
                "datanascimento" => ($this->_request->getPost("datanascimento", "") ? $this->_request->getPost("datanascimento", "") : NULL),
                "pep_mae" => ($this->_request->getPost("pep_mae", "") ? $this->_request->getPost("pep_mae", "") : NULL)
            );
            //"nome" => ($this->_request->getPost("nome", "") ? $this->_request->getPost("nome", ""): NULL)

            $nome = explode(" ", $this->_request->getPost("nome", ""));

            try {
                $qtdCadDup = $tbPes->listaCadastroDuplicado($dadosPessoa, $nome);
            } catch (Exception $exc) {
                $this->view->dados = "";
                return $this->render("dados", NULL, TRUE);
            }
            $this->view->dados = $qtdCadDup;
            //die("stop");
            return $this->render("dados", NULL, TRUE);
        }
    }

    public function dadosAction() {
        $usu_codigo = $this->_getParam("cod", FALSE);
        if (!$usu_codigo)
            return $this->_redirect("/");

        $tbUsu = new Application_Model_Usuario();
        $this->view->dados = $tbUsu->getDados($usu_codigo);
        $this->view->obs = $this->_getParam("obs", FALSE);
    }

    public function historicoDeMedicamentosAction() {
        $usu_codigo = $this->_getParam("cod", FALSE);
        $inicio = $this->_getParam("inicio", 0);
        $fim = $this->_getParam("fim", 0);
        if (!$usu_codigo)
            return $this->_redirect("/");

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        $this->view->usu_codigo = $usu_codigo;
        $receitaItensModel = new Application_Model_ReceitaItens();
        $itens = $receitaItensModel->receitasPorUsuario($usu_codigo);
        $retorno = [];
        $receitaAnterior = 0;
        foreach($itens as $item):
            if($receitaAnterior != $item->rec_codigo || $receitaAnterior == 0){
                $c = 0;
                $receitaAnterior = $item->rec_codigo;
                $retorno[$receitaAnterior]['rec_codigo'] = $item->rec_codigo;
                $retorno[$receitaAnterior]['rec_data'] = $item->rec_data;
                $retorno[$receitaAnterior]['rec_validade'] = $item->rec_validade;
                $retorno[$receitaAnterior]['usr_nome'] = $item->usr_nome;
            }
            $retorno[$receitaAnterior]['itens'][$c]['irec_quantidade'] = $item->irec_quantidade;
            $retorno[$receitaAnterior]['itens'][$c]['irec_recomendacao'] = $item->irec_recomendacao;
            $retorno[$receitaAnterior]['itens'][$c]['pro_nome'] = $item->pro_nome;
            $receitaAnterior = $item->rec_codigo;
            $c++;
        endforeach;

        $qtd = count($retorno);

        if($fim){
            $this->view->receitas = array_slice($retorno, $inicio, $fim);
            $this->view->mais = ($qtd - $fim);
            $this->view->inicio = $fim;
        } elseif($inicio){
            $this->view->receitas = array_slice($retorno, $inicio);
        } else {
            $this->view->receitas = $retorno;
        }
    }

    public function historicoDeMedicamentosRetiradosAction() {
        $usu_codigo = $this->_getParam("cod", FALSE);
        if (!$usu_codigo)
            return $this->_redirect("/");

        $tbMov = new Application_Model_Movimento();
        $this->view->itens = $tbMov->getMedicamentosDispensados($usu_codigo);
    }

    /**
     *
     * Retorna os ultimos exames que foram solicitados para determinado paciente
     */
    public function historicoDeExamesAction() {
        $usu_codigo = $this->_getParam("cod", FALSE);
        if (!$usu_codigo)
            return $this->_redirect("/");

        $tbRes = new Application_Model_ResultadoExame();
        $dados = $tbRes->getSolicitados($usu_codigo);

        $tbUsr = new Application_Model_Usuarios();
        $id_login = $tbUsr->getUsrAtual();

        $tbAge = new Application_Model_Agendamento();
        $age_codigo = $tbAge->usuEmAberto()->age_codigo;

        $this->view->age_codigo = $age_codigo;
        $this->view->usr_codigo = $id_login;
        $this->view->itens = $dados;
    }

    public function historicoDeExamesRealizadosAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/default/paciente/historico-de-exames-realizados.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.elevateZoom-2.5.5.min.js');
        $usu_codigo = $this->_getParam("cod", FALSE);
        if (!$usu_codigo)
            return $this->_redirect("/");

        $tbRes = new Application_Model_ResultadoExame();
        $dados = $tbRes->getColetados($usu_codigo);
        $this->view->itens = $dados;

        $tbUpl = new Application_Model_UploadArquivo();
        $dadosImg = $tbUpl->getArquivosPorUsuario($usu_codigo);

        $this->view->itensImg = $dadosImg;
        //die("asdfasdf");
    }

    public function dadosVisitaDomiciliarAction(){
        $usu_codigo = $this->_getParam("cod", FALSE);
        if (!$usu_codigo)
            return $this->_redirect("/");
        $tbAte = new Application_Model_Atendimento();
        //var_dump($usu_codigo);
        $dados = $tbAte->getDadosVisitaDomiciliarPorUsuario($usu_codigo);
        foreach($dados as $dado){        
            $motivo[$dado[ate_codigo]] = $tbAte->getMotivoPorAtendimento($dado[ate_codigo]);
        }
        //var_dump($motivo);
        $this->view->dados = $dados;
        $this->view->motivo = $motivo;
    }

    /**
     * Retorna os USU's em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarAction() { 
        $tbUsu = new Application_Model_Usuario();
        $term = $this->_getParam("term", FALSE);
        $tipo_busca = $this->_getParam("tipo_busca", FALSE);
        $tipo_de_busca = $this->_getParam("tipo_de_busca", FALSE);

        //echo "<pre>";print_r($tipo_de_busca);die();

        if($tipo_de_busca== "1" || $tipo_de_busca== "2" || $tipo_de_busca== "3" || $tipo_de_busca== "4" || $tipo_de_busca== "5"){
            $this->view->dados = $tbUsu->buscarTipo($term,$tipo_de_busca); 
        }
          else if ($tipo_busca == "") {
            $this->view->dados = $tbUsu->buscar($term);
        } else {
            $this->view->dados = $tbUsu->buscarFiltro($term, $tipo_busca);
        }

        return $this->render("dados", NULL, TRUE);
    }

    /*action busca raas*/
    public function buscarRasAction() {
        $tbUsu = new Application_Model_Usuario();
        $term = $this->_getParam("term", FALSE);
        $tipo_busca = $this->_getParam("tipo_busca", FALSE);
        if ($tipo_busca == "") {
            $this->view->dados = $tbUsu->buscarRas($term);
        } 
        return $this->render("dados", NULL, TRUE);
    }


    public function verificaVinculosDomicilioAction(){
        $usu_codigo = $this->_getParam("usu_codigo", FALSE);
        $tbUsu = new Application_Model_Usuario();
        $this->view->dados = $tbUsu->getVinculoComDomicilio($usu_codigo)->toArray();

        return $this->render("dados",NULL,TRUE);

    }
    public function buscarUsuarioRelatorioAction() {
        $tbUsu = new Application_Model_Usuario();
        $term = $this->_getParam("term", FALSE);
        $this->view->dados = $tbUsu->buscarUsuarioRelatorio($term);
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarOcupacaoAction() {
        $term = $this->_getParam("term", FALSE);
        $tbOcu = new Application_Model_Ocupacao();
        // echo "<pre>".  print_r($tbEst->fetchAll("pais_codigo = '$pais_codigo'"),1);die();
        $this->view->dados = $tbOcu->buscar($term);
        //die($this->view->estados);
        return $this->render("dados", NULL, TRUE);
    }

    public function buscarPessoaAction() {
        $term = $this->_getParam("term", FALSE);
        $tbPes = new Application_Model_Pessoa();
        // echo "<pre>".  print_r($tbEst->fetchAll("pais_codigo = '$pais_codigo'"),1);die();
        $this->view->dados = $tbPes->buscar($term);
        //die($this->view->estados);
        return $this->render("dados", NULL, TRUE);
    }

    public function tipoLogradouroAction() {
        $tbTpLogr = new Application_Model_TbMsTipoLogradouro();
        $id = $this->_getParam("id", FALSE);
        $select = $tbTpLogr->selectTag($id);
        $this->view->dados = $select;
        return $this->render("dados", NULL, TRUE);
    }
    public function getPacienteAgendamentoAction(){
        $tbAge = new Application_Model_Agendamento();
        $usu_codigo = $this->_getParam("usu_codigo", FALSE);
        $this->view->dados = $tbAge->getAgendamentosUsuario($usu_codigo);
        return $this->render("dados", NULL, TRUE);
    }

    /* ----------------------------------------------------------------
     * MODULO E-SUS
     * ---------------------------------------------------------------- */

    public function getDadosImportacaoCodigoAction($eir_codigo) {
        $tbEir = new Application_Model_EsusImportacaoResultado();
        $dadosEir = $tbEir->getDadosResultadoCodigo($eir_codigo);
        return $dadosEir;
    }

    public function editaPacienteEsusAction() {
        $this->_helper->layout->disableLayout();
        $eir_codigo = trim($this->_request->getPost("eir_codigo"));
        $usu_codigo = trim($this->_request->getPost("usu_codigo"));

        $tbUsu = new Application_Model_Usuario();
        $dadosEir = $this->getDadosImportacaoCodigoAction($eir_codigo);
        $dadosPac = $tbUsu->getPacientesPelaMaeNomeNasc($dadosEir, $usu_codigo);

        if (count($dadosPac) == 0) {
            $this->view->dados = "aviso";
        } else
        if (count($dadosPac) == 1) {
            $this->view->dados = "form";
        } else {
            $this->view->dados = "lista";
        }

        return $this->render("dados", NULL, TRUE);
    }

    public function esusListaPacientesDuplicadosAction() {
        $this->_helper->layout->disableLayout();
        $this->view->title = "E-SUS - Resultados";
        $eir_codigo = trim($this->_request->getParam("eir_codigo"));
        $usu_codigo = trim($this->_request->getParam("usu_codigo"));
        $tbUsu = new Application_Model_Usuario();
        $dadosEir = $this->getDadosImportacaoCodigoAction($eir_codigo);
        $dadosPac = $tbUsu->getPacientesPelaMaeNomeNasc($dadosEir);
        $this->view->dados = $dadosPac;
        $this->view->erroImportacao = $dadosEir->eir_mensagem;
        $this->view->dadosEir = $dadosEir;
    }

    public function esusIncosistenciaInvalidaAction() {
        $this->_helper->layout->disableLayout();
    }

    public function esusFormPacienteAction() {
        // die("bateu aqui");
        $this->_helper->layout->disableLayout();
        $this->view->title = "Edita Incosistências Paciente";
        $eir_codigo = trim($this->_request->getParam("eir_codigo"));
        $usu_codigo = trim($this->_request->getParam("usu_codigo"));
        $tbRac = new Application_Model_Raca();
        $tbUsu = new Application_Model_Usuario();
        $dadosEir = $this->getDadosImportacaoCodigoAction($eir_codigo);
        $dadosPac = $tbUsu->getPacientesPelaMaeNomeNasc($dadosEir, $usu_codigo);
        $this->view->raca = $tbRac->fetchAll();
        $this->view->erroImportacao = $dadosEir->eir_mensagem;
        //echo $this->view->erroImportacao; die("Erro");
        $this->view->dados = $dadosPac;
    }

    public function esusFormPacienteCnsAction() {
        $this->_helper->layout->disableLayout();
        $this->view->title = "Edita Incosistências Paciente";
        $usu_codigo = trim($this->_request->getParam("usu_codigo"));
        $ativCol = trim($this->_request->getParam("ativ_col"));
        $tbUsu = new Application_Model_Usuario();
        $tbRac = new Application_Model_Raca();
        $tbEsus = new Application_Model_EsusCadastroIndividual();
        $dadosEsus = $tbEsus->getDadosPorUsuario($usu_codigo);
        $dadosPac = $tbUsu->listaDadosUsuario($usu_codigo);
        // die(var_dump("here"));
        //die(var_dump($dadosPac));
        //die(var_dump($dadosEsus));
        $this->view->dadosEsus = $dadosEsus;
        $this->view->raca = $tbRac->fetchAll();
        $this->view->dados = $dadosPac;
        $this->view->ativCol = $ativCol;
        
    }
    
    public function esusFormPacienteCnsSalvarAction() {
        $tbUsu = new Application_Model_Usuario();
        $array_dados = array("usu_nome" => mb_convert_case($_POST[usu_nome], MB_CASE_UPPER, "UTF-8"),
                             "usu_mae" => mb_convert_case($_POST[usu_mae], MB_CASE_UPPER, "UTF-8"),
                             "usu_cartao_sus" => mb_convert_case($_POST[usu_cartao_sus_mc], MB_CASE_UPPER, "UTF-8"),
                             "usu_codigo" => $_POST[usu_codigo],
                             "rac_codigo" => $_POST[rac_codigo],
                             "cd_nacionalidade" => $_POST[cd_nacionalidade_mc]);    
        $tbUsu->salvar($array_dados);
        
        // --------
        $dadosRua = array(
            "rua_nome" => ($this->_request->getPost("rua_nome", "") ? mb_strtoupper($this->_request->getPost("rua_nome", ""), "UTF-8") : NULL),
            "cid_codigo" => ($this->_request->getPost("cid_codigo", "") ? $this->_request->getPost("cid_codigo", "") : NULL),
            "co_tipo_logradouro" => ($this->_request->getPost("co_tipo_logradouro", "") ? $this->_request->getPost("co_tipo_logradouro", "") : NULL),
            "rua_cep" => ($this->_request->getPost("rua_cep", "") ? $this->_request->getPost("rua_cep", "") : NULL),
            "rua_bairro" => ($this->_request->getPost("rua_bairro", "") ? mb_strtoupper($this->_request->getPost("rua_bairro", ""), "UTF-8") : NULL),
        );

        $dadosDomicilio = array(
            "dom_data_cadastro" => date("Y-m-d"),
            "dom_numero" => ($this->_request->getPost("dom_numero", "") ? $this->_request->getPost("dom_numero", "") : NULL),
            "dom_complemento" => ($this->_request->getPost("dom_complemento", "") ? $this->_request->getPost("dom_complemento", "") : NULL),
            "dom_ponto_referencia" => ($this->_request->getPost("dom_ponto_referencia", "") ? $this->_request->getPost("dom_ponto_referencia", "") : NULL),
            "co_tipo_domicilio" => "6",
            "dom_telefone" => ( $this->_request->getPost("dom_telefone", "") == '' ? '' : $this->_request->getPost("dom_telefone", "")),
            "usu_codigo_responsavel" => ($this->_request->getPost("usu_codigo", "") ? $this->_request->getPost("usu_codigo", "") : NULL),
            "tipo_imovel" => ($this->_request->getPost("tipo_imovel", "") ? $this->_request->getPost("tipo_imovel", "") : 1),
        );
        $rua = $this->_request->getPost("rua_nome");

        if (!empty($rua)) {
            // Selecionada rua, usa o código e atualiza os dados
            $tbRua = new Application_Model_Rua();
            if ($this->_request->getPost("rua_codigo")) {
                $dadosRua["rua_codigo"] = $this->_request->getPost("rua_codigo");
                $codRua = $tbRua->salvarRua($dadosRua);
            } else {
                // Validação de rua
                if ($tbRua->getQtdCodRuaDuplicada($dadosRua)->rua_codigo != "") {
                    $codRua = $tbRua->getQtdCodRuaDuplicada($dadosRua)->rua_codigo;
                } else {
                    $codRua = $tbRua->salvarRua($dadosRua);
                }
            }
            // Inserindo código de rua no array de domicilio
            $dadosDomicilio["rua_codigo"] = $codRua;
            // Já existe um domicilio cadastrado, não faz nada
            $tbDom = new Application_Model_Domicilio();
            
            $dadosDomicilio["dom_codigo"] = $this->_request->getPost("dom_codigo");
            $codDom = $tbDom->salvarDomicilio($dadosDomicilio);
        }
        return $this->render("dados",NULL,TRUE);
    }

    public function salvarFormPacienteEsusAction() {
        $tbCon = new Application_Model_Configuracao();
        if ($tbCon->getDadosConfigPelaChave("CADASTRO_AISE")->conf_valor_bool == 1) {
            $this->salvarPacienteAiseEsusAction($_POST);
        } else {
            $this->salvarPacienteEsusAction($_POST);
        }
    }

    public function salvarPacienteEsusAction($post) {
        $this->_helper->layout->disableLayout();
        // Array de Dados Usuario
        $paciente = array(
            "usu_nome" => ($this->_request->getPost("nome", "") ? mb_strtoupper($this->_request->getPost("nome", ""), "UTF-8") : NULL),
            "usu_datanasc" => ($this->_request->getPost("datanascimento", "") ? strtoupper($this->_request->getPost("datanascimento", "")) : NULL),
            "usu_pis_pasep" => ($this->_request->getPost("pispasep", "") ? $this->_request->getPost("pispasep", "") : NULL),
            "usu_cpf" => ($this->_request->getPost("cnpj_cpf", "") ? $this->_request->getPost("cnpj_cpf", "") : NULL),
            "usu_mae" => ($this->_request->getPost("pep_mae", "") ? mb_strtoupper($this->_request->getPost("pep_mae", ""), "UTF-8") : NULL),
            "usu_celular" => ($this->_request->getPost("pep_celular", "") ? $this->_request->getPost("pep_celular", "") : NULL),
            "usu_fone" => ($this->_request->getPost("pep_telefone", "") ? $this->_request->getPost("pep_telefone", "") : NULL),
            "usu_fone_recado" => ($this->_request->getPost("pep_contato", "") ? $this->_request->getPost("pep_contato", "") : NULL),
            "usu_cartao_sus" => ($this->_request->getPost("pep_cartao_sus", "") ? $this->_request->getPost("pep_cartao_sus", "") : NULL),
            "rac_codigo" => ($this->_request->getPost("rac_codigo", "") ? $this->_request->getPost("rac_codigo", "") : NULL),
        );

        // Valida edição
        if ($this->_request->getPost("usu_codigo")) {
            $paciente["usu_codigo"] = $this->_request->getPost("usu_codigo");
        }

        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
            // Salvando dados usuario
            $tbUsu = new Application_Model_Usuario();
            $usu_codigo = $tbUsu->salvar($paciente);
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->roolBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados", NULL, TRUE);
        }
        $this->view->dados = "";
        return $this->render("dados", NULL, TRUE);
    }

    public function salvarPacienteAiseEsusAction($post) {
        // Inicio da transação
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
            // Array de Dados Pessoa Aise
            $pessoa = array(
                "tipopessoa" => "F",
                "nome" => ($this->_request->getPost("nome", "") ? mb_strtoupper($this->_request->getPost("nome", ""), "UTF-8") : NULL),
                "datanascimento" => ($this->_request->getPost("datanascimento", "") ? strtoupper($this->_request->getPost("datanascimento", "")) : NULL),
                "pispasep" => ($this->_request->getPost("pispasep", "") ? $this->_request->getPost("pispasep", "") : NULL),
                "cnpj_cpf" => ($this->_request->getPost("cnpj_cpf", "") ? $this->_request->getPost("cnpj_cpf", "") : NULL)
            );
            // Caso o ID seja informado o cadastro é editado
            if ($this->_getParam("pessoa", FALSE)) {
                $pessoa["pessoa"] = $this->_getParam("pessoa", FALSE);
            }
            // Inserção de Pessoa
            $tbPes = new Application_Model_Pessoa();
            $codPes = $tbPes->salvar($pessoa);
            $confCodPes = $tbPes->confereInsPessoa($codPes);
            // Array de Dados Pessoa Paciente Social
            $pessoa_paciente = array(
                "pep_mae" => ($this->_request->getPost("pep_mae", "") ? mb_strtoupper($this->_request->getPost("pep_mae", ""), "UTF-8") : NULL),
                "pep_celular" => ($this->_request->getPost("pep_celular", "") ? $this->_request->getPost("pep_celular", "") : NULL),
                "pep_telefone" => ($this->_request->getPost("pep_telefone", "") ? $this->_request->getPost("pep_telefone", "") : NULL),
                "pep_fone" => ($this->_request->getPost("pep_contato", "") ? $this->_request->getPost("pep_contato", "") : NULL),
                "pep_cartao_sus" => ($this->_request->getPost("pep_cartao_sus", "") ? $this->_request->getPost("pep_cartao_sus", "") : NULL),
                "rac_codigo" => ($this->_request->getPost("rac_codigo", "") ? $this->_request->getPost("rac_codigo", "") : NULL),
            );
            // Inserção de Pessoa Paciente
            $tbPesPac = new Application_Model_PessoaPaciente();
            // Verifica se pessoa já não foi cadatrada
            if ($tbPesPac->confereInsPesPaciente($codPes)->pep_codigo != "") {
                $pessoa_paciente["pep_codigo"] =
                        $tbPesPac->confereInsPesPaciente($codPes)->pep_codigo;
            }
            $codPesPac = $tbPesPac->salvar($pessoa_paciente);
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados", NULL, TRUE);
        }
        
        $this->view->dados = array("msg" => "Dados cadastrados com sucesso", "id" => $usu_codigo);
        return $this->render("dados", NULL, TRUE);
    }

    /* ----------------------------------------------------------------
     * FIM MODULO E-SUS
     * ---------------------------------------------------------------- */
}