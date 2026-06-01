<?php

class ProgramasFederais_ConsumoAlimentarController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->title = "Marcadores de Consumo Alimentar";
        $this->_helper->acl->allow(NULL);
    }

    public function indexAction()
    {
        $tbAcessosFichaEsus = new Application_Model_AcessosFichaEsus();
        $tbUsr = new Application_Model_Usuarios();
        $tbEsp = new Application_Model_Especialidade();
        $usr = $tbUsr->getUsrAtual();
        if ($usr->usr_digitador == 'N') {
            if (!$usr->esp_codigo) {
                return $this->_redirect("/atendimento/atendimento-simplificado/erro");
            } else {
                $especialidade = $tbEsp->getEspecialidade($usr->esp_codigo);
                if (!$tbAcessosFichaEsus->getAcessosFichaEsus($especialidade->cod_cbo, "ca") && $usr->usr_digitador == 'N') {
                    return $this->_redirect("/atendimento/atendimento-simplificado/erro");
                }
            }
        }
        $tbAtiv = new Application_Model_TbCdsConsumoAlimentar();
        $this->view->dados = $tbAtiv->getDados();
    }

    public function buscarAction()
    {
        $busca = $this->_request->getPost("busca");
        $tipoBusca = $this->_request->getPost("tipo_busca");
        $tbAtiv = new Application_Model_TbCdsConsumoAlimentar();
        $this->view->dados = $tbAtiv->busca($busca, $tipoBusca);
        return $this->render("index");
    }

    public function formAction()
    {
        $tbLocal = new Application_Model_TbLocalAtend();
        $tbQuestao = new Application_Model_TbQstQuestao();
        $questoes = array();

        //Preenche o local de atendimento
        $this->view->selectLocais = $tbLocal->selectTag();

        //Preenche as perguntar de radio buttom
        $this->view->perguntaMenorSeis = $tbQuestao->getDadosPerguntasMenorSeis()->toArray();
        $this->view->perguntaMaiorSeis = $tbQuestao->getDadosPerguntasMaiorSeis()->toArray();
        $this->view->perguntaMaiorDois = $tbQuestao->getDadosPerguntasMaiorDois()->toArray();

        foreach ($this->view->perguntaMenorSeis as $codQuestao1) {
            array_push($questoes, $codQuestao1['co_qst_questao']);
        }
        foreach ($this->view->perguntaMaiorSeis as $codQuestao2) {
            array_push($questoes, $codQuestao2['co_qst_questao']);
        }
        foreach ($this->view->perguntaMaiorDois as $codQuestao3) {
            array_push($questoes, $codQuestao3['co_qst_questao']);
        }

        $codQuestoes = implode(',', $questoes);

        $this->view->respostas = $tbQuestao->getDadosResposta($codQuestoes)->toArray();

        $codFicha = $this->_request->getParam("id");

        if ($codFicha) {
            $tbConsumo = new Application_Model_TbCdsConsumoAlimentar();
            $this->view->dados = $tbConsumo->carregaDados($codFicha);
            $this->view->selectLocais = $tbLocal->selectTag($this->view->dados['co_local_atend']);
        }
    }

    public function salvarAction()
    {
        $this->_helper->layout->disableLayout();
        $ate_codigo = $this->_request->getParam("ate_codigo", NULL);
        $age_codigo = $this->_request->getParam("age_codigo", NULL);
        $tbUsuarios = new Application_Model_Usuarios();
        $tbAge = new Application_Model_Agendamento();
        $tbAte = new Application_Model_Atendimento();
        $tbCons = new Application_Model_TbCdsConsumoAlimentar();
        $tbQuestao = new Application_Model_TbQstQuestao();
        $tbPergRespos = new Application_Model_TbCdsConsumoAlimentarResposta();
        $dadosUsuarios = $tbUsuarios->getUsrAtual();

        $dadosAge = array(
            "age_codigo" => $age_codigo,
            "age_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "age_horario" => date("H:i"),
            "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
            "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
            "age_status" => 'A',
            "age_atendido" => 'A',
            "age_paciente" => $this->_request->getPost("usu_nome", FALSE),
            "uni_codigo" => $dadosUsuarios->uni_codigo,
            "esp_codigo" => $this->_request->getPost("esp_codigo", NULL),
            "usr_codigo_cad" => $dadosUsuarios->usr_codigo,
            "dt_cadastro" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
            "dt_atualizacao" => "NOW()",
            "age_data_atend" => "NOW()",
            "age_emergencia" => 'N',
            "usr_cod_status" => $_SESSION[id_login]
        );
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();

        try {
            $ageCodigo = $this->_request->getParam("age_codigo");
            if (!$ageCodigo) {
                $ageCodigo = $tbAge->salvarAgendamento($dadosAge);
            }

            // Salvar Atendimento
            $dadosAte = array(
                "ate_codigo" => $ate_codigo,
                "ate_data" => $this->_request->getPost("data_atendimento", FALSE) != "" ? $this->_request->getPost("data_atendimento", FALSE) : "NOW()",
                "ate_hora" => date("H:i"),
                "ate_reclamacao" => $this->_request->getPost("agee_observacao", FALSE),
                "med_codigo" => $this->_request->getPost("usr_codigo", FALSE),
                "ate_reclamacao" => $this->_request->getPost("agee_observacao", NULL),
                "usu_codigo" => $this->_request->getPost("usu_codigo", FALSE),
                "age_codigo" => $ageCodigo,
                "ate_valor_proc" => "0.00",
                "uni_codigo" => $this->_request->getPost("uni_codigo", FALSE),
                "ate_data_insert" => "NOW()",
                "ate_simplificado" => TRUE,
                "co_local_atend" => $this->_request->getPost("co_local_atend", FALSE),
                "ate_nasf_aval" => ($this->_request->getPost("ate_nasf_aval") != "" ? "t" : "f"),
                "ate_nasf_proc" => ($this->_request->getPost("ate_nasf_proc") != "" ? "t" : "f"),
                "ate_nasf_presc" => ($this->_request->getPost("ate_nasf_presc") != "" ? "t" : "f"),
                "ate_tipo" => ($this->_request->getPost("ate_tipo_atendimento", NULL)),
                "turno" => ($this->_request->getPost("turno", NULL)),
                "st_fora_area" => ($this->_request->getPost("st_fora_area") != "" ? "t" : "f"),
                "st_visita_compartilhada" => ($this->_request->getPost("st_visita_compartilhada") != "" ? "t" : "f")
            );

            // Salva atendimento
            $ateCodigo = $tbAte->salvarAtendimento($dadosAte);

            $codConsumo = $this->_request->getParam("id");

            $dataConsumo = array("co_seq_cds_consumo_alimentar" => null, "ate_codigo" => $ateCodigo);

            if ($codConsumo) {
                $dataConsumo = array("co_seq_cds_consumo_alimentar" => $codConsumo, "ate_codigo" => $ateCodigo);
            }

            $codConsumo = $tbCons->salvar($dataConsumo);

            $tbPergRespos->excluir($codConsumo);

            $perguntaMenorSeis = $tbQuestao->getDadosPerguntasMenorSeis()->toArray();
            $perguntaMaiorSeis = $tbQuestao->getDadosPerguntasMaiorSeis()->toArray();
            $perguntaMaiorDois = $tbQuestao->getDadosPerguntasMaiorDois()->toArray();

            if ($this->_request->getPost("menorSeis1")) {
                // Salva respostas para cada pergunta
                foreach ($perguntaMenorSeis as $codQuestao1) {
                    $this->salvarRespostas($codConsumo,
                        $codQuestao1['co_qst_questao'],
                        $this->_request->getPost("menorSeis" . $codQuestao1['co_qst_questao']));
                }
            }

            if ($this->_request->getPost("maiorSeis21")) {
                foreach ($perguntaMaiorSeis as $codQuestao2) {
                    $this->salvarRespostas($codConsumo,
                        $codQuestao2['co_qst_questao'],
                        $this->_request->getPost("maiorSeis" . $codQuestao2['co_qst_questao']));
                }
            }

            if ($this->_request->getPost("maiorDois11")) {
                foreach ($perguntaMaiorDois as $codQuestao3) {
                    if ($codQuestao3['co_qst_questao'] == 12) {
                        $checkBox = $this->_request->getPost("maiorDois");
                        foreach ($checkBox as $value) {
                            $this->salvarRespostas($codConsumo,
                                12,
                                $value
                            );
                        }
                    } else {
                        $this->salvarRespostas($codConsumo,
                            $codQuestao3['co_qst_questao'],
                            $this->_request->getPost("maiorDois" . $codQuestao3['co_qst_questao']));
                    }
                }
            }
            Zend_Db_Table::getDefaultAdapter()->commit();
            return $this->_redirect("/programasfederais/consumo-alimentar/index");
//die("asdfasdf");
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados", NULL, TRUE);
        }
    }

    public
    function salvarRespostas($codConsumo, $codPergunta, $codResposta)
    {
        $tbPergRespos = new Application_Model_TbCdsConsumoAlimentarResposta();
        try {
            $dataRespostas = array("co_cds_consumo_alimentar" => $codConsumo,
                "co_qst_questao" => $codPergunta, "co_qst_resposta" => $codResposta);
            $tbPergRespos->salvar($dataRespostas);
        } catch (Exception $ex) {
            die($ex->getMessage());
            return $ex->getMessage();
        }
    }


    public
    function trataValor($valor)
    {
        foreach ((array)$valor as $value) {
            $valorFinal = ($value != "" ? $value : NULL);
            return $valorFinal;
        }
    }

    public
    function salvarPublicoAction($pubAlvo = FALSE, $codFicha = FALSE)
    {
        $tbRlPub = new Application_Model_RlCdsFichaAtivColPubAlvo();
        $tbRlPub->excluir($codFicha);
        foreach ($pubAlvo as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_publico_alvo" => $value
            );
            try {
                $tbRlPub->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public
    function salvarPraticasAction($praticas = FALSE, $codFicha = FALSE)
    {
        $tbRlPrat = new Application_Model_RlCdsFichaAtivColPratica();
        $tbRlPrat->excluir($codFicha);
        foreach ($praticas as $value) {
            $dados = "";
            $dados = array(
                "co_cds_ficha_ativ_col" => $codFicha,
                "co_cds_ativ_col_pratica" => $value
            );
            try {
                $tbRlPrat->salvar($dados);
            } catch (Exception $ex) {
                die($ex->getMessage());
                return $ex->getMessage();
            }
        }
    }

    public function excluirAction()
    {
        $this->view->title = "Marcadores de Consumo Alimentar";

        $consAlime = $this->_request->getParam("id");

        $tbEsusConsumo = new Application_Model_EsusConsumoAlimentar();
        $tbcConsAlimen = new Application_Model_TbCdsConsumoAlimentar();
        $tbcConsAlimenResp = new Application_Model_TbCdsConsumoAlimentarResposta();
        $tbAte = new Application_Model_Atendimento();

        $dadosConsAlimen = $tbcConsAlimen->getDadosConsumoAlimentar($consAlime);

        try {
            $tbEsusConsumo->excluir($consAlime);
            $tbcConsAlimenResp->excluir($consAlime);
            $tbAte->excluir($dadosConsAlimen[0]['ate_codigo']);
            $tbcConsAlimen->excluir($consAlime);

            $this->view->dados = $tbcConsAlimen->getDados();
            $this->view->dialog = array("Confirmação", "Dados excluído com sucesso!", 300, 140);

            return $this->render("consumo-alimentar/index", NULL, TRUE);
        } catch (Exception $ex) {
            $ex->getMessage();
            $this->view->dados = $tbcConsAlimen->getDados();
            $this->view->erro = $ex->getMessage();

            return $this->render("index");
        }
    }

    public function inconsistenciasAction() {
        $this->view->title = "E-SUS Inconsistências Consumo Alimentar";
        $uuid = $this->_request->getPost("uuid");
        if ($uuid){
            $tbEsusCa = new Application_Model_EsusConsumoAlimentar();
            $this->view->dados = $tbEsusCa->getDadosPorUuid($uuid);
        }
    }

}


?>