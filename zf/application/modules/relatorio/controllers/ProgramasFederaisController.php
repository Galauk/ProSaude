<?php

class Relatorio_ProgramasFederaisController extends Elotech_Controller_Action_Relatorio {
    
    public function init(){
        ini_set('memory_limit','-1');
        set_time_limit(100000000000);
    }
    
    public function indexAction(){
        
    }
    
    public function consultaDadosHorusWebserviceAction(){
        $this->view->title = "HORUS - Relatório Consulta de Dados WebService";
    }
    
    public function geraRelConsultaDadosWebServiceAction(){
        
    }
        
    public function consultaDadosHorusAction(){
        $this->view->title = "Horus - Relatório Por Consulta de Dados";
    }
    
    public function dadosRelHorusConsultaDadosAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Horus - Relatório Por Consulta de Dados";
        $dadosRelConsulta = array(
            "hor_dad_status_envio" => $this->_request->getPost("statusenvio"),
            "hor_dad_tpxml" => $this->_request->getPost("tpmov"),
            "hor_dad_nome_respenvio" => $this->_request->getPost("respenvio"),
            "hor_dad_numprotocolo_envio" => $this->_request->getPost("numprotocoloenvio"),
            "hor_dad_dtinicial" => $this->_request->getPost("dtinicial"),
            "hor_dad_dtfinal" => $this->_request->getPost("dtfinal"),
        );
        $tbHorDad = new Application_Model_HorusDados();
         $data_inicial = $this->_request->getPost("dtinicial", FALSE);
        $data_final = $this->_request->getPost("dtfinal", FALSE);
         $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        $this->view->dados = $tbHorDad->listaDadosRelConsultaDados($dadosRelConsulta);
    }
    
    //ID #106475
    public function relatorioAtividadeFiltroAction(){
        //Titulo do frame
        $this->view->title = "Atividade - Relatório Atividades";
        //Pega a lista de todas as atividades para a view
        $tbTpa = new Application_Model_TbCdsTipoAtivCol();
        $this->view->atividades = $tbTpa->getDadosTipoAtividade();
        //Pega a lista de Unidades para a view
        $tbUni = new Application_Model_Unidade();
        $this->view->unidade = $tbUni->getTodasUnidadesOrdenadoPorCnesAtivo();
    }
    
    public function relatorioAtividadeAction(){
        /*
         * Este metodo busca as atividades de acordo com os filtros selecionados
         * e os coloca em uma unica variavel.
        */
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Atividade - Relatório Atividades";
        //Pegando os paramentros dos filtros informados
        //PS: validação dos filtros esta na querry
        $dadosParaConsulta = array(
            "atividade" => $this->_request->getPost("atividade"),
            "unidade" => $this->_request->getPost("uni_codigo"),
            "responsavel" => $this->_request->getPost("usr_codigo"),
            "dataInicial" => $this->_request->getPost("dataInicial"),
            "dataFinal" => $this->_request->getPost("dataFinal"),
        );

        /*
         * Correção de bug, caso selecionar um profissional, e apagar o campo por completo,
         * atribuir ao usr_codigo o valor 0, para buscar todos os profissionais
        */
        if($this->_request->getPost("profs_part_nome") == ""){
            $dadosParaConsulta[responsavel] = 0;
        }
        
        //Função que ira buscar as Atividades
        $tbFic = new Application_Model_TbCdsFichaAtivCol();
        $todasAtividadesFiltradas = $tbFic->getAtividadesParaRelatorioAtividadeColetiva($dadosParaConsulta);
        //pega o total de participantes por unidade em um array
        $arrayComOsTotaisDeParticipantes = $this->pegaTotalDeParticipantesPorUnidade($todasAtividadesFiltradas);        
        $this->view->totalParticipantes = $arrayComOsTotaisDeParticipantes;
        // echo "<pre>";print_r($todasAtividadesFiltradas);die();
        //Transformar a row em array
        $todasAtividadesFiltradas = $this->transformarRowEmArray($todasAtividadesFiltradas);
        
        //Pega temas e coloca a lista de temas no array de uma atividade
        $todasAtividadesFiltradasComTemasPublicosPraticas = $this->pegarTemasPublicosPraticas($todasAtividadesFiltradas);
        $this->view->atividades = $todasAtividadesFiltradasComTemasPublicosPraticas; 
    }
    
    public function pegaTotalDeParticipantesPorUnidade($todasAtividadesFiltradas) {      
        /*
         * $arrayComValoresDosParticipantes;
         * Essa variavel sera um array que tera em cada indice correspondente ao
         * codigo da unidade, o valor total de participantes das atividades
         * da unidade correspondente
        */
        $arrayComValoresDosParticipantes;
        /*
         * inicia o controle do codigo como 0, assim cada novo uni_codigo identificado
         * é atribuido a este controle, para gravar qual unidade esta sendo somada
         * e atribuida no array com os valores no index correspondente a esta unidade
         * após a soma realizada
        */
        $controleCodigoUnidade = 0;
        /*
         * Inicia o contador de participantes
         */
        $participantesTotal = 0;
        
        for ($index = 0; $index < count($todasAtividadesFiltradas); $index++) {
            if($todasAtividadesFiltradas[$index][uni_codigo] != $controleCodigoUnidade){
                $arrayComValoresDosParticipantes[$controleCodigoUnidade] = $participantesTotal;                    
                $controleCodigoUnidade = $todasAtividadesFiltradas[$index][uni_codigo];
                $participantesTotal = 0;
            }
            $participantesTotal = $participantesTotal + $todasAtividadesFiltradas[$index][qt_participante_ativ];
            //Antes de matar o for, atribui o valor ao ultimo index, assim garantindo
            //a ultima unidade receber seu valor[**PONTO DE MELHORIA]
            if($index == count($todasAtividadesFiltradas)-1){
                $arrayComValoresDosParticipantes[$controleCodigoUnidade] = $participantesTotal; 
            }
        }
        return($arrayComValoresDosParticipantes);
    }
    
    public function transformarRowEmArray($todasAtividadesFiltradas){
        
        foreach ($todasAtividadesFiltradas as $atividade) {
            
            $atividadesArray[] = array('co_cds_ficha_ativ_col' => $atividade[co_cds_ficha_ativ_col],
                                        'data_atividade' => $atividade[data_atividade],
                                        'hora_inicio' => $atividade[hora_inicio],
                                        'hora_fim' => $atividade[hora_fim],
                                        'cod_equipe_ine' => $atividade[cod_equipe_ine],
                                        'qt_participante_ativ' => $atividade[qt_participante_ativ],
                                        'uni_codigo' => $atividade[uni_codigo],
                                        'usr_nome' => $atividade[usr_nome],
                                        'uni_desc' => $atividade[uni_desc],
                                        'total' => $atividade[total],
                                        'no_cds_tipo_ativ_col' => $atividade[no_cds_tipo_ativ_col]);
        }
        return $atividadesArray; 
    }
    
    public function pegarTemasPublicosPraticas($todasAtividadesFiltradas){
        
        //Tabela relacionamento dos Temas
        $tbRlTema = new Application_Model_RlCdsFichaAtivColTema();
        //Tabela relacionamento dos Temas
        $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
        //Tabela relacionamento das Praticas
        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        
        foreach ($todasAtividadesFiltradas as $atividade) {
            //Busca e adiciona temas
            $temas = $tbRlTema->getDadosPorId($atividade[co_cds_ficha_ativ_col]);
            $atividade['temas'] = $temas;
            //Busca e adiciona temas
            $publicos = $tbRlPub->getDadosPorId($atividade[co_cds_ficha_ativ_col]);
            $atividade['publicos'] = $publicos;
            //Busca e adiciona Praticas
            $praticas = $tbRlPrat->getDadosPorId($atividade[co_cds_ficha_ativ_col]);
            $atividade['praticas'] = $praticas;
            
            //Array ira salvar todas as atividades com seus temas / publicos / praticas
            $arrayAtividadesComTemasPublicosPraticas[] = $atividade;
        }
        return $arrayAtividadesComTemasPublicosPraticas;
    }
    
    public function formTotalExportaEnvioFichaEsusAction(){
        $this->view->action = array("action" => "rel-total-exporta-envio-ficha-esus");
        return $this->render("unidade-data", NULL, TRUE); // mostra action para pedir os dados
    }
    
    public function relTotalExportaEnvioFichaEsusAction(){
        
        $esusAtendIndividual = new Application_Model_EsusAtendimentoIndividual();
        $esusAtivColetiva = new Application_Model_EsusAtividadeColetiva();
        $esusCadastroIndividual = new Application_Model_EsusCadastroIndividual();
        $esusFichaProcedimento = new Application_Model_EsusFichaProcedimento();
        $esusOdonto = new Application_Model_EsusOdonto();
        $esusVisitaDomiciliar = new Application_Model_EsusVisitaDomiciliar();
        
        $unidade = new Application_Model_Unidade();
        
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        
        $uni_codigo = $this->_request->getPost("uni_codigo", FALSE);
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        
        $totalAtendIndividual = $esusAtendIndividual->getQuantidadeFichaExpAtendIndivudual($data_inicial, $data_final,$uni_codigo);
        $totalAtivColetiva = $esusAtivColetiva->getQuantidadeFichaExpAtivColetiva($data_inicial, $data_final,$uni_codigo);
        $totalCadastroIndividual = $esusCadastroIndividual->getQuantidadeFichaExpCadIndividual($data_inicial, $data_final,$uni_codigo);
        $totalFichaProcedimento = $esusFichaProcedimento->getQuantidadeFichaProcedimento($data_inicial, $data_final,$uni_codigo);
        $totalOdonto = $esusOdonto->getQuantidadeFichaOdonto($data_inicial, $data_final,$uni_codigo);
        $totalVisitaDomiciliar = $esusVisitaDomiciliar->getQuantidadeFichaVisitaDomiciliar($data_inicial, $data_final,$uni_codigo);  
        $todasUnidades = $unidade->getUnidadesAtendExportEsus($uni_codigo);
        
        
          
        
        
      
        
        $this->view->listUniCodigo = $todasUnidades;
        $this->view->totalAtendIndividual = $totalAtendIndividual;
        $this->view->totalAtivColetiva = $totalAtivColetiva;
        $this->view->totalCadastroIndividual = $totalCadastroIndividual;
        $this->view->totalFichaProcedimento = $totalFichaProcedimento;
        $this->view->totalOdonto = $totalOdonto;
        $this->view->totalVisitaDomiciliar = $totalVisitaDomiciliar;
    }

    public function formIndiceSaudeAvaliadaAction(){
        $this->view->title = "Índice de Atendimentos por Condição de Saúde Avaliada";
    }

    public function relIndiceSaudeAvaliadaAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbAte = new Application_Model_Atendimento();
        
        $data_inicial	= $this->_request->getPost("data_inicial", FALSE);
        $data_final		= $this->_request->getPost("data_final", FALSE);
        $nu_ine		= $this->_request->getPost("nu_ine", FALSE);
         $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;

        $this->view->title = "Quantidade de Atendimentos por Condição de Saúde Avaliada";
        $this->view->atendQtd = $tbAte->getNumeroAtendimentoCondicaoAvaliada();

    }
    
    public function formTotalEnvioFichaEsusPmaqAction(){
        $this->view->title = 'Relatório - Percentual de serviços essenciais em Atenção Básica e Odontologia realizados';
    }
    
    public function relTotalEnvioFichaEsusPmaqAction(){
        
        $esusAtendIndividual = new Application_Model_EsusAtendimentoIndividual();
        $esusAtivColetiva = new Application_Model_EsusAtividadeColetiva();
        $esusFichaProcedimento = new Application_Model_EsusFichaProcedimento();
        $esusOdonto = new Application_Model_EsusOdonto();
        
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        
        $ine = $this->_request->getPost("uni_codigo", FALSE);
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        $odonto_select = $this->_request->getPost("odonto_select", FALSE);
        
        $totalAtendIndividual = $esusAtendIndividual->getQuantidadeFichaExpAtendIndivudualPmaq($data_inicial, $data_final,$ine);
        $totalAtivColetiva = $esusAtivColetiva->getQuantidadeFichaExpAtivColetivaPmaq($data_inicial, $data_final,$ine);
        $totalFichaProcedimento = $esusFichaProcedimento->getQuantidadeFichaProcedimentoPmaq($data_inicial, $data_final,$ine);
        $totalOdonto = $esusOdonto->getQuantidadeFichaOdontoPmaq($data_inicial, $data_final,$ine);
        //Die(var_dump( $totalAtendIndividual->total));
       // die(var_dump("here"));
         $params = array($data_inicial = $data_inicial,
                            $data_final = $data_final,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
        
        if ($odonto_select == 0){
            $this->view->quant = "Quantitativo  Atenção Básica";
            $this->view->title = "Atenção Básica";
            $this->view->perc = "Percentual de serviços Equipe de Atenção Básica";
            $totalBasico = $totalAtendIndividual->total + $totalAtivColetiva->total + $totalFichaProcedimento->total;
            $this->view->totalBasico = $totalBasico;
        }else{
            $this->view->quant = "Quantitativo  Odontologia";
            $this->view->title = "Odontologia";
            $this->view->perc = "Percentual de serviços de Odontologia";
            $totalBasico = $totalOdonto->total;
            $this->view->totalBasico = $totalBasico;
        }
    }
    
}

?>