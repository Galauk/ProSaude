<?php

class Prontuario_OdontogramaController extends Zend_Controller_Action {
    /* ------------------------------------------------/
     * GERAL                                           / 
     * ----------------------------------------------- */

    // Função chamada ao iniciar a página
    public function init() {
        //$this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.printElement.js');
        $this->_helper->acl->copiarPermissao("zf/prontuario/index");
        Zend_Layout::getMvcInstance()->setLayout("prontuario");
        $this->view->title = "Odontograma";
    }

    // Chama a tela de listagem de tratamento
    public function indexAction() {
        // Definindo layout da página
        $this->_helper->layout->setLayout("prontuario");
        // Setando título da página
        $this->view->title = "Odontograma";
        // Código do usuário que está em consulta
        $usuCodigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
        // Pegando código do tratamento em aberto
        if ($this->_request->getPost("odo_trat_codigo") != "") {
            $tratCodigo = $this->_request->getPost("odo_trat_codigo");
        } else {
            $tratCodigo = $this->getCodigoTratamentoAtual();
        }
        // Enviando código do tratamento para a página
        $this->view->tratCodigo = $tratCodigo;
        // Se dados tratamento existir carrega tratamento
        if (!empty($tratCodigo)) {
            $this->view->historicoProcRealizados = $this->model("procedimentosRealizados")->listaProcedimentosRealizados($tratCodigo)->toArray();
            //se não traz tela bloqueada pela requisição ajax
        } else {
            $this->view->historicoTratamentos = $this->model("tratamento")->listaTratamentosRealizados($usuCodigo);
        }
    }

    public function imprimirProcedimentosAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();


        // Pegando lista de procedimentos a realizar e encaminhando para view
        $this->view->historicoProcaRealizar = $this->model("procedimentos")->listaProcedimentos($this->getCodigoTratamentoAtual());
        // Pegando lista de procedimentos realizados e encaminhando dados para a view
        $this->view->historicoProcRealizado = $this->model("procedimentosRealizados")->listaProcedimentosRealizados($this->getCodigoTratamentoAtual());
    }

    public function imprimirOdontogramaAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Pegando lista de procedimentos a realizar e encaminhando para view
        $this->view->historicoProcaRealizar = $this->model("procedimentos")->listaProcedimentos($this->getCodigoTratamentoAtual());
        // Pegando lista de procedimentos realizados e encaminhando dados para a view
        $this->view->historicoProcRealizado = $this->model("procedimentosRealizados")->listaProcedimentosRealizados($this->getCodigoTratamentoAtual());
    }

    /* ------------------------------------------------------/
     * FIM DA CODIFICAÇÃO GERAL                              /   
     * ------------------------------------------------------ */

    /* ------------------------------------------------/
     * TRATAMENTOS                                     / 
     * ----------------------------------------------- */

    // Salva o cadastro de um novo tratamento
    public function salvarTratamentoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Busca código de atendimento, verificar se é isto mesmo
        $ateCodOrigem = $this->model("atendimento")->buscaRetornoOrigem()->ate_codigo;            // Array de dados tratamento
        // Array de dados tratamento
        $data = array(
            "odo_trat_dtinicial" => date("d-m-y H:i:s"),
            "odo_trat_status" => 'A',
            "ate_codigo_origem" => $ateCodOrigem
        );
        // Salvando tratamento
        $this->model("tratamento")->salvar($data);
    }

    // Faz as validações e finaliza o tratamento
    public function finalizarTratamentoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Confere se não tem atendimento pra ser realizado
        $qtdTrat = $this->model("tratamento")->getQtdAtendimentoFaltanteTratamento($this->getCodigoTratamentoAtual())->qtd_atendimento;
        // Se não tiver ele finaliza o tratamento
        if ($qtdTrat == "0") {
            // Criando array de atualização
            $dadosTrat = array(
                "odo_trat_codigo" => $this->getCodigoTratamentoAtual(),
                "odo_trat_dtfinal" => date("Y-m-d H:i:s"),
                "odo_trat_status" => "F"
            );
            // Atualizando dados
            $this->model("tratamento")->salvar($dadosTrat);
            // Efetuando retorno para o jquery
            $this->view->dados = "ok";
            return $this->render("dados", NULL, TRUE);
            // Se tiver retorna erro    
        } else {
            // Efetuando retorno de erro pro Jquery
            $this->view->dados = "erro";
            return $this->render("dados", NULL, TRUE);
        }
    }

    public function listaTratamentosRealizadosAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        //
        $usuCodigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
        // Listando os tratamentos realizados
        $this->view->historicoTratamentos = $this->model("tratamento")->listaTratamentosRealizados($usuCodigo);
    }

    public function consultaTratamentoAction() {
        // Desabilitando Layout 
        $this->_helper->layout->disableLayout();
        // Pega codigo do tratamento especificado
        //$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/prontuario/odontograma/consulta.css');
        // Pega código do tratamento
        $tratCodigo = $this->_getParam("tratCodigo");
        // Pegando os procedimentos realizados de acordo com o tratamento
        $this->view->historicoProcRealizados = $this->model("procedimentosRealizados")->listaProcedimentosRealizados($tratCodigo)->toArray();
    }

    // Pega código do tratamento atual
    public function getCodigoTratamentoAtual() {
        // Pegando o código do paciente que esta consultando
        $usuCodigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
        // Pegando dados do último tratamento se existir
        $tratCodigo = $this->model("tratamento")->getCodigoTratamentoAtual($usuCodigo)->odo_trat_codigo;
        // Retornando código do tratamento atual
        return $tratCodigo;
    }

    /* ------------------------------------------------------/
     * FIM DA CODIFICAÇÃO DE TRATAMENTOS                     /   
     * ----------------------------------------------------- */

    /* ------------------------------------------------/
     * PROCEDIMENTOS                                   / 
     * ----------------------------------------------- */

    // Chama o model de procedimento de acordo com o dente e traz o histórico
    public function cadastraProcedimentoAction() {
        // Desabilitando layout
        $this->_helper->layout->disableLayout();
        // Dente que está sendo executado o procedimento
        $dente = $this->_getParam("dente", FALSE);
        // Dados usuário e tratamento
        $usuCodigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
        // Código do tratamento atual
        $tratCodigo = $this->getCodigoTratamentoAtual();
        // Enviando dados do dente para o modal
        $this->view->dente = $dente;
        // Procedimentos SIGTAP
        $this->view->situacao = $this->model("procedimentos")->getProcedimentosOdontologicos()->toArray();
        // HIstórico de procedimentos realizados no dente
        $this->view->historicoDente = $this->model("procedimentosRealizados")->getProcedimentosDente($dente, $tratCodigo, $usuCodigo);
    }

    public function listaProcedimentosAction() {
        //Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Código do tratamento atual
        $tratCodigo = $this->getCodigoTratamentoAtual();
        // Histórico de procedimentos a realizar 
        $this->view->historicoProcaRealizar = $this->model("procedimentos")->listaProcedimentos($tratCodigo)->toArray();
    }

    // Salvando o procedimento
    public function salvarProcedimentoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Verificando se o procedimento vai ser realizado ou está sendo realizado
        if ($this->_request->getPost("odo_proc_status") == "true") {
            // Criando array de procedimentos realizados
            $dadosProcReal = array(
                "proc_codigo" => $this->_request->getPost("proc_codigo"),
                "odo_preal_dentenum" => $this->_request->getPost("odo_proc_dentenum"),
                "odo_preal_denteface" => $this->_request->getPost("odo_proc_denteface"),
                "odo_preal_denteanot" => $this->_request->getPost("odo_proc_denteanot")
            );
            // Pegando código do procedimento controle, atraves do método e inserindo no array
            $dadosProcReal["odo_pcon_codigo"] = $this->getCodigoProcedimentoControle();
            // Salvando em banco
            $this->model("procedimentosRealizados")->salvar($dadosProcReal);
            // Enviando o código do procedimento realizado para o jquery
            $this->view->dados = $this->model("procedimentosRealizados")->getUltimoProcedimentoRealizado($this->getCodigoTratamentoAtual())->odo_preal_codigo;
            return $this->render("dados", NULL, TRUE);
        } else {
            // Colocando os posts de dados do procedimento em array
            $dadosProc = $this->insereDadosArray($_POST);
            // Pegando código do procedimento controle, atraves do método e inserindo no array
            $dadosProc["odo_pcon_codigo"] = $this->getCodigoProcedimentoControle();
            // Inserindo no banco de dados o procedimento
            $this->model("procedimentos")->salvar($dadosProc);
            // Enviando retorno para o jquery
            $this->view->dados = "procedimentoInserido";
            return $this->render("dados", NULL, TRUE);
        }
    }

    /* ------------------------------------------------------/
     * FIM DA CODIFICAÇÃO DE PROCEDIMENTOS                   /   
     * ----------------------------------------------------- */

    /* ------------------------------------------------/
     * PROCEDIMENTOS CONTROLE                          / 
     * ----------------------------------------------- */

    // Pega código do procedimento controle, validando se já existe ou não
    public function getCodigoProcedimentoControle() {
        // Dados Procedimento controle
        $tratCodigo = $this->getCodigoTratamentoAtual();
        $ateCodigo = $this->model("atendimento")->buscaRetornoOrigem()->ate_codigo;
        $procConCodigo = $this->model("procedimentosControle")->getCodigoTratamentoAtendimento($tratCodigo, $ateCodigo)->odo_pcon_codigo;
        // Verificando se não existe um atendimento de controle para o Tratamento, se não existir insere
        if (empty($procConCodigo)) {
            // Array Dados procedimento controle
            $dadosProcCon = array(
                "odo_trat_codigo" => $tratCodigo,
                "ate_codigo" => $ateCodigo
            );
            $this->model("procedimentosControle")->salvar($dadosProcCon);
            $procConCodigo = $this->model("procedimentosControle")->getCodigoTratamentoAtendimento($tratCodigo, $ateCodigo)->odo_pcon_codigo;
            return $procConCodigo;
        } else {
            return $procConCodigo;
        }
    }

    /* ------------------------------------------------------/
     * FIM DA CODIFICAÇÃO DE PROCEDIMENTOS CONTROLE          /   
     * ----------------------------------------------------- */

    /* ------------------------------------------------/
     * PROCEDIMENTOS REALIZADOS                        / 
     * ----------------------------------------------- */

    // Função que lista os procedimentos realizados
    public function listaProcedimentosRealizadoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Pega código do tratamento atual ou o desejado
        if ($this->_request->getPost("odo_trat_codigo") != "") {
            $tratCodigo = $this->_request->getPost("odo_trat_codigo");
        } else {
            $tratCodigo = $this->getCodigoTratamentoAtual();
        }
        // Pega código do procedimento, caso queira listar somente ele
        $procRealCodigo = $this->_request->getPost("odo_preal_codigo");
        $this->view->dados = $this->transformaEmArray($this->model("procedimentosRealizados")->listaProcedimentosRealizados($tratCodigo, $procRealCodigo)->toArray());
        // Retornando dados para o jquery
        return $this->render("dados", NULL, TRUE);
    }

    // Função que lista os procedimentos realizados consulta
    public function listaProcedimentosRealizadoConsultaAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Pega código do tratamento atual ou o desejado
        if ($this->_request->getPost("odo_trat_codigo") != "") {
            $tratCodigo = $this->_request->getPost("odo_trat_codigo");
        } else {
            $tratCodigo = $this->getCodigoTratamentoAtual();
        }
        // Pega código do procedimento, caso queira listar somente ele
        $procRealCodigo = $this->_request->getPost("odo_preal_codigo");
        $this->view->dados = $this->transformaEmArrayConsulta($tratCodigo, $this->model("procedimentosRealizados")->listaProcedimentosRealizados($tratCodigo, $procRealCodigo)->toArray());
        // Retornando dados para o jquery
        return $this->render("dados", NULL, TRUE);
    }

    // Função que salva os do procedimento para procedimento, pela requisição ajax
    public function salvarProcedimentoRealizadoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Pega os dados do procedimentos
        $dadosProc = $this->model("procedimentos")->getProcedimento($this->_request->getPost("odo_proc_codigo"));
        // Jogando os dados no array de procedimentos realizados
        $dadosProcReal = array(
            "proc_codigo" => $dadosProc->proc_codigo,
            "odo_preal_dentenum" => $dadosProc->odo_proc_dentenum,
            "odo_preal_denteface" => $dadosProc->odo_proc_denteface,
            "odo_preal_denteanot" => $dadosProc->odo_proc_denteanot
        );
        // Pegando código do procedimento controle, atraves do método e inserindo no array
        $dadosProcReal["odo_pcon_codigo"] = $this->getCodigoProcedimentoControle();
        // Salva em banco
        $this->model("procedimentosRealizados")->salvar($dadosProcReal);
        // Atualiza o status do procedimento que estava a realizar
        $dadosProcAtu = array(
            "odo_proc_codigo" => $this->_request->getPost("odo_proc_codigo"),
            "odo_proc_status" => "TRUE"
        );
        // Atualizando status
        $this->model("procedimentos")->salvar($dadosProcAtu);
        // Retornando o código do procedimento para o ajax
        $this->view->dados = $this->model("procedimentosRealizados")->getUltimoProcedimentoRealizado($this->getCodigoTratamentoAtual())->odo_preal_codigo;
        // Enviando para o ajax
        return $this->render("dados", NULL, TRUE);

        // Pega código do procedimento, caso queira listar somente ele
        $procRealCodigo = $this->_request->getPost("odo_preal_codigo");
        // Pega dados dos procedimentos realizados de acordo com o tratamento para pintar os dentes
        $this->view->dados = $this->transformaEmArray($this->model("procedimentosRealizados")->listaProcedimentosRealizados($tratCodigo, $procRealCodigo)->toArray());
        // Retornando dados para o jquery
        return $this->render("dados", NULL, TRUE);
    }

    // Salva posts em banco de dados
    public function salvarPostsProcedimentoRealizadoAction() {
        // Desabilita Layout 
        $this->_helper->layout->disableLayout();
        // Jogando os posts para construção do array
        $dadosProcReal = $this->insereDadosArray($_POST);
        // Salvando em banco
        $this->model("procedimentosRealizados")->salvar($dadosProcReal);
    }

    // Função que remove o procedimento selecionado
    public function excluirProcedimentoRealizadoAction() {
        // Desabilitando layout 
        $this->_helper->layout->disableLayout();
        // Removendo procedimento selecionado
        $this->model("procedimentosRealizados")->excluirProcedimentoRealizado($this->_request->getPost("odo_preal_codigo"));
    }

    public function editaProcedimentoRealizadoAction() {
        // Desabilitando o layout
        $this->_helper->layout->disableLayout();
        // Pegando os dados do procedimentos a ser editado
        $dadosProcReal = $this->model("procedimentosRealizados")->getProcedimentoRealizado($this->_getParam("procRealCodigo"));
        // Procedimentos SIGTAP
        $this->view->situacao = $this->model("procedimentos")->getProcedimentosOdontologicos()->toArray();
        // Enviando os dados para view
        $this->view->dados = $dadosProcReal;
    }

    // Lista o último procedimento realizado para inserção no jQuery
    public function getProcedimentoRealizadoAction() {
        // Desabilitando Layout
        $this->_helper->layout->disableLayout();
        // Código do tratamento atual
        $tratCodigo = $this->getCodigoTratamentoAtual();
        // Pegando dados do ultimo procedimento realizado e encaminhando para o ajax
        $this->view->dados = $this->model("procedimentosRealizados")->getProcedimentoRealizado($this->_request->getPost("odo_preal_codigo"))->toArray();
        // Enviando dados ajax
        $this->render("dados", NULL, TRUE);
    }

    // Transforma os procedimentos realizados em um único array por causa das faces
    public function transformaEmArray($dadosProcReal) {
        // Transformando o array para o jquery pintar os dentes
        foreach ($dadosProcReal as $procReal) {
            // Colocando cada face do procedimento em array 
            $faces = array();
            for ($i = 0; $i < strlen($procReal["odo_preal_denteface"]); $i++) {
                //echo $procReal["odo_preal_denteface"]."<br />";
                $caracter = substr($procReal["odo_preal_denteface"], $i, 1);
                $faces[] = $caracter;
            }
            // Criando array transformado
            $dadosProcRealTrat[] = array(
                "n" => $procReal["odo_preal_dentenum"],
                "f" => $faces,
                "s" => $procReal["proc_codigo"],
                "e" => "0"
            );
            // Validação de exodontia, pega código do usuário em que esta aberto o prontuario e verifica se alguma vez foi realizada exodontia
        }
        // Incrementando array de dentes que foram realizados procedimentos e precisa de validação
        $usu_codigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
        $dadosVal = $this->model("procedimentosRealizados")->listaDentesQuePrecisaValidacao($usu_codigo);
        // Quantidade de registros no array de procedimentos realizados
        $y = count($dadosProcRealTrat);
        foreach ($dadosVal as $dados) {
            // Colocando cada face do procedimento em array 
            $faces = array();
            for ($i = 0; $i < strlen($dados["odo_preal_denteface"]); $i++) {
                //echo $procReal["odo_preal_denteface"]."<br />";
                $caracter = substr($dados["odo_preal_denteface"], $i, 1);
                $faces[] = $caracter;
            }
            $dadosProcRealTrat[$y]["n"] = $dados["odo_preal_dentenum"];
            $dadosProcRealTrat[$y]["f"] = $faces;
            $dadosProcRealTrat[$y]["s"] = $dados["proc_codigo"];
            $dadosProcRealTrat[$y]["e"] = "1";
            $y++;
        }
        return $dadosProcRealTrat;
    }

    // Transforma os procedimentos realizados em um único array por causa das faces
    public function transformaEmArrayConsulta($tratCodigo, $dadosProcReal) {
        // Transformando o array para o jquery pintar os dentes
        $y = 0;
        foreach ($dadosProcReal as $procReal) {
            // Colocando cada face do procedimento em array 
            $faces = array();
            for ($i = 0; $i < strlen($procReal["odo_preal_denteface"]); $i++) {
                //echo $procReal["odo_preal_denteface"]."<br />";
                $caracter = substr($procReal["odo_preal_denteface"], $i, 1);
                $faces[] = $caracter;
            }
            // Criando array transformado
            $dadosProcRealTrat[] = array(
                "n" => $procReal["odo_preal_dentenum"],
                "f" => $faces,
                "s" => $procReal["proc_codigo"]
            );
            // Incrementando array de dentes que foram realizados procedimentos e precisa de validação
            $usu_codigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
            $confExo = $this->model("procedimentosRealizados")->confereProcedimentosRealizadosExodontiaPorUsu($tratCodigo, $usu_codigo, $procReal["odo_preal_dentenum"])->confExodontia;
            if ($confExo > 0) {
                $dadosProcRealTrat[$y]["e"] = "1";
            } else {
                $dadosProcRealTrat[$y]["e"] = "0";
            }
            $y++;
            // Validação de exodontia, pega código do usuário em que esta aberto o prontuario e verifica se alguma vez foi realizada exodontia
        }
        return $dadosProcRealTrat;
    }

    // Método que confere se o procedimento foi realizado no atendimento
    public function confereProcedimentoRealizadoAtendimentoAction($procRealCodigo = FALSE) {
        // Conferindo se o dado esta vindo por post
        if ($this->_request->getPost("odo_preal_codigo") != "") {
            $procRealCodigo = $this->_request->getPost("odo_preal_codigo");
        }
        // Dados Procedimento controle
        $tratCodigo = $this->getCodigoTratamentoAtual();
        $ateCodigo = $this->model("atendimento")->buscaRetornoOrigem()->ate_codigo;
        // Método que confere se o procedimento foi realizado no atendimento ou não
        $retorno = $this->model("procedimentosRealizados")->confereProcedimentoRealizadoAtendimento($tratCodigo, $ateCodigo, $procRealCodigo)->odo_preal_codigo;
        // Valida retorno
        if ($retorno != "") {
            $this->view->dados = "true";
            return $this->render("dados", NULL, TRUE);
        } else {
            $this->view->dados = "false";
            return $this->render("dados", NULL, TRUE);
        }
    }

    /* ------------------------------------------------------/
     * FIM DA CODIFICAÇÃO DE PROCEDIMENTOS REALIZADOS        /   
     * ----------------------------------------------------- */

    /* ------------------------------------------------------/
     * FUNÇÕES GERAIS                                        /
     * ----------------------------------------------------- */

    public function insereDadosArray($post) {
        $array = array();
        while (list($key, $val) = each($post)) {
            $array[$key] = $val;
            //echo $key." - ".$val."/n";
        }
        return $array;
    }

    // Instância os models utilizados para não ter que ficar chamando
    public function model($model = FALSE) {
        switch ($model) {
            case "atendimento":
                return new Application_Model_Atendimento();
                break;
            case "tratamento":
                return new Application_Model_OdontoTratamento();
                break;
            case "procedimentos":
                return new Application_Model_OdontoProcedimentos();
                break;
            case "procedimentosControle":
                return new Application_Model_OdontoProcedimentosControle();
                break;
            case "procedimentosRealizados":
                return new Application_Model_OdontoProcedimentosRealizados();
                break;
        }
    }

    public function procedimentosAction() {
        $tbOd = new Application_Model_Odonto();

        $id = $this->_getParam("id", FALSE);
        if ($id)
            $procedimentos = $tbOd->getHistorico($id);
        else
            $procedimentos = $tbOd->getTodosProcedimentos(FALSE);

        $this->view->dados = $tbOd->toJson($procedimentos);
        $this->view->historico = $tbOd->getTodosProcedimentos(FALSE);

        return $this->render("dados", NULL, TRUE);
    }

    /* ------------------------------------------------------/
     * FIM DAS FUNÇÕES GERAIS                                /   
     * ----------------------------------------------------- */


    /**
     * Os procedimentos realizados nos dentes são carregados por ajax
     * Esta action devolve os os procedimentos realizado, em json.
     * Se informar o id (get) ela irá retornar apenas o procedimento feito
     * no odonto_historico com essa pk.
     * Se informar o dente (get) ela irá filtrar apenas os procedimentos daquele
     * dente
     */
    /*




      public function infoEditaAction(){
      $tbOd =  new Application_Model_Odonto();
      $tbOdt = new Application_Model_OdontoHistorico();
      $this->_helper->layout->disableLayout();
      $idProc = $this->_getParam("procNum", false);
      $this->view->situacao = $tbOd->getProcedimentosOdontologicos()->toArray();
      $this->view->dados = $tbOdt->getProcedimentoEditado($idProc);
      //$this->render("dados",NULL,true);
      //die("aaaaaaaaa");
      }

      public function listaAction(){
      /*
      $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/prontuario/odontograma.js');
      $this->_helper->layout->disableLayout();
      $tbOd = new Application_Model_Odonto();
      $this->view->historico = $tbOd->getTodosProcedimentos(FALSE);
     */
    /*
      $this->_helper->layout->disableLayout();
      $age = Application_Model_Agendamento::usuEmAberto();
      $tbOdh = new Application_Model_OdontoHistorico();
      //$this->view->dados = $tbOd->getTodosProcedimentos(FALSE)->toArray();
      $age_codigo = $age->age_codigo;
      $this->view->dados = $tbOdh->getUltimoProcedimento($age_codigo)->toArray();
      return $this->render("dados", NULL, TRUE);
      }

     * 
     */
    
     public function buscarProcedimentoOdontologicoAction(){
        $term = $this->_getParam("term", FALSE);
        $tbOdop = new Application_Model_OdontoProcedimentos();
        if($term)
            $this->view->dados = $tbOdop->buscaProcedimentosOdontologicos($term);
        
        
        
        return $this->render("dados",null,true);
        
        
    }
}
