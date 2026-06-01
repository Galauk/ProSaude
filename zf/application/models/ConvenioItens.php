<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_ConvenioItens extends Elotech_Db_Table_Abstract {

    protected $_name = 'convenio_itens';
    protected $_primary = 'coni_codigo';

    /* -----------------------------------------------------------------
     * MÉTODOS CONVÊNIOS AGENDAMENTO ESTABELECIMENTO DE SAÚDE
     * ---------------------------------------------------------------- */

    public function buscaEstabelecimentoDeSaude($coni_codigo) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("coni_codigo", "coni_ativo", "coni_data_inicio", "coni_data_termino"))
                ->join(array("esp" => "especialidade"), "coni.esp_codigo=esp.esp_codigo", array("esp_codigo"))
                ->join(array("conv" => "convenio"), "conv.conv_codigo = coni.conv_codigo", array(""))
                ->joinLeft(array("med" => "medico"), "med.med_codigo= conv.med_codigo", array("med_nome"))
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=conv.uni_codigo", "uni_desc")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr.usr_codigo", "usr_nome"))
                ->where("coni.coni_codigo=?", $coni_codigo);
        //die($where);
        return $this->fetchRow($where);
    }

    /* -----------------------------------------------------------------
     * MÉTODOS CONVÊNIOS
     * ---------------------------------------------------------------- */


    /* -----------------------------------------------------------------
     * MÉTODOS GERAIS UTIL PARA CONVÊNIO DE LABORATORIO OU AGENDAMENTO
     * ---------------------------------------------------------------- */

    // Busca pelos procedimento vinculados ao convênio ou pelo convênio de agendamento(Estabelecimento)
    public function buscarGenerico($conv_codigo = FALSE, $coni_codigo = FALSE, $ativo = FALSE) {
        //  Busca procedimento
        // die('caiu aqui generico');
        $sql1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("coni_codigo", "coni_valor", "coni_cota_mes", "coni_cota_dia", "coni_ativo", "conv_codigo"))
                ->join(array("proc" => "procedimento"), "coni.proc_codigo=proc.proc_codigo", array("proc_nome as item_nome", "proc_codigo as item_codigo", "proc_nome", "proc_codigo", "proc_apelido"))
                ->join(array("conve"=>"convenio"), "coni.conv_codigo=conve.conv_codigo", array("max_dia_manha", "max_dia_tarde"))
                ->joinLeft(array("espe" => "especialidade"), "espe.esp_codigo=coni.esp_codigo", "esp_nome");
        
        if ($conv_codigo) {
            $sql1->where("coni.conv_codigo=$conv_codigo");
        }
        
        if ($coni_codigo) {
            $sql1->where("coni.coni_codigo IN (?)", (array) $coni_codigo);
        }
        
        // Busca usuários ligado ao convênio(Estabelecimento de Saúde)
        $sql2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("coni_codigo", "coni_valor", "coni_cota_mes", "coni_cota_dia", "coni_ativo", "conv_codigo"))
                ->join(array("usr" => "usuarios"), "coni.usr_codigo=usr.usr_codigo", array("usr_nome as item_nome", "usr_codigo as item_codigo", "usr_nome", "usr_codigo", "usr_nome as apelido_gambi"))
                ->join(array("conv"=>"convenio"), "coni.conv_codigo=conv.conv_codigo", array("max_dia_manha", "max_dia_tarde"))
                ->join(array("esp" => "especialidade"), "esp.esp_codigo=coni.esp_codigo", "esp_nome");
        
        if ($conv_codigo) {
            $sql2->where("coni.conv_codigo=$conv_codigo");
        }

        if ($coni_codigo) {
            $sql2->where("coni.coni_codigo IN (?)", (array) $coni_codigo);
        }

        if ($ativo) {
            $sql1->where("(coni.coni_ativo='S' or coni.coni_ativo='' or coni.coni_ativo is null)");
            $sql2->where("(coni.coni_ativo='S' or coni.coni_ativo='' or coni.coni_ativo is null)");
        }

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($sql1, $sql2))
                ->order(array("item_nome"));


        // die($where);
        return $this->fetchAll($where);
    }

    public function buscarVagasExame($coni, $data){
        
        $queryM = "SELECT count(*) from agenda where age_codigo in (select age_codigo from agenda_itens where coni_codigo = $coni AND agei_data = '$data' order by agei_codigo desc) AND turno = 'm'";
        $queryT = "SELECT count(*) from agenda where age_codigo in (select age_codigo from agenda_itens where coni_codigo = $coni AND agei_data = '$data' order by agei_codigo desc) AND turno = 't'";
        
        $whereM = $this->getDefaultAdapter()->query($queryM)->fetch();
        $whereT = $this->getDefaultAdapter()->query($queryT)->fetch();

        $select = "select max_dia_manha, max_dia_tarde, max_dia_manha_horario, max_dia_tarde_horario from convenio where conv_codigo = (select conv_codigo from convenio_itens where coni_codigo = $coni)";

        $sql = $this->getDefaultAdapter()->query($select)->fetch();


        // echo "<pre>";
        // echo "dados";
        // print_r($query);
        // die();

        $where->manha = $whereM['count'];
        $where->tarde = $whereT['count'];
        $where->total = $sql;
        return $where;
    }

    /* -----------------------------------------------------------------
     * OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
     * ---------------------------------------------------------------- */

    /**
     * Insert ou update em um item
     * @param array $data dados do formulário
     * @return int chave primária do registro inserido ou atualizado 
     */
    public function salvar(array $data) {
        //echo "<pre>" . print_r($data, 1);            die();
        $this->addRealName(array(
            "coni_valor" => "valor",
            "usr_codigo" => "Profissional",
            "proc_codigo" => "Procedimento",
            "gruex_codigo" => "Grupo de Exame"
        ));

        /**/

        if ($data["tipo_form"] == "P") {
            $this->notEmpty(array("coni_valor", "proc_codigo"), $data);
            $this->peloMenosUm(array("proc_codigo", "gruex_codigo"), $data);
        } else {
            $this->notEmpty(array("usr_codigo"), $data);
        }
        unset($data['tipo_form']);
        $this->notEmpty(array("conv_codigo"), $data);
        $this->valoresPadrao($data);
        $this->emptyToUnset($data, FALSE);
        //echo "<pre>".print_r($data,1);die();
        try {
            $this->getAdapter()->beginTransaction();

            $temp = $data;
            unset($data['coni_cota_mes_original'], $data['coni_cota_dia_original']);

            $this->atualizarExcecoes($temp);
            $coni_codigo = parent::salvar($data);

            $this->getAdapter()->commit();
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            throw new Zend_Validate_Exception($e->getMessage());
        }

        return $coni_codigo;
    }

    /**
     * Verifica se é necessário alterar as cotas de cada mês/dia
     * @param arary $data dados do formulário
     */
    private function atualizarExcecoes($data) {
        if (empty($data['coni_codigo'])) // insert não precisa disso
            return;

        if ($data['coni_cota_mes'] != $data['coni_cota_mes_original']) {
            $tbGram = new Application_Model_GradeMes();
            $tbGram->atualizarCota($data['coni_codigo'], $data['coni_cota_mes']);
        }

        if ($data['coni_cota_dia'] != $data['coni_cota_dia_original']) {
            $tbGrad = new Application_Model_GradeDia();
            $tbGrad->atualizarCota($data['coni_codigo'], $data['coni_cota_dia']);
        }
    }

    /**
     * Salva os valores padrão de um item
     * @param array $data 
     */
    private function valoresPadrao(&$data) {
        if ($data['coni_cota_mes'] === "") {
            $data['coni_cota_mes'] = -1;
        }

        if ($data['coni_cota_dia'] === "") {
            $data['coni_cota_dia'] = -1;
        }

        $data['coni_cota_mes'] = (int) $data['coni_cota_mes'];
        $data['coni_cota_dia'] = (int) $data['coni_cota_dia'];

        if ($data['coni_cota_mes'] < -1)
            throw new Zend_Validate_Exception("A quatidade de vagas do mês não pode ser menor que zero.");

        if ($data['coni_cota_dia'] < -1)
            throw new Zend_Validate_Exception("A quatidade de vagas do dia não pode ser menor que zero.");
    }

    /**
     * Busca os procedimentos pelo código dos convênio
     * @param int $conv_codigo
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function buscarPeloConvenio($conv_codigo = FALSE, $ativo = FALSE) {
        return $this->buscarGenerico($conv_codigo, NULL, $ativo);
    }

    /**
     * Busca os procedimentos pelo código do convenio, ou pelos códigos dos itens
     * @param int $conv_codigo
     * @param array $coni_codigo
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function buscaSelectProcedimento($conv_codigo = FALSE, $term = FALSE) {

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("proc" => "procedimento"), array("proc_nome", "proc_codigo"))
                ->join(array("coni" => "convenio_itens"), "coni.proc_codigo=proc.proc_codigo", array("coni_codigo", "coni_valor", "coni_cota_mes", "coni_cota_dia"))
                ->order("proc_nome")
                ->where("proc.proc_nome ilike '%$term%'")
                ->where("coni.conv_codigo=?", $conv_codigo)
                ->where("(coni_ativo='' or coni_ativo is null or coni_ativo='S')");
        $out = array();
        $all = $this->fetchAll($where);
        foreach ($all as $item) {
            $data = $item->toArray();
            $out [] = array(
                "id" => $item->coni_codigo,
                "label" => $item->proc_nome,
                "data" => $data
            );
        }

        if (!count($out)) {
            $out [] = array(
                "id" => 0,
                "label" => "Nenhum item encontrado",
                "data" => array("coni_codigo" => "0", "proc_nome" => "")
            );
        }
        return $out;
    }

    /**
     * Busca os procedimentos pelos códigos dos itens
     * @param array $coni_codigo
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function buscarPeloConvenioItens($coni_codigo = FALSE) {
        return $this->buscarGenerico(FALSE, $coni_codigo);
    }

    /**
     * Exclui um item do convênio
     * @param int $coni_codigo
     */
    public function excluir($coni_codigo) {
        $item = $this->fetchRow("coni_codigo=$coni_codigo");
        if ($item) {
            $item->delete();
        }
        return true;
    }

    /**
     * @deprecated Quem chama essa função deverá renomeá-la. Retirar na versão 3.28.x
     * @param int $coni_codigo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function editar($coni_codigo) {
        return $this->fetchRow("coni_codigo=$coni_codigo");
    }

    /**
     * Retorna os dados de um item (procedimento, local, cota..)
     * @param int $coni_codigo
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function busca($coni_codigo) {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("coni_codigo", "coni_valor", "coni_cota_mes", "coni_cota_dia", "coni_ativo", "coni_intervalo", "coni_encaixe", "coni_data_inicio", "coni_data_termino", "gruex_codigo"))
                ->joinLeft(array("proc" => "procedimento"), "coni.proc_codigo=proc.proc_codigo", array("proc_nome", "proc_codigo"))
                ->join(array("conv" => "convenio"), "conv.conv_codigo = coni.conv_codigo", array("uni_codigo", "med_codigo", "conv_codigo"))
                ->joinLeft(array("med" => "medico"), "med.med_codigo= conv.med_codigo", array("med_nome"))
                ->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=conv.uni_codigo", "uni_desc")
                ->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr.usr_codigo", "usr_nome"))
                ->joinLeft(array("gruex" => "grupoexame"), "gruex.gruex_codigo=coni.gruex_codigo")
                ->where("coni.coni_codigo=?", $coni_codigo)
                ->order("proc_nome");

        //die($where->__toString());
        return $this->fetchRow($where);
    }

    /**
     * Informa o nome de cada exame associado ao item do convenio
     * @param array $coni_codigos 
     * @return array nome dos procedimentos
     */
    public function getNomeProcedimentos($coni_codigos) {
        $conis = $this->buscarPeloConvenioItens($coni_codigos);
        $out = array();
        foreach ($conis as $coni) {
            $out[$coni->coni_codigo] = $coni->proc_nome;
        }
        return $out;
    }

    public function getValorProcedimentos($coni_codigos) {
        $conis = $this->buscarPeloConvenioItens($coni_codigos);
        $out = array();
        foreach ($conis as $coni) {
            $out[$coni->coni_codigo] = $coni->coni_valor;
        }
        return $out;
    }

    public function getItemPorUsuarios($usr_codigo = FALSE, $conv_codigo) {
        $where = $this->select()
                ->from(array("coni" => "convenio_itens"))
                ->where("usr_codigo=?", $usr_codigo)
                ->where("conv_codigo=?", $conv_codigo);
        return $this->fetchRow($where);
    }

    // Função que pega o número de vagas menos o número de agendamento e retorna o número de vagas disponivel
    public function getVagas($coni_codigo = FALSE, $data = FALSE, $atendeQueDia = FALSE) {
        $where = $this->select()
                ->from(array("coni" => "convenio_itens"), array("((select condi_age_cota_dia + condi_age_encaixe AS coni_cota_dia
                                                                            from convenio_dias_semana_agendamento 
                                                                           where coni_codigo = $coni_codigo AND
                                                                                 condi_age_dia = $atendeQueDia)- 
                                                                         (select count(age_codigo) 
                                                                            from agendamento 
                                                                           where coni_codigo = $coni_codigo
                                                                             and age_data = '$data')) as cota"));
																			 
		//die($where);
        return $this->fetchRow($where);
    }

    /* public function getVagas($coni_codigo=FALSE,$data=FALSE){
      $where = $this->select()
      ->from(array("coni"=>"convenio_itens"),array("((select coni_cota_dia
      from convenio_itens
      where coni_codigo = $coni_codigo) -
      (select count(age_codigo)
      from agendamento
      where coni_codigo = $coni_codigo
      and age_data = '$data')) as cota"));
      return $this->fetchRow($where);
      } */

    public function getNomeProfissional($coni_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("coni_codigo"))
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr_codigo", "usr_nome"))
                ->where("coni_codigo=?", $coni_codigo);
        return $this->fetchRow($where);
    }

    public function getNomeProfissionaisPorUnidade($uni_codigo = FALSE) {
        $where = $this->select()
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("conv" => "convenio"), array(""))
                ->join(array("coni" => "convenio_itens"), "conv.conv_codigo=coni.conv_codigo", array("coni_codigo"))
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr_codigo", "usr_nome"))
                ->where("conv.uni_codigo=?", $uni_codigo);
        return $this->fetchAll($where);
    }

    public function getNomeProfissionaisPorUnidadeConveniado($uni_codigo = FALSE) {
        $where = $this->select()
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("conv" => "convenio"), array(""))
                ->join(array("coni" => "convenio_itens"), "conv.conv_codigo=coni.conv_codigo", "")
                ->join(array("convh" => "convenio_horarios"), "coni.coni_codigo=convh.coni_codigo", "")
                //->join(array("coni"=>"convenio_itens"),"conv.conv_codigo=coni.conv_codigo",array("coni_codigo"))
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr_codigo", "usr_nome"))
                //->join(array("esp"=>"especialidade"),"esp.esp_codigo=coni.esp_codigo",array("esp_codigo"))
                ->where("conv.uni_codigo=?", $uni_codigo)
                ->order("usr.usr_nome ASC");
        return $this->fetchAll($where);
    }

    public function getIntervalos($coni_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), "coni_intervalo")
                ->where("coni_codigo=?", $coni_codigo);

        return $this->fetchRow($where);
    }

    public function getEspecialidadeMedicoPorConvenio($uni_codigo = FALSE, $usr_codigo = FALSE) {
        $where = $this->select()
                ->distinct()
                ->setIntegrityCheck(FALSE)
                ->from(array("conv" => "convenio"), array(""))
                ->join(array("coni" => "convenio_itens"), "conv.conv_codigo=coni.conv_codigo", array("coni_codigo"))
                ->join(array("convd" => "convenio_dias_semana_agendamento"), "coni.coni_codigo=convd.coni_codigo", "")
                ->join(array("convh" => "convenio_horarios"), "coni.coni_codigo=convh.coni_codigo", "")
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=coni.usr_codigo", array("usr_codigo", "usr_nome"))
                ->join(array("esp" => "especialidade"), "esp.esp_codigo=coni.esp_codigo", array("esp_nome", "esp_codigo"))
                ->where("conv.uni_codigo=?", $uni_codigo)
                ->where("usr.usr_codigo=?", $usr_codigo);
        return $this->fetchAll($where);
    }

    public function getEspecialidadeConvenioItens($coni_codigo) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array(""))
                ->join(array("esp" => "especialidade"), "esp.esp_codigo=coni.esp_codigo", array("esp_codigo"))
                ->where("coni.coni_codigo=?", $coni_codigo);

        // die($where);
        return $this->fetchRow($where);
    }

    public function confereConvItens($conv_codigo = FALSE, $esp_codigo = FALSE, $usr_codigo = FALSE) {
        if ($conv_codigo != FALSE && $esp_codigo != FALSE && $usr_codigo != FALSE) {
            $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("coni" => "convenio_itens"), array("coni_codigo AS qtd_conv"))
                    ->where("conv_codigo =?", $conv_codigo)
                    ->where("esp_codigo =?", $esp_codigo)
                    ->where("usr_codigo =?", $usr_codigo);
            return $this->fetchRow($sql);
        }
    }

    public function getConvenioItensPorProcedimento($conv_codigo = FALSE, $proc_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"))
                ->where("proc_codigo=$proc_codigo")
                ->where("conv_codigo=$conv_codigo");

        return $this->fetchRow($where);
    }

    public function getDados($coni_codigo = FALSE) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"))
                ->where("coni_codigo=$coni_codigo");

        return $this->fetchRow($where);
    }

    public function getValorAgendamentoAtual($coni_codigos) {
        $where = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("coni" => "convenio_itens"), array("SUM(coni_valor) AS valor"))
                ->where("coni_codigo IN ($coni_codigos)");
        //die($where);
        return $this->fetchRow($where);
    }

    public function getItensDesatualizados() {
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ci" => "convenio_itens"))
                ->join(array("usr" => "usuarios"), "usr.usr_codigo=ci.usr_codigo")
                ->join(array("esp" => "especialidade"), "esp.esp_codigo=ci.esp_codigo", "esp_nome")
                ->join(array("conv" => "convenio"), "conv.conv_codigo=ci.conv_codigo", "")
                ->join(array("uni" => "unidade"), "uni.uni_codigo=conv.uni_codigo", array("uni_codigo", "uni_desc"))
                ->where("ci.esp_codigo not in (select esp_codigo 
                                                        from usuarios u
                                                        join medico_especialidade m
                                                          on m.med_codigo=u.usr_codigo
                                                      where mes_ativo = 'A' and m.med_codigo = ci.usr_codigo)")
                ->order("usr.usr_codigo");
        return $this->fetchAll($where);
    }

}
