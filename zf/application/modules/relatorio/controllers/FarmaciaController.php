<?php

class Relatorio_FarmaciaController extends Elotech_Controller_Action_Relatorio {

	public function init() {
            $this->view->title = "Medicamentos dispensados";
	}

	public function indexAction(){
            $this->view->title = "Medicamentos dispensados por paciente e setor";
            $this->render("index");
	}
        
    public function relatorioAction(){
        $this->view->title = "Medicamentos dispensados por paciente e setor";
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        $set_codigo = $this->_request->getPost("set_codigo", FALSE);
        $tbMov = new Application_Model_Movimento();
        $this->view->itens = $tbMov->relDispensados($set_codigo, $data_inicial, $data_final)->toArray();
    }


    public function buscarAction(){
        $term = $this->_getParam("term", false);
        $tipo = $this->_getParam("tipo", false);
        if (!$term) {
            return false;
        }

        if ($tipo == "consolidado") {
            $tbUni = new Application_Model_Unidade();
            $this->view->dados = $tbUni->buscarLocais($term);
        } else if ($tipo == "individualizado") {
            $tbUni = new Application_Model_Unidade();
            $this->view->dados = $tbUni->buscarLocais($term);
        }

        return $this->render("dados", null, true);
    }
        
    public function formNumeroDePacientesAtendidosPorMedicamentoAction(){
        $this->view->title = "Número de pacientes atendidos por medicamento";
    }

    public function formLivroPisicotropicoAction(){
        $this->view->title = "Livro de Registro de Medicamentos Pisicotropicos";
    }

    public function numPacientesAtendidosPorMedicamentoDispensadoAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "NUMERO DE PACIENTES ATENDIDOS POR MEDICAMENTO";
        $tbMov = new Application_Model_Movimento();
        $tbUsr = new Application_Model_Usuarios();
        $codUnidade = $tbUsr->getUsrAtual()->uni_codigo;
        $codProd = $this->_request->getPost("pro_codigo");
        $codSetor = $this->_request->getPost("set_codigo");
        $setor = $this->_request->getPost("set_nome");
        $dataInicial = $this->_request->getPost("data_inicial");
        $dataFinal = $this->_request->getPost("data_final");
        $tbUsr = new Application_Model_Usuarios();
        $array = array('uni_desc'=> $tbUsr->getUsrAtual()->uni_desc,
                        'set_nome' => $setor ? $setor : "TODOS");
        if ($dataInicial)
            $array["data_inicial"] = $dataInicial;
        if ($dataFinal)
            $array["data_final"] = $dataFinal;
        
        $this->view->params = serialize($array);
        $this->view->dados = $tbMov->getNumPacientesAtendidosPorMedicamentoDispensado($codUnidade,$codProd,$codSetor,$dataInicial,$dataFinal); 
    }
        
    public function formNumeroPacientesAtendidosPorPeriodoSetorAction(){
        $this->view->title = "Número de Pacientes atendidos por período e setor";
    }

    public function numPacientesAtendidosPorPeriodoSetorAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Número de Pacientes atendidos por período e setor";
        // Parametros
        $tbMov = new Application_Model_Movimento();
        $tbUsr = new Application_Model_Usuarios();
        $codUnidade = $tbUsr->getUsrAtual()->uni_codigo;
        $codSetor = $this->_request->getPost("set_codigo");
        $setor = $this->_request->getPost("set_nome");
        $dataInicial = $this->_request->getPost("data_inicial");
        $dataFinal = $this->_request->getPost("data_final");
        $tbUsr = new Application_Model_Usuarios();
        $array = array(
            'uni_desc' => $tbUsr->getUsrAtual()->uni_desc,
            'set_nome' => $setor ? $setor : "TODOS"
        );
        if ($dataInicial){
            $array["data_inicial"] = $dataInicial;
        }

        if ($dataFinal){
            $array["data_final"] = $dataFinal;
        }

        // Dados título relatório
        $params = array(
            $data_inicial = $dataInicial,
            $data_final = $dataFinal,
            $uni_nome = $uni_desc
        );
        $this->view->params = $params;
        $dados = $tbMov->getNumPacientesAtendidosPorPeriodoSetor($codUnidade, $codSetor, $dataInicial, $dataFinal)->qtd_atendimento;
        $this->view->dados = $dados;
    }

    public function formPacientesFaltososAction(){
        $this->view->title = "Pacientes Faltasos segundo Dispensação";
    }

    public function relPacientesFaltososAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Pacientes Faltasos segundo Dispensação";
        $tbMov = new Application_Model_Movimento();
        $tbUsr = new Application_Model_Usuarios();
        $setor = $this->_request->getPost("set_codigo");
        $dataInicial = $this->_request->getPost("data_inicial");
        $dataFinal = $this->_request->getPost("data_final");
        //Seleciona todos os itens dispensados em um periodo de tempo//
        $iteMovs = $tbMov->getItensMovimentoDePrograma($setor, $dataInicial, $dataFinal);
        foreach ($iteMovs as $iteMov) {
            //Calcula o data de vencimento, ou seja, que o paciente deve vir buscar uma nova quantidade de medicamentos//
            $data1 = date('Y-m-d', strtotime('+' . $iteMov->ite_duracao - 1 . 'days', strtotime($iteMov->mov_data)));
            //Formata data 
            $myDateTime = DateTime::createFromFormat('d/m/Y', $dataFinal);
            $newDateString = $myDateTime->format('Y-m-d');            
            //$intervalo = date_diff(date_create($newDateString), date_create($data1))->format('%d');
            //Se data de vencimento for menor que a data final escolhida, realiza uma nova busca pra ver se no final da tabela, dentro do range de datas, nao existe uma outra dispensacao para o mesmo paciente que comprove que este nao eh faltante
            if ($data1 <= $newDateString) { 
                //Pesquisa se existe uma dispensaçao mais recente para esta situação 
                $dispRecente = $tbMov->verificaDispensacaoMaisRecente($setor, $data1, $iteMov->usu_codigo, $iteMov->pro_codigo, $dataFinal);
                //Se exitir
                if ($dispRecente->ite_codigo != null) {
                    //Calcula novamente a data de vencimento
                    $data1 = date('Y-m-d', strtotime('+' . $dispRecente->ite_duracao - 1 . 'days', strtotime($dispRecente->mov_data)));
                    //Se foi encontrado uma nova data de vencimento e esta continua menor que data final significa que o paciente eh faltoso
                    if ($data1 <= $newDateString) {
                        //die(var_dump("here"));
                        $dados_faltates[$dispRecente->ite_codigo] = $dispRecente;
                    }
                } else {
                    //Se nao foi encontrada uma nova data, significa que o paciente eh faltoso
                    $dados_faltates[$iteMov->ite_codigo] = $iteMov;
                }
            }
        }
        //die(var_dump($dados_faltates));
        $params = array(
            $data_inicial = $dataInicial,
            $data_final = $dataFinal,
            $uni_nome = $uni_desc
        );
        $this->view->params = $params;
        $this->view->dados_faltates = $dados_faltates;
    }


    public function relLivroPisicotropicoAction(){
        Zend_Layout::getMvcInstance()->setLayout("simples");
        $this->view->title = "Livro de Pisicotropicos";
        // Parametros
        $tbMov = new Application_Model_Movimento();
        $tbUsr = new Application_Model_Usuarios();
        $tbSal = new Application_Model_Saldo();
        $codUnidade = $tbUsr->getUsrAtual()->uni_codigo;
        $codSetor = $this->_request->getPost("set_codigo");
        $setor = $this->_request->getPost("set_nome");
        $dataInicial = $this->_request->getPost("data_inicial");
        $dataFinal = $this->_request->getPost("data_final");
        $tbUsr = new Application_Model_Usuarios();
        $array = array(
            'uni_desc' => $tbUsr->getUsrAtual()->uni_desc,
            'set_nome' => $setor ? $setor : "TODOS"
        );

        if ($dataInicial){
            $array["data_inicial"] = $dataInicial;
        }

        if ($dataFinal){
            $array["data_final"] = $dataFinal;
        }

        // Dados título relatório
        $params = array(
            $data_inicial = $dataInicial,
            $data_final = $dataFinal,
            $uni_nome = $uni_desc
        );

        $this->view->params = $params;
        // = $sec;
        $dados = $tbMov->getItemPsicoDispensados($codSetor, $dataInicial, $dataFinal);
        $this->view->dados = $dados;
    }


    public function formEntradasSaidasAction($codSetor,  $dataInicial, $dataFinal){
        $this->view->title = "Entradas e Saidas";
    }

    public function relEntradasSaidasAction($codSetor, $dataInicial, $dataFinal){
        $codSetor = $this->_request->getPost("set_codigo");
        $dataInicial = $this->_request->getPost("data_inicial");
        $dataFinal = $this->_request->getPost("data_final");
        $this->view->title = "Entradas e Saidas";
        $tbMov = new Application_Model_Movimento();
        $tbSec = new Application_Model_Secretaria();
        $sec = $tbSec->getDadosSec();
        $dados = $tbMov->getDadosRelarioEntradasSaidas($codSetor, $dataInicial, $dataFinal);
        $params = array(
            $data_inicial = $dataInicial,
            $data_final = $dataFinal,
            $set_codigo = $set_codigo
        );
        $this->view->sec = $sec;
        $this->view->params = $params;
        $this->view->dados = $dados;
    }

    public function componenteEspecializadoAction(){
        $this->view->title = "Componente Especializado da Assistência Técnica";
    }

    public function solicitacaoMedicamentosAction(){
        $this->view->title = "Solicitação de Medicamentos";

    }
}