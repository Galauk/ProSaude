<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Atendimento extends Elotech_Db_Table_Abstract {

	protected $_name = 'atendimento';
	protected $_primary = 'ate_codigo';
	protected $_sequence = 'seq_ate_codigo';
	protected $_dependentTables = array();
	protected $_referenceMap = array(
		'Agendamento' => array(
			'columns' => 'age_codigo',
			'refTableClass' => 'Agendamento',
			'refColumns' => 'age_codigo'
		)
	);

	/**
	 * agendamento.age_atendimento
	 */
	const RECEPCIONADO = 'S';
	const ATENDIDO = 'A';
	const AGENDADO = 'N';
	const TRANSFERIDO = 'T';
	const FALTOSO = 'T';
	const EM_ATENDIMENTO = 'E';

    public function getAtendimentosOdontologicos(){

        $tbUsr = new Application_Model_Usuarios();
        $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;
        $sql = $this->select(FALSE)
                    ->distinct()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","ate_data_insert"))
                    ->join(array("age"=>"agendamento"),"ate.age_codigo=age.age_codigo","")
                    ->join(array("esp"=>"especialidade"),"age.esp_codigo=esp.esp_codigo",array("cod_cbo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("uni"=>"unidade"),"ate.uni_codigo=uni.uni_codigo",array("uni_desc"))
                    ->join(array("pcont"=>"odonto_procedimentos_controle"),"ate.ate_codigo=pcont.ate_codigo","")
                    ->join(array("procr"=>"odonto_procedimentos_realizados"),"pcont.odo_pcon_codigo=procr.odo_pcon_codigo","")
                    ->where("age.uni_codigo=$uni_codigo")
                    ->order("ate.ate_data DESC")
                    ->limit(15);
        return $this->fetchAll($sql);
    }

    public function getDadosAtendimentosOdontologico($ateCod){
        $sql = $this->select(FALSE)
                    ->distinct()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo", "co_local_atend","turno","usu_ate_dom_mod","ate_tipo_atendimento_paciente","ate_encaminhamento_conduta","ate_conduta_desfecho","ate_conduta_desfecho"))
                    ->join(array("age"=>"agendamento"),"ate.age_codigo=age.age_codigo",array("age_codigo"))
                    ->join(array("esp"=>"especialidade"),"age.esp_codigo=esp.esp_codigo",array("cod_cbo"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome", "usu_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome", "usr_codigo"))
                    ->join(array("uni"=>"unidade"),"ate.uni_codigo=uni.uni_codigo",array("uni_desc", "uniCodigo" => "uni_codigo"))
                    ->join(array("pcont"=>"odonto_procedimentos_controle"),"ate.ate_codigo=pcont.ate_codigo",array("odo_pcon_codigo"))
                    ->join(array("procr"=>"odonto_procedimentos_realizados"),"pcont.odo_pcon_codigo=procr.odo_pcon_codigo","")
                    ->where("ate.ate_codigo =?",$ateCod);
        return $this->fetchRow($sql);
    }

    public function getAtendimentosOdontologicosProfissionais($term=FALSE,$tipoBusca=FALSE){
        $sql = $this->select(FALSE)
                    ->distinct()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","ate_data_insert"))
                    ->join(array("age"=>"agendamento"),"ate.age_codigo=age.age_codigo","")
                    ->join(array("esp"=>"especialidade"),"age.esp_codigo=esp.esp_codigo",array("cod_cbo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("uni"=>"unidade"),"ate.uni_codigo=uni.uni_codigo",array("uni_desc"))
                    ->join(array("pcont"=>"odonto_procedimentos_controle"),"ate.ate_codigo=pcont.ate_codigo","")
                    ->join(array("procr"=>"odonto_procedimentos_realizados"),"pcont.odo_pcon_codigo=procr.odo_pcon_codigo","");

        switch ($tipoBusca) {
            case 1: $sql->where("usr.usr_nome ILIKE '%$term%'"); break;
            case 2: $sql->where("ate.ate_data = '$term'"); break;
            case 3: $sql->where("esp.cod_cbo = '$term'"); break;
            case 4: $sql->where("uni.uni_desc ILIKE '%$term%'"); break;
        }

        $sql->order("ate.ate_data DESC");
        return $this->fetchAll($sql);
    }

	public function getCodigoAtendimentoPorAgendamento($age_codigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_codigo"))
                    ->join(array("age"=>"agendamento"),"ate.age_codigo=age.age_codigo","")
                    ->where("ate.age_codigo =?",$age_codigo);
        return $this->fetchRow($sql);
    }

        /*---------------------------------------------------------------------*
	 * Salvar o item, insert ou update
	 * @param array $data Array, chave=>valor
	 * @return int Primary Key
        * --------------------------------------------------------------------*/

	public function salvar(array $data,$internacao=false,$io_codigo=false) {
        if($data["age_codigo"] != NULL){
            $this->valoresPadraoFicha($data);
        } else {
            $this->valoresPadrao($data);
        }

        $tbAge = new Application_Model_Agendamento();
        $tbUsr = new Application_Model_Usuarios();
        $tbInt = new Application_Model_AtendimentoInternacao();

        if($internacao == "S"){
            $data['usu_codigo'] = $tbInt->getInternacao($io_codigo)->current()->usu_codigo;
            $data['med_codigo'] = $tbUsr->getUsrAtual()->usr_codigo;
            $data['uni_codigo'] = $tbInt->getInternacao($io_codigo)->current()->uni_codigo;
        }

        // verificar se é "unico" ou "multiplo"
        if ($this->textareaUnico()) {
            // ate_reclamacao é obrigatório
            $this->addRealName(array("ate_reclamacao" => "evolução"));
            //$this->notEmpty(array("ate_reclamacao"), $data);
        } else {
            // ao menos um dos campos
            $this->peloMenosUm(array("ate_reclamacao", "ate_exame_fisico", "ate_diagnostico", "ate_tratamento", "ate_curativo"), $data);
        }

        foreach (array("ate_reclamacao", "ate_exame_fisico", "ate_diagnostico", "ate_tratamento", "ate_curativo") as $campo) {
            $data[$campo] = trim($data[$campo]);
        }

        // Exceção de atualização para os campos de cids que se forem vazio precisa ser atualizado
        $cd10_codigo = $data["cd10_codigo"];
        $cd10_codigos = $data["cd10_codigos"];
        $cd10_codigot = $data["cd10_codigot"];

        $this->emptyToUnset($data);
        // Continuando validação de exceção CIDS
        $data["cd10_codigo"] = $cd10_codigo;
        $data["cd10_codigos"] = $cd10_codigos;
        $data["cd10_codigot"] = $cd10_codigot;
        $tbUsr = new Application_Model_Usuarios();
        $med_codigo = Application_Model_Agendamento::usuEmAberto()->med_codigo;
        if($internacao != "S"){
            if($data[ate_encaminhamento] != "S"){
                if ($tbUsr->isMedico() || ($tbUsr->getUsrAtual()->usr_codigo == $med_codigo) || $med_codigo == 99998) {
                    $tbAge->alteraSituacao("I", FALSE, FALSE);
                }
            }
        }
        // Validações CID
        if($data[cd10_codigo] == ""){
            $data[cd10_codigo] = null;
        }
        if($data[cd10_codigos] == ""){
            $data[cd10_codigos] = null;
        }
        if($data[cd10_codigot] == ""){
            $data[cd10_codigot] = null;
        }

        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o atendimento: ".$exc->getMessage());
        }
    }

    public function salvarAtendimento($data){
        try {
            return parent::salvar($data);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar o atendimento: ".$exc->getMessage());
        }
    }

    public function listaAtendimentosSimplificados($busca=FALSE,$tipoBusca=FALSE, $usr_codigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","age_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"))
                    ->where("ate_tipo = 'A'")
                    ->where("usr.usr_codigo = '$usr_codigo'");
        // Usado somente para busca
        switch ($tipoBusca) {
            case "P":
                $sql->where("usu.usu_nome ilike '%".$busca."%'");
            break;
            case "D":
                if(!empty($busca))
                    $sql->where("ate.ate_data = '".$busca."'");
            break;
            case "U":
                $sql->where("usr.usr_nome ilike '%".$busca."%'");
            break;
            case "":
                $sql->where("ate_data=CURRENT_DATE");
            break;
        }
        // die($sql);
        $sql->order(array("ate_data DESC"));
        return $this->fetchAll($sql);
    }

    public function listaVisitaDomiciliar($busca=FALSE,$tipoBusca=FALSE, $usr_codigo=FALSE){
        // die("visitas");
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","age_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"))
                    ->where("ate_tipo = 'V'")
                    ->where("usr.usr_codigo = '$usr_codigo'");
        // Usado somente para busca
        switch ($tipoBusca) {
            case "P":
                $sql->where("usu.usu_nome ilike '%".$busca."%'");
            break;
            case "D":
                if(!empty($busca))
                    $sql->where("ate.ate_data = '".$busca."'");
            break;
            case "U":
                $sql->where("usr.usr_nome ilike '%".$busca."%'");
            break;
            case "":
                $sql->where("ate_data=CURRENT_DATE");
            break;
        }
        // die($sql);
        $sql->order(array("ate_data DESC"));
        return $this->fetchAll($sql);
    }

    public function listaProcedimento($busca=FALSE,$tipoBusca=FALSE, $usr_codigo=FALSE){
        // die("visitas");
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","age_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"))
                    ->where("ate_tipo = 'P'");
                    // ->where("usr.usr_codigo = '$usr_codigo'");
        // Usado somente para busca
        switch ($tipoBusca) {
            case "P":
                $sql->where("usu.usu_nome ilike '%".$busca."%'");
            break;
            case "D":
                if(!empty($busca))
                    $sql->where("ate.ate_data = '".$busca."'");
            break;
            case "U":
                $sql->where("usr.usr_nome ilike '%".$busca."%'");
            break;
            case "":
                $sql->where("ate_data=CURRENT_DATE");
            break;
        }
        // die($sql);
        $sql->order(array("ate_data DESC"));
        return $this->fetchAll($sql);
    }

    public function listaBeneficiosConcedidos($busca=FALSE,$tipoBusca=FALSE, $usr_codigo=FALSE){
        // die("visitas");
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data","ate_codigo","age_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"))
                    ->where("ate_tipo = 'B'")
                    ->where("usr.usr_codigo = '$usr_codigo'");
        // Usado somente para busca
        switch ($tipoBusca) {
            case "P":
                $sql->where("usu.usu_nome ilike '%".$busca."%'");
            break;
            case "D":
                if(!empty($busca))
                    $sql->where("ate.ate_data = '".$busca."'");
            break;
            case "U":
                $sql->where("usr.usr_nome ilike '%".$busca."%'");
            break;
            case "":
                $sql->where("ate_data=CURRENT_DATE");
            break;
        }
        // die($sql);
        $sql->order(array("ate_data DESC"));
        return $this->fetchAll($sql);
    }

    public function relListaAtendimentosSimplificados($dataInicial=FALSE,$dataFinal=FALSE,$usuCodigo=FALSE,$usrCodigo=FALSE,$procCodigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("ate"=>"atendimento"),"")
                    ->join(array("proca"=>"procedimento_atendimento"),"ate.ate_codigo=proca.ate_codigo","")
                    ->join(array("proc"=>"procedimento"),"proca.proc_codigo=proc.proc_codigo",array("count(proc_codigo_sus)","proc_codigo_sus","proc_nome","proc_codigo"))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo","")
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo","")
                    ->group(array("proc_codigo_sus","proc_nome","proc.proc_codigo"));
        if ($dataInicial){
            $sql->where("ate_data >= '$dataInicial'");
        }

        if ($dataFinal){
            $sql->where("ate_data <= '$dataFinal'");
        }

        if ($usuCodigo){
            $sql->where("usu.usu_codigo = '$usuCodigo'");
        }

        if ($usrCodigo){
            $sql->where("usr.usr_codigo = '$usrCodigo'");
        }

        if ($procCodigo){
            $sql->where("proca.proc_codigo = '$procCodigo'");
        }
        
        $sql->order(array("proc_nome"));
        return $this->fetchAll($sql);
    }

    public function relListaAtendimentosSimplificadosDetalhes($dataInicial=FALSE,$dataFinal=FALSE,$usuCodigo=FALSE,$usrCodigo=FALSE,$procCodigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->distinct()
                    ->from(array("ate"=>"atendimento"),"ate_data")
                    ->join(array("proca"=>"procedimento_atendimento"),"ate.ate_codigo=proca.ate_codigo","")
                    ->join(array("uni"=>"unidade"),"uni.uni_codigo=ate.uni_codigo","uni.uni_desc")                    
                    ->join(array("proc"=>"procedimento"),"proca.proc_codigo=proc.proc_codigo","")
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome","qtde_prof"=>"(SELECT count(usr2.usr_codigo) FROM atendimento AS ate2
                        INNER JOIN procedimento_atendimento AS proca2
                            ON ate2.ate_codigo=proca2.ate_codigo
                        INNER JOIN procedimento AS proc2
                            ON proca2.proc_codigo=proc2.proc_codigo
                        INNER JOIN usuarios AS usr2
                            ON ate2.med_codigo=usr2.usr_codigo
                        WHERE
                            (ate_data >= '$dataInicial')
                            AND (ate_data <= '$dataFinal')
                            and usr2.usr_codigo = usr.usr_codigo
                            AND proca2.proc_codigo = $procCodigo)","usr_codigo"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"));
        if ($dataInicial)
            $sql->where("ate_data >= '$dataInicial'");
        if ($dataFinal)
            $sql->where("ate_data <= '$dataFinal'");
        if ($usuCodigo)
            $sql->where("usu.usu_codigo = '$usuCodigo'");
        if ($usrCodigo)
            $sql->where("usr.usr_codigo = '$usrCodigo'");
        if ($procCodigo)
            $sql->where("proca.proc_codigo = '$procCodigo'");
        $sql->order(array("usr.usr_codigo", "ate_data"));

//die($sql);
        return $this->fetchAll($sql);

    }

    public function buscaAtendimentosSimplificados($busca=FALSE,$tipoBusca=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),array("ate_data",""))
                    ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome"))
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome"))
                    ->where("ate_simplificado = 't'");
        switch ($tipoBusca) {
            case "P":
                $sql->where("usu.usu_nome ilike '%".$busca."%'");
            break;
            case "D":
                $sql->where("ate.ate_data = '".$busca."'");
            break;
            case "U":
                $sql->where("usr.usr_nome ilike '%".$busca."%'");
            break;
        }
        $sql->order(array("ate_data DESC"));
        return $this->fetchAll($sql);
    }

        /***
	 * Atualiza o status de atendimento como: Atendido, Em  atendimento e não atendido
	 */
	public function atualizaStatus(array $data){
		$this->emptyToUnset($data);
		return parent::salvar($data);

	}

        /*Esse método foi feito para a ficha pois o metodo valorespadrao buscava da session do prontuário*/
        private function valoresPadraoFicha(&$data){
            $tbAge = new Application_Model_Agendamento();
            $age = $tbAge->getAgendamento($data[age_codigo]);
            if (is_null($data['ate_data']))
			$data['ate_data'] = $age->age_data;

            if (is_null($data['ate_hora']))
			$data['ate_hora'] = date("H:i");

            if (is_null($data['usu_codigo']))
			$data['usu_codigo'] = $age->usu_codigo;

            if (is_null($data['med_codigo']) || empty($data['med_codigo']))
			$data['med_codigo'] = $age->med_codigo;

		if (is_null($data['uni_codigo']) || empty($data['uni_codigo']))
			$data['uni_codigo'] = $age->uni_codigo;
        }

	private function valoresPadrao(&$data) {
		$tbAge = new Application_Model_Agendamento();
		$age = $tbAge->usuEmAberto();

		if (is_null($data['ate_data']))
			$data['ate_data'] = "NOW()";

		if (is_null($data['ate_hora']))
			$data['ate_hora'] = date("H:i");

		// Pra que usu_codigo? Não era só pegar em agendamento.usu_codigo?
		if (is_null($data['usu_codigo']))
			$data['usu_codigo'] = $age->usu_codigo;

		// Pq atendimento.age_codigo pode ser null?
		if (is_null($data['age_codigo']) || empty($data['age_codigo']))
			$data['age_codigo'] = $age->age_codigo;

		if (is_null($data['med_codigo']) || empty($data['med_codigo']))
			$data['med_codigo'] = $age->med_codigo;

		if (is_null($data['uni_codigo']) || empty($data['uni_codigo']))
			$data['uni_codigo'] = $age->uni_codigo;
	}

	/**
	 * Busca os dados mais importantes do atendimento
	 * @param int $ate_codigo (opcional dentro do prontuário eletronico)
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function buscar($ate_codigo=FALSE) {
        // die("teste");
                $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
                ->from(array("ate" => "atendimento"), array("ate_codigo_formatado" =>"to_char(ate_data, 'DD-MM-YYYY')" ,"ate_codigo", "gd_codigo", "ate_data", "ate_hora", "ate_descatend", "ate_observacao", "ate_diagnostico","vacinacao_em_dia", "ate_acidentetrab","ate_rac_saude","ate_perimetro_cefalico", "ate_reclamacao", "ate_exame_fisico", "ate_tratamento", "ate_curativo", "cd10_codigo", "cd10_codigos", "cd10_codigot", "age_codigo", "ate_datafinal", "ate_horafinal", "ate_puericultura","ate_peso","ate_altura", "ate_pre_natal", "ate_cancer", "ate_dst", "ate_diabetes", "ate_hipertensao","turno","ate_encaminhamento_conduta","ate_conduta_desfecho","ate_tipo_atendimento_paciente","usu_ate_dom_mod", "ate_hanseniase", "ate_tuberculose","ate_outros","ate_encaminhamento","co_local_atend","ate_somente_procedimento","ate_tipo","ate_nasf_aval","ate_nasf_proc","ate_nasf_presc", "ate_inter_data_formatado" => "to_char(ate_inter_data, 'DD-MM-YYYY')", "ate_inter_motivo", "ate_hipotese_diagnostico", "beneficio_emergencia","laboratorio_de_destino"))
				->join(array("u" => "usuario"), "u.usu_codigo=ate.usu_codigo", array("usu_codigo", "usu_nome","usu_sexo"))
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", array("age_atendido"))
				->join(array("uni" => "unidade"), "uni.uni_codigo=ate.uni_codigo", array("uni_desc","uni_endereco","uni_numero","uni_codigo"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr_codigo", "usr_nome", "usr_num_conselho","usr_tipo_medico"))
				->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo", "esp_nome")
				->joinLeft(array("c" => "cid10"), "c.cd10_codigo=ate.cd10_codigo", array("cd10_codigo", "cd10_codigo_cid", "cd10_descricao"))
                                ->order("ate.ate_codigo desc");
                                // die($where);
		if (!$ate_codigo) {
            $age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
            $where->where("ate.age_codigo=$age_codigo");
            $item = $this->fetchRow($where);
		} else{
            $where->where("ate.ate_codigo=$ate_codigo");
            $item = $this->fetchRow($where);
        }
        // echo "<pre>";print_r($where);die();
		// Se ainda não houver atendimento, item será NULL
		// É necessário carregar o usu_nome
		if (!$item) {
			$item->usu_nome = Application_Model_Agendamento::usuEmAberto()->age_paciente;
                        $item->usu_codigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;
		}

		// Se o médico ainda não fez o atendimento,
		// desconsiderar a hora do atendimento do enfermeiro.
		// update: como o atendimento pode ser reaberto (a partir de A), ele não deve alterar a hora
		if ($item->age_atendido != self::EM_ATENDIMENTO && $item->age_atendido != self::ATENDIDO) {
			$item->ate_hora = date("H:i");
		}
		//echo "<pre>".print_r($item,1);
		return $item;
	}

        //Verifica quais CIDS estão sendo utilizados e retorna o disponivel
        public function verificaCidsLivres($ateCodigo){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"),array("ate_codigo","cd10_codigo","cd10_codigos","cd10_codigot"))
                        ->where("ate_codigo =? ",$ateCodigo);
            //die($sql);
            return $this->fetchRow($sql);
        }

        public function atualizaCids($dadosAtuCids){
            /*echo "<pre>";
            print_r($dadosAtuCids);
            echo "</pre>";
            die();*/
            parent::salvar($dadosAtuCids);
        }

        public function getQtdRegistrosAtendCid10($codAte,$codCid){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"),array("count(ate_codigo) AS qtd_reg_cid10"))
                        ->where("ate_codigo =? ",$codAte)
                        ->where("cd10_codigo =? ",$codCid);
            return $this->fetchRow($sql);
        }

        public function getQtdRegistrosAtendCid10s($codAte,$codCid){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"),array("count(ate_codigo) AS qtd_reg_cid10s"))
                        ->where("ate_codigo =? ",$codAte)
                        ->where("cd10_codigos =? ",$codCid);
            //die($sql);
            return $this->fetchRow($sql);
        }

        public function getQtdRegistrosAtendCid10t($codAte,$codCid){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"),array("count(ate_codigo) AS qtd_reg_cid10t"))
                        ->where("ate_codigo =? ",$codAte)
                        ->where("cd10_codigot =? ",$codCid);
            return $this->fetchRow($sql);
        }

	/**
	 * Busca os dados mais importantes do atendimento
	 * @param int $ate_codigo (opcional dentro do prontuário eletronico)
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function buscarInternacao($ate_codigo=FALSE) {
		if ($ate_codigo) {
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("ate" => "atendimento"), array("ate_codigo", "gd_codigo", "ate_data", "ate_hora", "ate_descatend", "ate_observacao", "ate_diagnostico", "ate_acidentetrab", "ate_reclamacao", "ate_exame_fisico", "ate_tratamento", "ate_curativo", "cd10_codigo", "age_codigo", "ate_datafinal", "ate_horafinal", "ate_puericultura", "ate_pre_natal", "ate_cancer", "ate_dst", "ate_diabetes", "ate_hipertensao", "ate_hanseniase", "ate_tuberculose", "vacinacao_em_dia"))
					->joinLeft(array("u" => "usuario"), "u.usu_codigo=ate.usu_codigo", array("usu_codigo", "usu_nome"))
					->joinLeft(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "age_atendido")
					->joinLeft(array("uni" => "unidade"), "uni.uni_codigo=ate.uni_codigo", "uni_desc")
					->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr_codigo", "usr_nome", "usr_num_conselho"))
					->joinLeft(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo", "esp_nome")
					->joinLeft(array("c" => "cid10"), "c.cd10_codigo=ate.cd10_codigo", array("cd10_codigo", "cd10_codigo_cid", "cd10_descricao"));


			$where->where("ate.ate_codigo=?", $ate_codigo);
			//die($where);
			$item = $this->fetchRow($where);
			return $item;
		}else{
			return false;
		}


	//echo "<pre>".print_r($item,1);exit;

	}

	/**
	 * Retorna um atendimento,
	 * Ou seja, o atendimento gerado pelo enfermeiro, durante a pré-consulta não é válido.
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function temAtendimentoMedico($age_codigo=FALSE) {
            if ($age_codigo) {
                //die("akokoa");
                    $tbAge = new Application_Model_Agendamento();
                    $age = $tbAge->getAgendaPerm($age_codigo);
            } else {
                    $age = Application_Model_Agendamento::usuEmAberto();
            }
            if ($age->age_atendido == self::EM_ATENDIMENTO || $age->age_atendido == self::ATENDIDO || $age->age_atendido == "I") {
                    return $this->temAtendimento($age->age_codigo);

            }
           // echo "<pre>".print_r($age,1);exit;
            return FALSE;
	}

	/**
	 * Retorna um atendimento,
	 * Ou seja, o atendimento gerado pelo enfermeiro, durante a pré-consulta não é válido.
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function temAtendimentoMedicoNaInternacao($ate_codigo=FALSE) {

		if ($ate_codigo) {
			$tbAte = new Application_Model_Atendimento();
			$ate = $tbAte->fetchRow("ate_codigo=$ate_codigo");
                        /*echo "<pre>" . print_r($ate, 1);
                        die();*/
			return $ate;
		} else {
			return FALSE;
		}


	}

	/**
	 * Retorno um atendimento, somente se já exisir.
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function temAtendimento($age_codigo=FALSE,$gambi=FALSE) {
               $tbAge = new Application_Model_Agendamento();
               //echo "Age:".$tbAge->usuEmAberto()->age_codigo;
               //die();
               if (!$age_codigo) {

                    $age = Application_Model_Agendamento::usuEmAberto();
                    if (!$age)
                            return FALSE;

                    $age_codigo = $age->age_codigo;
                }

                $sql = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("ate"=>"atendimento"))
                            ->joinLeft(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo",array("age_atendido","age_codigo"))
                            ->where("age.age_codigo=$age_codigo");
                 // $sql->where("age.age_atendido = 'I'");
                            if($gambi){
                                $sql->where("age.age_atendido = 'I'");
                            }
                //die($sql);
                return $this->fetchRow($sql);
	}

        public function temAtendimentoAgendamento($age_codigo){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"))
                        ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_atendido")
                        ->where("ate.age_codigo=$age_codigo")
                        ->where("age.age_atendido = 'A'");
            return $this->fetchRow($sql);
        }

	public function estaEmAtendimento($age_codigo=FALSE) {
		if($age_codigo){
			$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agendamento"), array("age_atendido","med_codigo"))
				->where("age.age_codigo=?", $age_codigo);

                        return $this->fetchRow($where);
		}else{
			return false;
		}
	}

	/**
	 * Sempre retorna um atendimento. Se não existir o método irá criar.
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getAtendimento($age_codigo=FALSE) {
		if (!$age_codigo) {
			$age = Application_Model_Agendamento::usuEmAberto();
			$age_codigo = $age->age_codigo;
		}

		$previus = $this->temAtendimento($age_codigo);
		if ($previus)
                    return $previus;


		elseif (!$age){
			$age = Application_Model_Agendamento::usuEmAberto();
                }
		// Não há atendimento. Gerar um.
		$data = array(
			"ate_data" => date("Y-m-d"),
			"ate_hora" => date("H:i"),
			"usu_codigo" => $age->usu_codigo,
			"med_codigo" => $age->med_codigo, // em agendamento, a FK do médico é: med_codigo
			"age_codigo" => $age_codigo,
			"uni_codigo" => $age->uni_codigo
		);
		$this->salvar($data);
		return $this->temAtendimento($age_codigo);
	}

	/**
	 * Traz a lista de todos atendimentos
	 * @param string $term Opcional, usado para filtrar os atendimentos que contenham esse termo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getHistorico($term=FALSE) {
		$usu_codigo = Application_Model_Agendamento::usuEmAberto()->usu_codigo;

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("a" => "atendimento"), array("ate_codigo", "ate_data", "ate_hora"))
				->join(array("u" => "unidade"), "u.uni_codigo=a.uni_codigo", "uni_desc")
				->join(array("usr" => "usuarios"), "usr.usr_codigo=a.med_codigo", "usr_nome")
				->join(array("age" => "agendamento"), "age.age_codigo=a.age_codigo", "age_codigo")
				->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo", "esp_nome")
				->where("a.usu_codigo=?", $usu_codigo)
				->order(array("ate_data DESC", "ate_hora DESC"));

//die($where);
        if($term)
            $where->where("COALESCE(ate_reclamacao,'') || COALESCE(ate_exame_fisico,'') || COALESCE(ate_diagnostico,'') || COALESCE(ate_tratamento,'') || COALESCE(ate_curativo,'') ILIKE ('%$term%')");
		Zend_Registry::get("logger")->log($where->__toString(), Zend_Log::INFO);

		return $this->fetchAll($where);
	}

	/**
	 * Retorna todos os dados de um atendimento específico
	 * @param int $ate_codigo
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getDetalhes($ate_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ate" => "atendimento"))
				->joinLeft(array("c" => "cid10"), "c.cd10_codigo=ate.cd10_codigo", array("cd10_codigo_cid", "cd10_descricao"))
                                ->joinLeft(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_codigo")
                                ->joinLeft(array("uni"=>"unidade"),"uni.uni_codigo=age.uni_codigo","uni_desc")
                                ->joinLeft(array("esp"=>"especialidade"),"esp.esp_codigo=age.esp_codigo","esp_nome")
                                ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=age.med_codigo","usr_nome")
				->where("ate_codigo=?", $ate_codigo);
		return $this->fetchRow($where);
	}

        /**
	 * Retorna os dados de um atendimento
	 * Faz referencias as tabelas relacionadas (usr, usu)
	 * Usado no novo agendamento (ajax) para preecher os dados pelo código do atendimento
	 * @param int $ate_codigo código do atendimento
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function getDadosCabecalho($ate_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ate" => "atendimento"), array("ate_codigo"))
				->join(array("usu" => "usuario"), "usu.usu_codigo=ate.usu_codigo", array("usu_codigo", "usu_nome"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr_codigo", "usr_nome"))
				->where("ate.ate_codigo=?", $ate_codigo);

		return $this->fetchRow($where);
	}

	/**
	 * Traz todos os atendimentos, e os dados de cada um
	 * @param int $usu_codigo
	 * @param string $data_inicial
	 * @param string $data_final
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getHistoricoDetalhado($usu_codigo, $data_inicial=FALSE, $data_final=FALSE,$limit=FALSE) {
		$subSelectPC = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pc2" => "pre_consulta"), "pc_codigo")
				->where("pc2.age_codigo=a.age_codigo")
				->order("pc_codigo DESC")
				->limit(1);


		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("a" => "atendimento"), array("ate_codigo", "ate_data", "ate_hora", "ate_reclamacao", "ate_exame_fisico", "ate_diagnostico", "ate_tratamento", "ate_curativo"))
				->join(array("u" => "unidade"), "u.uni_codigo=a.uni_codigo", "uni_desc")
				->join(array("age" => "agendamento"), "age.age_codigo=a.age_codigo", "")
				->joinLeft(array("usr" => "usuarios"), "usr.usr_codigo=a.med_codigo", "usr_nome")
				->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo", "esp_nome")
				->joinLeft(array("pc" => "pre_consulta"), "pc.age_codigo=a.age_codigo", array("pc_peso", "pc_altura", "pc_pressao_sistolica", "pc_pressao_diastolica"))
				->joinLeft(array("cd10" => "cid10"), "cd10.cd10_codigo=a.cd10_codigo", array("cd10_codigo_cid", "cd10_descricao"))
				->where("a.usu_codigo=?", $usu_codigo)
				//->where("pc.pc_codigo IN ?", $subSelectPC)
				->order(array("ate_data DESC", "ate_hora DESC"));

		if ($data_inicial)
			$where->where("ate_data >= ?", $data_inicial);

		if ($data_final)
			$where->where("ate_data <= ?", $data_final);

                if($limit)
                    $where->limit($limit);
		return $this->fetchAll($where);

	}

	/**
	 * Verificar quantos textarea's devem ser exibidos no atendimento
	 * @return bool TRUE caso deva ser exibido um único textarea
	 */
	public function textareaUnico() {
		$tbConf = new Application_Model_Configuracao();
		return $tbConf->getConfig("PRONTUARIO_ATENDIMENTO_TEXTAREAUNICO");
	}

	public function relProcedimentoPorUnidade(&$dados, $uni_codigo) {
		$dados->title = "Procedimento Por Unidade";
		$dados->params = serialize($_POST);
		$dados->config = array(
			"th" => array("uni_desc" => "Unidade", "proc_nome" => "Procedimento", "total" => "Quant."),
			"formato" => array("total" => "uni")
		);

		$sqlAte = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pat" => "procedimento_atendimento"), array("proc.proc_codigo"))
				->join(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
				->join(array("ate" => "atendimento"), "ate.ate_codigo=pat.ate_codigo", "")
				->join(array("uni" => "unidade"), "uni.uni_codigo=ate.uni_codigo", "uni_desc")
				->where("uni.uni_codigo=?", $uni_codigo);

		$sqlPC = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pat" => "procedimento_atendimento"), array("proc.proc_codigo"))
				->join(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
				->join(array("pc" => "pre_consulta"), "pc.pc_codigo=pat.pc_codigo", "")
				->join(array("age" => "agendamento"), "age.age_codigo=pc.age_codigo", "")
				->join(array("uni" => "unidade"), "uni.uni_codigo=age.uni_codigo", "uni_desc")
				->where("uni.uni_codigo=?", $uni_codigo);

		if ($data_inicial)
			$sqlPC->where("age.age_data >= ?", $data_inicial);

		if ($data_final)
			$sqlPC->where("age.age_data <= ?", $data_final);

		$sqlPE = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("pat" => "procedimento_atendimento"), array("proc.proc_codigo"))
				->join(array("proc" => "procedimento"), "proc.proc_codigo=pat.proc_codigo", "proc_nome")
				->join(array("pe" => "posto_enfermagem"), "pe.pe_codigo=pat.pe_codigo", "")
				->join(array("ate" => "atendimento"), "ate.ate_codigo=pe.ate_codigo", "")
				->join(array("uni" => "unidade"), "uni.uni_codigo=ate.uni_codigo", "uni_desc")
				->where("uni.uni_codigo=?", $uni_codigo);

		if ($data_inicial) {
			$sqlAte->where("ate.ate_data >= ?", $data_inicial);
			$sqlPE->where("ate.ate_data >= ?", $data_inicial);
		}

		if ($data_final) {
			$sqlAte->where("ate.ate_data <= ?", $data_final);
			$sqlPE->where("ate.ate_data <= ?", $data_final);
		}

		$union = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->union(array($sqlAte, $sqlPC, $sqlPC), Zend_Db_Select::SQL_UNION_ALL)
				->order(array("uni_desc", "proc_nome"));

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("x" => $union), array("total" => "count(x.proc_codigo)", "proc_nome", "uni_desc"))
				->group(array("x.proc_nome", "x.uni_desc"));

		return $where;
	}

	/* FUNÇÔES DO PMA2 */

	/**
	 * Diz quantos atendimentos foram realizados por faixa etaria
	 * Filtros:
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getAtendimentosPorFaixaEtaria($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$faixa = "CASE WHEN extract(year from age(usu.usu_datanasc)) < 1 THEN 'CONSULTA.MENOR_DE_1_ANO'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 1 AND 4 THEN 'CONSULTA.DE_1_A_4'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 5 AND 9 THEN 'CONSULTA.DE_5_A_9'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 10 AND 14 THEN 'CONSULTA.DE_10_A_14'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 15 AND 19 THEN 'CONSULTA.DE_15_A_19'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 20 AND 39 THEN 'CONSULTA.DE_20_A_39'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 40 AND 49 THEN 'CONSULTA.DE_40_A_49'
            WHEN extract(year from age(usu.usu_datanasc)) BETWEEN 50 AND 59 THEN 'CONSULTA.DE_50_A_59'
            ELSE 'CONSULTA.60_OU_MAIS'
       END";

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agendamento"), array("total" => "count(age.age_codigo)", "faixa" => $faixa))
				->group(array("faixa"))
				->where("age.age_atendido=?", self::ATENDIDO)
				->order(array("faixa"))
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
//die($where);
		$all = $this->fetchAll($where);
		$fora = $this->getAtendimentoParaResidentesEmOutrosMunicipios($data_inicial, $data_final, $uni_codigo);

		$retorno = array(
			"CONSULTA.MENOR_DE_1_ANO" => 0,
			'CONSULTA.DE_1_A_4' => 0,
			'CONSULTA.DE_5_A_9' => 0,
			'CONSULTA.DE_10_A_14' => 0,
			'CONSULTA.DE_15_A_19' => 0,
			'CONSULTA.DE_20_A_39' => 0,
			'CONSULTA.DE_40_A_49' => 0,
			'CONSULTA.DE_50_A_59' => 0,
			"CONSULTA.60_OU_MAIS" => 0
		);

		$sub = 0;
		foreach ($all as $faixa) {
			$retorno[$faixa->faixa] = $faixa->total;
			$sub += $faixa->total;
		}

		$retorno["CONSULTA.SUBTOTAL"] = $sub;
		$retorno["CONSULTA.FORA_DA_AREA"] = $fora;
		$retorno["CONSULTA.TOTAL"] = $sub + $fora;

		return $retorno;
	}

	/**
	 * Retorna quantos atendimentos foram feitos para pacientes de outros municípios
	 * @param date $data_inicial
	 * @param date $data_final
	 * @return int
	 */
	private function getAtendimentoParaResidentesEmOutrosMunicipios($data_inicial=FALSE, $data_final=FALSE, $uni_codigo=FALSE) {
		// Qual o código dessa cidade?
		$tbCid = new Application_Model_Cidade();
		$cid_codigo = $tbCid->getCidadeAtual()->cid_codigo;

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agendamento"), array("total" => "count(age.age_codigo)"))
				->join(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", "")
				->join(array("dom" => "domicilio"), "dom.dom_codigo=usu.dom_codigo", "")
				->join("rua", "rua.rua_codigo=dom.rua_codigo", "")
				->where("age.age_atendido=?", self::ATENDIDO)
				->where("rua.cid_codigo <> ?", $cid_codigo)
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, FALSE, $uni_codigo);

		return $this->fetchRow($where)->total;
	}

	/**
	 * Filtra uma consulta pela data, area e microarea
	 * @param Zend_Db_Table_Select $where
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @param int $mic_codigo
	 */
	private function aplicarFiltroDeDataEArea(&$where, $data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		if ($data_inicial)
			$where->where("age.age_data >= ?", $data_inicial);

		if ($data_final)
			$where->where("age.age_data <= ?", $data_final);

		if ($area_codigo) {
			$this->joinUsuPsfArea($where);
			//$where->where("area.area_codigo=?", $area_codigo);
		}

		if ($uni_codigo)
			$where->where("age.uni_codigo=?", $uni_codigo);
	}

	/**
	 * Retorna quantas visitas foram feitas por médicos
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * return int
	 */
	public function getTotalDeVisitasDociliaresMedico($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->selecionaProcedimentosDeVisitas($data_inicial, $data_final, $area_codigo, array('0101030010','0301010137'), FALSE, $uni_codigo);
		return $this->fetchRow($where)->total;
	}

	/**
	 * Retorna quantas visitas foram feitas por enfermeiros
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * return int
	 */
	public function getTotalDeVisitasDociliaresEnfermeiro($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->selecionaProcedimentosDeVisitas($data_inicial, $data_final, $area_codigo, array('0301010137'), TRUE, $uni_codigo);
		return $this->fetchRow($where)->total;
	}

	/**
	 * Retorna quantas visitas foram feitas por outros profissionais de nivel superior
	 * UPDATE: olha somentes procedimentos feitos pelo CBO de dentista
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * return int
	 */
	public function getTotalDeVisitasDociliaresOutroSuperior($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$where = $this->fromPatToAge()
				->columns(array("total" => "count (pat.pat_codigo)"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pat.usr_codigo", "")
				->join(array("me" => "medico_especialidade"), "me.med_codigo=usr.usr_codigo", "")
				->join(array("esp" => "especialidade"), "esp.esp_codigo=me.esp_codigo", "")
				->where("proc.proc_codigo_sus IN (?)", array('0101030010','0301010137'))
				->where("cod_cbo IN (?)",  array('223208','2232B1'));

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		return $this->fetchRow($where)->total;
	}

	/**
	 * Retorna quantas visitas foram feitas por profissionais de nivel médio
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * return int
	 */
	public function getTotalDeVisitasDociliaresNivelMedio($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->selecionaProcedimentosDeVisitas($data_inicial, $data_final, $area_codigo, array('0101030010','0301050058'), NULL, $uni_codigo);
		return $this->fetchRow($where)->total;
	}

	/**
	 * Reuso de select dos procedimentos de visitas domiciliares realizados por periodo
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @param array $procedimentos
	 * @param bool $in Inclui, ou exclui os CBO: '223505','223555','223560', '2235C1', '2235C2'
	 * @return Zend_Db_Table_Select
	 */
	private function selecionaProcedimentosDeVisitas($data_inicial, $data_final, $area_codigo, $procedimentos, $in=NULL, $uni_codigo=FALSE){
		$where = $this->fromPatToAge()
				->columns(array("total" => "count (pat.pat_codigo)"))
				->join(array("usr" => "usuarios"), "usr.usr_codigo=pat.usr_codigo", "")
				->join(array("me" => "medico_especialidade"), "me.med_codigo=usr.usr_codigo", "")
				->join(array("esp" => "especialidade"), "esp.esp_codigo=me.esp_codigo", "")
				->where("proc.proc_codigo_sus IN (?)", $procedimentos);

		$cbos = array('223505','223555','223560', '2235C1', '2235C2');

		if( $in === TRUE)
			$where->where("cod_cbo IN (?)", $cbos);
		elseif( $in === FALSE)
			$where->where("cod_cbo NOT IN (?)", $cbos);

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
		return $where;
	}

	/**
	 * Agrupa todos os métodos que buscam os totais de visitas docimiliares
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalDeVisitasDociliares($data_inicial, $data_final, $area_codigo, $uni_codigo=FALSE){
		$retorno = array(
			"VISITAS.MEDICO" => $this->getTotalDeVisitasDociliaresMedico($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"VISITAS.ENFERMEIRO" => $this->getTotalDeVisitasDociliaresEnfermeiro($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"VISITAS.SUPERIOR" => $this->getTotalDeVisitasDociliaresOutroSuperior($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"VISITAS.MEDIO" => $this->getTotalDeVisitasDociliaresNivelMedio($data_inicial, $data_final, $area_codigo, $uni_codigo),
			"VISITAS.ACS" => 0
		);
		$total = 0;
		foreach($retorno as $item)
			$total += $item;

		$retorno['VISITAS.TOTAL'] = $total;

		return $retorno;
	}

	/**
	 * Informa quantos atendimentos de pericultura, pré-natal, prevenção de cancer cérvico-uterino (...)
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalPorTipoDeAtendimento($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("ate" => "atendimento"), array(
					"TIPO.PUERICULTURA" => "sum(ate_puericultura::int)",
					"TIPO.PRE_NATAL" => "sum(ate_pre_natal::int)",
					"TIPO.CANCER" => "sum(ate_cancer::int)",
					"TIPO.DST" => "sum(ate_dst::int)",
					"TIPO.DIABETES" => "sum(ate_diabetes::int)",
					"TIPO.HIPERTENSAO" => "sum(ate_hipertensao::int)",
					"TIPO.HANSENIASE" => "sum(ate_hanseniase::int)",
					"TIPO.TUBERCULOSE" => "sum(ate_tuberculose::int)"
				))
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
		return $this->fetchRow($where)->toArray();
	}

	/**
	 * Quantos encaminhamento (para especialista, internação hospitalar ou urgencia) houve
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalDeEncaminhamentos($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("enc" => "encaminhamento"), array(
					"total" => "count(enc.enc_codigo)",
					"internacao" => "sum(enc_internacao::int)",
					"urgencia" => "sum(enc_urgencia::int)"))
				->join(array("ate" => "atendimento"), "ate.ate_codigo=enc.ate_codigo", "")
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		$resultado = $this->fetchRow($where);

		$retorno = array(
			'ENCAMINHAMENTO.ESPECIALIZADO' => $resultado->total,
			'ENCAMINHAMENTO.INTERNACAO' => $resultado->internacao?$resultado->internacao:0,
			'ENCAMINHAMENTO.URGENCIA' => $resultado->urgencia?$resultado->urgencia:0
		);

		return $retorno;
	}

	/**
	 * Retorna quantos procedimentos de curativos, internação domiciliar, inalações, injeções, suturas e atendimentos em grupos houveram
	 * @param dare $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalDeProcedimentosNoAtendimento($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$grupo = "CASE WHEN proc.proc_codigo_sus in('0307020029','0401010023','0401010015','0413010058','0413010031','0413010040') THEN 'PROCEDIMENTOS.CURATIVOS'
      WHEN proc.proc_codigo_sus in('0401010058','0401010066','0401020053','0403010209','0404020097','0405010176','0405030096','0405030100','0405050291','0405050305','0405050399','0406020132','0407020209','0407040242','0408050888','0411010069','0411010077','0416080030','0416080049','0406020140') THEN 'PROCEDIMENTOS.SUTURA'
      WHEN proc.proc_codigo_sus in('0101010036','0202120023','0301040036','0101010010','0101010028','0202080196','0301080160') THEN 'PROCEDIMENTOS.ATENDIMENTO_GRUPO'
      WHEN proc.proc_codigo_sus in('0301050074') THEN 'PROCEDIMENTOS.INTERNACAO_DOMICILIAR'
      WHEN proc.proc_codigo_sus in ('0301100012','0301100020') THEN 'PROCEDIMENTOS.INJECOES'
      WHEN proc.proc_codigo_sus in('0301100101') THEN 'PROCEDIMENTOS.INALACOES'
	  WHEN proc.proc_codigo_sus IN ('0301100152') THEN 'PROCEDIMENTOS.RETIRADA_DE_PONTOS'
	  WHEN proc.proc_codigo_sus IN ('0102010145') THEN 'PROCEDIMENTOS.INSPECAO_SANITARIA'
	  WHEN proc.proc_codigo_sus IN ('0301100187') THEN 'PROCEDIMENTOS.REIDRATACAO'
	  WHEN proc.proc_codigo_sus IN ('0101020031','0101020015','0101020040','0101020023') THEN 'PROCEDIMENTOS.COLETIVOS'
	  ELSE 'PROCEDIMENTOS.OUTROS'
      END";

		$where = $this->fromPatToAge()
				->columns(array("total" => "count(pat.pat_codigo)", "grupo" => $grupo))
				->group("grupo")
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);


		$retorno = array(
			"PROCEDIMENTOS.INSPECAO_SANITARIA" => 0, // inspeção sanitária
			"PROCEDIMENTOS.CURATIVOS" => 0, // curativo
			"PROCEDIMENTOS.SUTURA" => 0, // sutura
			"PROCEDIMENTOS.ATENDIMENTO_GRUPO" => 0, // atendimento de grupo
			"PROCEDIMENTOS.INTERNACAO_DOMICILIAR" => 0, // internação
			"PROCEDIMENTOS.INJECOES" => 0, // injeção
			"PROCEDIMENTOS.INALACOES" => 0, // inalação
			"PROCEDIMENTOS.RETIRADA_DE_PONTOS" => 0, // retirada de pontos
			"PROCEDIMENTOS.REIDRATACAO" => 0, // terapida de reidratação oral
			"PROCEDIMENTOS.COLETIVOS" => 0  // Procedimentos coletivos
		);

		$all = $this->fetchAll($where);
		foreach ($all as $item)
			$retorno[$item->grupo] = $item->total;

		return $retorno;
	}

	/**
	 * Retorna quantos atendimentos indíviduais por enfermeiro foram feitos
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return int
	 */
	public function getTotalDeAtendimentoIndividualEnfermeiro($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$grupo = "CASE WHEN cod_cbo IN ('223565','223505','223515','233525','223545','223555','223560','2235C1','2235C2') THEN 'T'
			           ELSE 'F'
				  END";

		$where = $this->fromPatToAge()
				->columns(array("total" => "count(pat.pat_codigo)", "enfermeiro" => $grupo))
				->join(array("mes" => "medico_especialidade"), "mes.med_codigo=pat.usr_codigo", "")
				->where("proc.proc_codigo_sus IN (?)", array('0301010030', '0301010110', '0301010129'))
				->join(array("esp" => "especialidade"), "esp.esp_codigo=mes.esp_codigo", "")
				->group("esp.cod_cbo")
		;

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		$retorno = array(
			"ENF" => 0, // enfermeiros
			"OUT" => 0  // outros profissionais de nível superior
		);
		$all = $this->fetchAll($where);
		foreach ($all as $item)
			$retorno[$item->enfermeiro] = $item->total;

		return array(
			"PROCEDIMENTOS.INDIVIDUAL_ENFERMEIRO" => $retorno['ENF'],
			"PROCEDIMENTOS.INDIVIDUAL_OUTROS" => $retorno['OUT']
		);
	}

	/**
	 * Retorna a quantidade de exames solicitados, agrupado de acordo com a ficha PMA2
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalDeExamesComplementaresSolicitados($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {

		$grupo = "CASE WHEN proc.proc_codigo_sus='0201020033' THEN 'EXAMES.CITOPATOLOGIA'
            WHEN proc.proc_codigo_sus IN ('0205020143','0205020151','0205020160','0205010059') THEN 'EXAMES.ULTRASSONOGRAFIA'
            WHEN proc.proc_nome ILIKE '%radiografia%' THEN 'EXAMES.RADIODIAGNOSTICO'
            ELSE 'EXAMES.OUTROS'
       END ";

		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("req" => "requisicao_exames"), array("total" => "COUNT(req.req_codigo)", "grupo" => $grupo))
				->join(array("proc" => "procedimento"), "proc.proc_codigo=req.proc_codigo", "")
				->join(array("ate" => "atendimento"), "ate.ate_codigo=req.ate_codigo", "")
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
				->group("grupo");

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		$retorno = array(
			"EXAMES.PATOLOGIA" => 0, // patologia clínica
			"EXAMES.RADIODIAGNOSTICO" => 0, // radiodiagnostico
			"EXAMES.CITOPATOLOGIA" => 0, // citopalógico cérvico-vaginal
			"EXAMES.ULTRASSONOGRAFIA" => 0, // ultrassonografia
			"EXAMES.OUTROS" => 0 // adivinha?
		);

		$all = $this->fetchAll($where);
		foreach ($all as $item)
			$retorno[$item->grupo] = $item->total;

		return $retorno;
	}

	/**
	 * Total de atendimento especifico para acidende de trabalho (AT)
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return int
	 */
	public function getTotalDeAtendimentoEspecificoParaAcidendeDeTrabalho($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->fromPatToAge()
				->columns(array("total" => "count(pat.pat_codigo)"))
				->where("ate_acidentetrab=?", "S");

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		return $this->fetchRow($where)->total;
	}

	/**
	 * Faz o join das seguintes tabelas: usuario (pelo agendamento), psf e area
	 * @param Zend_Db_Table_Select $where
	 */
	private function joinUsuPsfArea(&$where) {
		$where->join(array("usu" => "usuario"), "usu.usu_codigo=age.usu_codigo", "")
				->joinLeft("psf", "psf.dom_codigo=usu.dom_codigo", "")
				->joinLeft("area", "area.area_codigo=psf.psf_area", "");
	}

	/**
	 * Inicia um select para buscar os procedimentos de um agendamento
	 * @return Zend_Db_Table_Select
	 */
	private function fromPatToAge() {
		return $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("pat" => "procedimento_atendimento"), "")
						->join(array("proc" => "procedimento"), "pat.proc_codigo=proc.proc_codigo", "")
						->joinLeft(array("pe" => "posto_enfermagem"), "pe.pe_codigo=pat.pe_codigo", "")
						->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo=pat.ate_codigo OR ate.ate_codigo=pe.ate_codigo", "")
						->joinLeft(array("pc" => "pre_consulta"), "pc.pc_codigo=pat.pc_codigo", "")
						->joinLeft(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo OR age.age_codigo=pc.age_codigo", "");
	}

	/**
	 * Retorna quantos CID's foram registrados
	 * Isso preenche os itens de 1 a 8 do bloco MARCADORES do Relat[oório PMA2
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalMarcadoresPorCid($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("cid" => "cid10"), array("total" => "count (cid.cd10_codigo)"))
				->columns(array("idade" => "extract(year from age(usu.usu_datanasc))"))
				->joinLeft(array("pat" => "procedimento_atendimento"), "cid.cd10_codigo=pat.cd10_codigo", "")
				->join(array("ate" => "atendimento"), "ate.cd10_codigo=cid.cd10_codigo OR ate.ate_codigo=pat.ate_codigo", "")
				->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
				->join(array("gc" => "grupos_cid"), "gc.cd10_codigo=cid.cd10_codigo", "")
				->join(array("gd" => "grupo_doencas"), "gd.gd_codigo=gc.gd_codigo", "gd_chave")
				->group(array("gd_chave", "idade"));

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		$retorno = array(
			"VAL" => 0, // VALVULOPATIAS REUMÁTICAS
			"AVC" => 0, // ACIDENTE VASCULAR CEREBRAL
			"IAM" => 0, // INFARTO AGUDO NO MIOCÁRDIO
			"DHE" => 0, // DHEG (forma grave)
			"DHP" => 0, // DOENÇA HEMOLÍTICA PERINATAL
			"FCF" => 0, // FRATURAS DE COLO DE FÊMUR
			"MET" => 0, // MENINGITE E TUBERCULOSE
			"HAN" => 0, // Hanseniase
			"CO " => 0  // CITOLOGIA ONCOTICA
		);

		$all = $this->fetchAll($where);
		foreach ($all as $item) {
			if ($item->gd_chave == "VAL" && $item->idade < 5 && $item->idade > 14)
				continue;

			if ($item->gd_chave == "FCF" && $item->idade < 50)
				continue;

			if ($item->gd_chave == "MET" && $item->idade >= 5)
				continue;

			$retorno[$item->gd_chave] += $item->total;
		}

		return array(
			"MARCADORES.VALVULOPATIAS" => $retorno["VAL"],
			"MARCADORES.AVC" => $retorno["AVC"],
			"MARCADORES.INFARTO" => $retorno["IAM"],
			"MARCADORES.DHEG" => $retorno["DHE"],
			"MARCADORES.DHP" => $retorno["DHP"],
			"MARCADORES.FRATURAS_FEMUR" => $retorno["FCF"],
			"MARCADORES.MENINGITE" => $retorno["MET"],
			"MARCADORES.HANSENIASE" => $retorno["HAN"],
			"MARCADORES.CITOLOGIA" => $retorno["CO "]
		);
	}

	/**
	 * Informa quantos RN < 2500g houve
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return int
	 */
	public function getTotalDeRecemNascidoComPesoMenorQue2500($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agendamento"), array("total" => "count(ate.ate_codigo)"))
				->join(array("ate" => "atendimento"), "age.age_codigo=ate.age_codigo", "")
				->join(array("ap" => "avaliacao_puerperal"), "ap.ate_codigo=ate.ate_codigo", "")
				->where("ava_peso < ?", 2.5);

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
		return $this->fetchRow($where)->total;
	}

	/**
	 * Retorna o total de gravidez em pacientes com menos de 20 anos
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return int
	 */
	public function getTotalDeGravidezEmMenorDe20Anos($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agendamento"), "")
				->join(array("ate" => "atendimento"), "ate.age_codigo=age.age_codigo", array("total" => "count(ate.ate_codigo)"))
				->join(array("sispn" => "sis_pre_natal"), "sispn.sispn_codigo=ate.sispn_codigo", "");

		$this->aplicarFiltroDeDataEArea($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
		$where->where("extract(year from age(usu_datanasc)) < 20");

		return $this->fetchRow($where)->total;
	}

	/**
	 * Total de óbitos em menores de 1 ano, de acordo com a causa
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return array
	 */
	public function getTotalDeObitosEmMenoresDe1Ano($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE) {
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("usu" => "usuario"), array("total" => "count(usu.usu_codigo)"))
				->joinLeft(array("gc" => "grupos_cid"), "gc.cd10_codigo=usu.cd10_codigo_obito", "")
				->joinLeft(array("gd" => "grupo_doencas"), "gd.gd_codigo=gc.gd_codigo", "gd_chave")
				->join("psf", "psf.dom_codigo=usu.dom_codigo", "")
				->where("psf.psf_area=?", $area_codigo) // filtro por area
				->where("extract(year from age(usu_datanasc)) < 1")
				->group("gd_chave");

		$this->aplicarFiltroDeDataDeObito($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		$retorno = array(
			"total" => 0,
			"DIA" => 0, // diarréia
			"IRE" => 0  // infecção respiatoria
		);
		$all = $this->fetchAll($where);
		foreach($all as $item){
			if($item->gd_chave)
				$retorno[ $item->gd_chave ] = $item->total;

			$retorno[ "total" ] += $item->total;
		}

		return array(
			'MARCADORES.OBITOS_TODAS' => $retorno["total"],
			'MARCADORES.OBITOS_DIARREIA' => $retorno["DIA"],
			'MARCADORES.OBITOS_INFECCAO' => $retorno["IRE"]
			);
	}

	/**
	 * Total de óbtios em mulheres de 10 a 49 anos
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * @return int
	 */
	public function getTotalDeObtiosEmMulheresDe10A49Anos($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE){
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("usu"=>"usuario"),array("total"=>"count(usu.usu_codigo)"))
				->where("extract(year from age(usu_datanasc)) BETWEEN 10 AND 49")
				->where("usu_sexo='F'");

		$this->aplicarFiltroDeDataDeObito($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);

		return $this->fetchRow($where)->total;
	}

	/**
	 * Total de óbitos de adolescentes (10-19) por violência
	 * @param date $data_inicial
	 * @param date $data_final
	 * @param int $area_codigo
	 * return int
	 */
	public function getTotalDeObitosEmAdolescentesPorViolencia($data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE){
		$where = $this->select()
				->setIntegrityCheck(FALSE)
				->from(array("usu"=>"usuario"),array("total"=>"count(usu.usu_codigo)"))
				->join(array("gc"=>"grupos_cid"),"gc.cd10_codigo=usu.cd10_codigo_obito","")
				->join(array("gd"=>"grupo_doencas"),"gd.gd_codigo=gc.gd_codigo","")
				->where("extract(year from age(usu_datanasc)) BETWEEN 10 AND 19")
				->where("gd_chave=?","VIO")
				;

		$this->aplicarFiltroDeDataDeObito($where, $data_inicial, $data_final, $area_codigo, $uni_codigo);
		return $this->fetchRow($where)->total;
	}

	/**
	 * Reuso do filtro por data de óbito
	 * @param Zend_Db_Table_Select $where
	 * @param date $data_inicial
	 * @param date $data_final
	 */
	private function aplicarFiltroDeDataDeObito(&$where, $data_inicial=FALSE, $data_final=FALSE, $area_codigo=FALSE, $uni_codigo=FALSE){
		if ($data_inicial)
			$where->where("usu.usu_dt_obito >= ?", $data_inicial);

		if ($data_final)
			$where->where("usu.usu_dt_obito <= ?", $data_final);

		if($area_codigo)
			$where->join("psf","psf.dom_codigo=usu.dom_codigo","")
			->where("psf.psf_area=?",$area_codigo);

		if($uni_codigo)
			$where->where("usu.uni_codigo_obito=?",$uni_codigo);
	}

        public function getRetorno(){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ate"=>"atendimento"),"ate_codigo")
                          ->joinLeft(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_atendido")
                          ->where("ate_encaminhamento = 'S'")
                          ->where("age.age_atendido != 'E'");
            //die($where);
            return $this->getCodigoInternacao($this->fetchAll($where)->toArray());
        }

        public function getCodigoInternacao($data=FALSE){
            $io_codigo = array();
            $ate_codigo = array();
            if(!empty($data)){
                foreach($data as $dados){
                    $where = $this->select(FALSE)
                                  ->setIntegrityCheck(FALSE)
                                  ->from(array("atin"=>"atendimento_internacao"),"io_codigo")
                                  ->where("ate_codigo=?",$dados[ate_codigo]);
                    $reg = $this->fetchRow($where);
                    $numRows = count($reg);
                    if($numRows == 0 || $numRows == null){
                        array_push($ate_codigo, $dados[ate_codigo]);
                        $registros = $this->getListaRetornoProntuario($ate_codigo);
                    }else{
                        array_push($io_codigo, $reg[io_codigo]);
                        $registros = $this->getListaRetornoInternacao($io_codigo);

                    }

                }
                return $registros;
            }else{
                return false;
            }

        }

        public function getListaRetornoInternacao($data=FALSE){

            foreach($data as $dados){
               $io_codigo .= $dados.",";
            }
            $rest = substr($io_codigo, 0, -1);
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("atin"=>"atendimento_internacao"),array("io_codigo","ate_codigo"))
                            ->join(array("ate"=>"atendimento"),"ate.ate_codigo=atin.ate_codigo",array("ate_codigo","ate_hora"))
                            ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_codigo")
                            ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario","usu_nome","usu_datanasc","usu_mae"))
                            ->where("io_codigo in($rest)");
            return $this->fetchAll($where)->toArray();
        }

        public function getListaRetornoProntuario($data=FALSE){
             foreach($data as $dados){
               $ate_codigo .= $dados.",";
            }
            $rest = substr($ate_codigo, 0, -1);
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("ate"=>"atendimento"),array("ate_codigo","ate_hora"))
                            ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_codigo")
                            ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario","usu_nome","usu_datanasc","usu_mae"))
                            ->where("ate_codigo in($rest)");
            //die($where);
            return $this->fetchAll($where)->toArray();
        }

        public function finalizaRetorno($ate_codigo=FALSE){
            //echo $ate_codigo;
            //die("aaaaaaa");
            $status = "N";
            $data = array(
                    'ate_encaminhamento'=> $status
                );
            $where = $this->select()->where("ate_codigo =?", $ate_codigo)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];
            return $this->update($data, $where);
        }

        public function buscarAtendidos( $data = FALSE ) {
            $tbUsr = new Application_Model_Usuarios();
            $usr = $tbUsr->getUsrAtual();
            if ($usr->usr_tipo_medico == "E" || $usr->usr_tipo_medico == "A") {
                $or = "OR ate.med_codigo = 99998";
            }

            if ($data[pre] == "" && $data[ate] == "") {
                $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ate"=>"atendimento"),array("ate.age_codigo", "usu_datanasc" => "DATE_PART('YEAR', AGE(CURRENT_DATE, usu.usu_datanasc))","ate_hora"))
                ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo",array("age_data","age_estratificacao_risco"))
                ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario", "usu_nome", "usu_mae", "usu_end_cidade"))
                ->where("ate.med_codigo = $usr->usr_codigo $or")
                ->where("ate.ate_data = CURRENT_DATE");

                if ($usr->usr_tipo_medico == "E" || $usr->usr_tipo_medico == "A") {
                    $where2 = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("pc"=>"pre_consulta"),array("pc.age_codigo", "usu_datanasc" => "DATE_PART('YEAR', AGE(CURRENT_DATE, usu.usu_datanasc))","to_char(pc_data,'HH24:MI') as ate_hora"))
                    ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo","age_data")
                    ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario", "usu_nome", "usu_mae", "usu_end_cidade"))
                    ->where("pc.usr_codigo=?",$usr->usr_codigo)
                    ->where("age.age_data = CURRENT_DATE");
                }
            }

            if ($data[ate]) {
                $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ate"=>"atendimento"),array("ate.age_codigo", "usu_datanasc" => "DATE_PART('YEAR', AGE(CURRENT_DATE, usu.usu_datanasc))","ate_hora"))
                ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo",array("age_data"))
                ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario", "usu_nome", "usu_mae", "usu_end_cidade"));
                if ($data[med_codigo]) {
                    $where->where("ate.med_codigo = $data[med_codigo] $or");
                }
                if ($data[usu_codigo]) {
                    $where->where("age.usu_codigo = $data[usu_codigo]");
                }
                if ($data[ate_data]) {
                    $where->where("ate.ate_data = '$data[ate_data]'");
                }
            }
            if ($data[pre]) {
                $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("pc"=>"pre_consulta"),array("pc.age_codigo", "usu_datanasc" => "DATE_PART('YEAR', AGE(CURRENT_DATE, usu.usu_datanasc))","to_char(pc_data,'HH24:MI') as ate_hora"))
                ->join(array("age"=>"agendamento"),"age.age_codigo=pc.age_codigo",array("age_data","age_estratificacao_risco"))
                ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_prontuario", "usu_nome", "usu_mae", "usu_end_cidade"));
                if ($data[med_codigo]) {
                    $where2->where("pc.usr_codigo = $data[med_codigo]");
                }
                if ($data[usu_codigo]) {
                    $where2->where("age.usu_codigo = $data[usu_codigo]");
                }
                if ($data[ate_data]) {
                    $where2->where("age.age_data = '$data[ate_data]'");
                }
            }

            $atendidos = array();
            if ($where != null) {
                $res1 = $this->fetchAll($where)->toArray();
                // percorre o resultado da sql para inserir o tipo de atendimento, a-> Atendimento PC -> Pré Consulta
                for ($i = 0; $i < count($res1); $i++) {
                    $res1[$i][usu_tipoAtendimento] = 'Atendimento';
                }
                array_push($atendidos, $res1);
            }

            if ($where2 != null) {
                $res2 = $this->fetchAll($where2)->toArray();

                for ($j = 0; $j < count($res2); $j++) {
                    $res2[$j][usu_tipoAtendimento] = 'Pré Consulta';
                }
                array_push($atendidos, $res2);
            }

            //echo "<pre>".print_r($atendidos,1);
            return $atendidos;
        }

        public function buscaRetornoOrigem($age_codigo=FALSE){
             if(empty($age_codigo)){
                $age_codigo = Application_Model_Agendamento::usuEmAberto()->age_codigo;
             }
             $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ate"=>"atendimento"),array("ate_codigo","gd_codigo","ate_encaminhamento"))
                          ->join(array("age"=>"agendamento"),"age.age_codigo=ate.age_codigo","age_atendido")
                          ->where("ate.age_codigo=?",$age_codigo)
                          ->order("ate_codigo");
             //die($where);
             return $this->fetchRow($where);
        }

        public function producaoDiariaConsulta($usr_codigo, $data_inicial, $data_final, $cd10_codigo, $tipo_consulta = FALSE) {
  $sql = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from( array("at"=>"atendimento"), array("desc" => "ate_reclamacao","data" => "ate_data"))
                        ->join( array("u"=>"usuario")  ,"u.usu_codigo = at.usu_codigo" , array("usu_nome"))
                        ->join( array("us"=>"usuarios"),"us.usr_codigo = at.med_codigo", array("usr_nome"))
                        ->order(array("at.med_codigo"));
                        //->limit("100");

            if (!Empty($usr_codigo)) {
                $sql->where ("at.med_codigo=?",$usr_codigo);
            }
            if (!Empty($data_inicial)) {
                $sql->where ("at.ate_data>=?",$data_inicial);
            }
            if (!Empty($data_final)) {
                $sql->where ("at.ate_data<=?",$data_final);
            }
            $i = 0;
            $cond = "";
            if ($tipo_consulta){
                foreach ($tipo_consulta as $tc) {
                    $i++;
                    if ($i == 1){
                        $cond = "$tc='t'";
                    } else {
                        $cond .="OR $tc='t'";
                    }
                }
                if (!Empty($cond)) {
                    $sql->where($cond);
                }
            }


            return $this->fetchAll($sql);
		}
		
		public function producaoDiariaPreConsulta($usr_codigo, $data_inicial, $data_final, $cd10_codigo, $tipo_consulta = FALSE) {
			$sql = $this->select()
						->setIntegrityCheck(FALSE)
						->from( array("ag"=>"agendamento"), array("age_data"))
						->join( array("u"=>"usuario")  ,"u.usu_codigo = ag.usu_codigo" , array("usu_nome"))
						->join( array("us"=>"usuarios"),"us.usr_codigo = ag.med_codigo", array("usr_nome"))
						->joinLeft( array("pc"=>"pre_consulta"), "pc.age_codigo = ag.age_codigo", array("desc" => "pc_dados","data" => "pc_data"))
						->order(array("ag.med_codigo"));
						
			if (!Empty($usr_codigo)) {
				$sql->where ("ag.med_codigo=?",$usr_codigo);
			}
			if (!Empty($data_inicial)) {
				$sql->where ("pc.pc_data>=?",$data_inicial);
			}
			if (!Empty($data_final)) {
				$sql->where ("pc.pc_data<=?",$data_final);
			}
			//die($sql);
			return $this->fetchAll($sql);
		}
        public function relAtendimentoPorIdade($uni_codigo, $usr_codigo,$esp_codigo,$data_inicial = FALSE, $data_final = FALSE, $usu_sexo = FALSE){
            $where = $this->select()
                     ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"),"COUNT(*) as total")
                    ->join(array("usu"=>"usuario"),"ate.usu_codigo = usu.usu_codigo","")
                    ->join(array("age"=>"agendamento"),"age.age_codigo = ate.age_codigo","extract(year from age(usu_datanasc)) as anos")
                    ->group("anos")
                    ->order("anos");
                    if($usu_sexo!='0') {
                        $where->where("usu.usu_sexo=?",$usu_sexo);                        
                    }
                    if($uni_codigo){
                        $where->where("age.uni_codigo=?",$uni_codigo);
                    }
                    if($usr_codigo){
                        $where->where("age.med_codigo=?",$usr_codigo);
                    }
                    if($esp_codigo){
                        $where->where("age.esp_codigo=?",$esp_codigo);
                    }
                    if($data_inicial){
                        $where->where("ate.ate_data>=?",$data_inicial);
                    }
                    if($data_final){
                        $where->where("ate.ate_data<=?",$data_final);
                    }
                    
                  return $this->fetchAll($where);
        }
        // Lista os CIDS do atendimento
        public function listaCidsAtendimento($codAtend){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate"=>"atendimento"),array("cd10_codigo","cd10_codigos","cd10_codigot"))
                        ->joinLeft(array("cd10"=>"cid10"),"cd10.cd10_codigo=ate.cd10_codigo",array("cd10_descricao AS cd10_codigo_desc","cd10_codigo_cid AS cd10_codigo_cid"))
                        ->joinLeft(array("cd101"=>"cid10"),"cd101.cd10_codigo=ate.cd10_codigos", array("cd10_descricao AS cd10_codigos_desc", "cd10_codigo_cid AS cd10_codigos_cid"))
                        ->joinLeft(array("cd102"=>"cid10"), "cd102.cd10_codigo=ate.cd10_codigot", array("cd10_descricao AS cd10_codigot_desc", "cd10_codigo_cid AS cd10_codigot_cid"))
                        ->where("ate_codigo =?",$codAtend);
						//die($sql);
            return $this->fetchRow($sql);
        }


        public function excluir($ate_codigo=FALSE){
            $item = $this->fetchRow("ate_codigo=$ate_codigo");
            try{
                if ($item) {
                    $item->delete();
                }
            } catch (Exception $ex) {
                die($ex->getMessage());
                $ex->getMessage();
            }
            return true;
		}
		
		public function isGestante($usu_codigo=FALSE){
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), array("ate_codigo","ate_idade_gest"))
						->order("ate.ate_codigo DESC")
						->limit(1);

			if($usu_codigo){
				$sql->where("ate.usu_codigo=?", $usu_codigo);
			}

			$isGest = $this->fetchRow($sql);

			if($isGest->ate_idade_gest != null && $isGest->ate_idade_gest != ""){
				return true;
			}

			return false;
		}

		public function getIdadeGestacional($usu_codigo=FALSE){
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), array("ate_data","ate_idade_gest"))
						->join(array("usu" => "usuario"), "usu.usu_codigo=ate.usu_codigo", array("usu_sexo"))
						->order("ate.ate_codigo DESC")
						->limit(1);

			if($usu_codigo){
				$sql->where("ate.usu_codigo=?", $usu_codigo);
			}
			//die($sql);
			return $this->fetchRow($sql);
		}

        public function getProfissionaisProcedimentoPorMes($dataInicial = FALSE, $dataFinal = FALSE, $proc_codigo = FALSE,$uni_codigo = FALSE){
            $sql = $this->select(FALSE)
                ->distinct()
              ->setIntegrityCheck(FALSE)
              ->from(array("ate" => "atendimento"), "")
              ->join(array("usr" => "usuarios"), "usr.usr_codigo = ate.med_codigo",array("usr_codigo","usr_nome"))
              ->join(array("pat" => "procedimento_atendimento"), "pat.ate_codigo = ate.ate_codigo", "")
              ->join(array("proc" => "procedimento"),"proc.proc_codigo = pat.proc_codigo","")
              ->group(array("usr.usr_codigo"));

              if($proc_codigo){
                $sql->where("proc.proc_codigo=?", $proc_codigo);
              }
              if($dataInicial){
                $sql->where("ate.ate_data>=?", $dataInicial);
              }
              if($dataFinal){
                $sql->where("ate.ate_data<=?", $dataFinal);
              }
               if($uni_codigo){
                $sql->where("ate.uni_codigo=?", $uni_codigo);
              }

              // die($sql);
              return $this->fetchAll($sql);
              
        }
		
		public function getTotalProcedimentoPorMesPorProfissional($usr_codigo=FALSE, $dataInicial = FALSE, $dataFinal = FALSE, $proc_codigo = FALSE,$uni_codigo = FALSE){
            $sql = $this->select(FALSE)
              ->setIntegrityCheck(FALSE)
              ->from(array("pat" => "procedimento_atendimento"),"")
              ->join(array("ate" => "atendimento"),"pat.ate_codigo = ate.ate_codigo","count(pat.ate_codigo) as total")
              ->join(array("proc" => "procedimento"),"proc.proc_codigo = pat.proc_codigo",array("proc.proc_nome"))
              ->join(array("uni" => "unidade"),"uni.uni_codigo = ate.uni_codigo",array("uni.uni_desc"))
              ->join(array("usr" => "usuarios"), "usr.usr_codigo = ate.med_codigo",array("usr_nome"))
              ->group(array("proc.proc_nome", "uni.uni_desc", "usr.usr_nome"))
              ->order(array("uni_desc"));

              if($usr_codigo!=""){
                $sql->where("usr.usr_codigo=?", $usr_codigo);
              }
              if($proc_codigo){
                $sql->where("proc.proc_codigo=?", $proc_codigo);
              }
              if($dataInicial){
                $sql->where("ate.ate_data>=?", $dataInicial);
              }
              if($dataFinal){
                $sql->where("ate.ate_data<=?", $dataFinal);
              }
              if($uni_codigo){
                $sql->where("ate.uni_codigo=?", $uni_codigo);
              }

            //  die($sql);
              return $this->fetchAll($sql);
		      
		}

		public function getNumeroAtendimentoCondicaoAvaliada($data_inicial=FALSE, $data_final=FALSE){
			/*Obesidade*/
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), "COUNT(*) as total")
						->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
						->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap","")
						->where("tc.co_seq_ciap = 733"); //Obesidade

						if($data_inicial){
							$where->where("ate.ate_data>=?",$data_inicial);
						}
						if($data_final){
							$where->where("ate.ate_data<=?",$data_final);
						}
			$ObesTotal = $this->fetchRow($sql);
			/*Hipertensao*/
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), "COUNT(*) as total")
						->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
						->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap","")
						->where("tc.co_seq_ciap = 731"); 

						if($data_inicial){
							$where->where("ate.ate_data>=?",$data_inicial);
						}
						if($data_final){
							$where->where("ate.ate_data<=?",$data_final);
						}
			$HASTotal = $this->fetchRow($sql);
			/*Diabetes*/
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), "COUNT(*) as total")
						->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
						->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap","")
						->where("tc.co_seq_ciap = 732"); 

						if($data_inicial){
							$where->where("ate.ate_data>=?",$data_inicial);
						}
						if($data_final){
							$where->where("ate.ate_data<=?",$data_final);
						}
			$DMTotal = $this->fetchRow($sql);
			/*Depressão*/
			$sql = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->from(array("ate" => "atendimento"), "COUNT(*) as total")
						->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
						->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap","")
						->where("tc.co_seq_ciap = 280"); 

						if($data_inicial){
							$where->where("ate.ate_data>=?",$data_inicial);
						}
						if($data_final){
							$where->where("ate.ate_data<=?",$data_final);
						}
			$DepTotal = $this->fetchRow($sql);

			$dados = array(
				"HAS" => $HASTotal->total,
				"DM" => $DMTotal->total,
				"Obes" => $ObesTotal->total,
				"Dep" => $DepTotal->total
			);
			return $dados;

		}
public function getMotivoPorAtendimento($ate_codigo=FALSE){
            $where = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ate"=>"atendimento"), "")
                    ->joinLeft(array("tcvd"=>"tb_cds_visita_domiciliar"), "tcvd.ate_codigo = ate.ate_codigo","")
                    ->joinLeft(array("rcvdm" => "rl_cds_visita_dom_motivo"), "rcvdm.co_cds_visita_domiciliar = tcvd.co_seq_cds_visita_domiciliar", "")
                    ->joinLeft(array("tcvdm" => "tb_cds_visita_dom_motivo"), "tcvdm.co_cds_visita_dom_motivo = rcvdm.co_cds_visita_dom_motivo", array("tcvdm.no_cds_visita_dom_motivo"))
                    ->where("ate.ate_codigo=?",$ate_codigo);
                    //die($where);
            return $this->fetchAll($where);
    } 


public function getDadosVisitaDomiciliar($med_codigo=FALSE, $uni_codigo=FALSE, $dataFinal=FALSE, $dataInicial=FALSE){
            $dados = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ate"=>"atendimento"),array("ate_codigo", "ate_data"))
                          ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo = ate.med_codigo", array("usr.usr_nome"))
                          ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo = ate.usu_codigo", array("usu.usu_nome"))
                          ->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo = ate.uni_codigo", array("uni.uni_desc"))
                          ->joinLeft(array("tcvd"=>"tb_cds_visita_domiciliar"), "tcvd.ate_codigo = ate.ate_codigo","")
                          ->joinLeft(array("tcvdd" => "tb_cds_visita_dom_desfecho"), "tcvd.co_cds_visita_dom_desfecho = tcvdd.co_cds_visita_dom_desfecho", array("tcvdd.no_cds_visita_dom_desfecho"))
                        //   ->joinLeft(array("rcvdm" => "rl_cds_visita_dom_motivo"), "rcvdm.co_cds_visita_domiciliar = tcvd.co_seq_cds_visita_domiciliar", "")
                        //   ->joinLeft(array("tcvdm" => "tb_cds_visita_dom_motivo"), "tcvdm.co_cds_visita_dom_motivo = rcvdm.co_cds_visita_dom_motivo", array("tcvdm.no_cds_visita_dom_motivo"))
                          ->where("ate.ate_tipo = 'V'")
                          ->order("ate.ate_data");
                        if($uni_codigo){
                            $dados->where("ate.uni_codigo=?",$uni_codigo);
                        }
                        if($med_codigo){
                            $dados->where("ate.med_codigo=?",$med_codigo);
                        }
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        //die($dados); 
            return $this->fetchAll($dados);
        }

    public function getDadosVisitaDomiciliarPorUsuario($usu_codigo=FALSE){
            $dados = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ate"=>"atendimento"),array("ate_codigo", "ate_data"))
                          ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo = ate.med_codigo", array("usr.usr_nome"))
                          ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo = ate.usu_codigo", array("usu.usu_nome"))
                          ->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo = ate.uni_codigo", array("uni.uni_desc"))
                          ->joinLeft(array("tcvd"=>"tb_cds_visita_domiciliar"), "tcvd.ate_codigo = ate.ate_codigo","")
                          ->joinLeft(array("tcvdd" => "tb_cds_visita_dom_desfecho"), "tcvd.co_cds_visita_dom_desfecho = tcvdd.co_cds_visita_dom_desfecho", array("tcvdd.no_cds_visita_dom_desfecho"))
                        //   ->joinLeft(array("rcvdm" => "rl_cds_visita_dom_motivo"), "rcvdm.co_cds_visita_domiciliar = tcvd.co_seq_cds_visita_domiciliar", "")
                        //   ->joinLeft(array("tcvdm" => "tb_cds_visita_dom_motivo"), "tcvdm.co_cds_visita_dom_motivo = rcvdm.co_cds_visita_dom_motivo", array("tcvdm.no_cds_visita_dom_motivo"))
                          ->where("ate.ate_tipo = 'V'")
                          ->order("ate.ate_data");
                        if($usu_codigo){
                            $dados->where("ate.usu_codigo=?",$usu_codigo);
                        }
                        //die($dados); 
            return $this->fetchAll($dados);
        }

    public function verificaDoencaCronica($usu_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "COUNT(*) as total")
                        ->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
                        ->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap","")
                        ->where("tc.co_seq_ciap = 733 OR tc.co_seq_ciap = 731 OR tc.co_seq_ciap = 732 OR tc.co_seq_ciap = 280"); 
                        //Obesidade
                        //Hipertensao
                        //Diabetes
                        //Depressao

                        if($usu_codigo){
                            $sql->where("ate.usu_codigo=?",$usu_codigo);
                        }
            $dc = $this->fetchRow($sql);

            if($dc->total > 0){
                return true;
            }

            return false;
    }

    public function getDoencaCronica($usu_codigo=FALSE){
        $i = 0;
        $sql = $this->select(FALSE)
                    //->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "")
                        ->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
                        ->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap", array("tc.co_ciap", "tc.ds_ciap"))
                        ->where("tc.co_seq_ciap = 733") 
                        ->group(array("tc.co_ciap", "tc.ds_ciap"));
                        //Obesidade
                        //Hipertensao
                        //Diabetes
                        //Depressao

                        if($usu_codigo){
                            $sql->where("ate.usu_codigo=?",$usu_codigo);
                        }
            $result = $this->fetchRow($sql);
            if($result->co_ciap != null){
                $dc[$i] = $result;
                $i++;
            }


            $sql = $this->select(FALSE)
                    //->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "")
                        ->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
                        ->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap", array("tc.co_ciap", "tc.ds_ciap"))
                        ->where("tc.co_seq_ciap = 731") 
                        ->group(array("tc.co_ciap", "tc.ds_ciap"));
                        //Obesidade
                        //Hipertensao
                        //Diabetes
                        //Depressao

                        if($usu_codigo){
                            $sql->where("ate.usu_codigo=?",$usu_codigo);
                        }

            $result = $this->fetchRow($sql);
            if($result->co_ciap != null){
                $dc[$i] = $result;
                $i++;
            }

            $sql = $this->select(FALSE)
                    //->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "")
                        ->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
                        ->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap", array("tc.co_ciap", "tc.ds_ciap"))
                        ->where("tc.co_seq_ciap = 732") 
                        ->group(array("tc.co_ciap", "tc.ds_ciap"));
                        //Obesidade
                        //Hipertensao
                        //Diabetes
                        //Depressao

                        if($usu_codigo){
                            $sql->where("ate.usu_codigo=?",$usu_codigo);
                        }
            $result = $this->fetchRow($sql);
            if($result->co_ciap != null){
                $dc[$i] = $result;
                $i++;
            }

            $sql = $this->select(FALSE)
                    //->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "")
                        ->join(array("rcaic" => "rl_cds_atend_individual_ciap"), "rcaic.ate_codigo=ate.ate_codigo","")
                        ->join(array("tc" => "tb_ciap"), "rcaic.co_ciap=tc.co_seq_ciap", array("tc.co_ciap", "tc.ds_ciap"))
                        ->where("tc.co_seq_ciap = 280") 
                        ->group(array("tc.co_ciap", "tc.ds_ciap"));
                        //Obesidade
                        //Hipertensao
                        //Diabetes
                        //Depressao

                        if($usu_codigo){
                            $sql->where("ate.usu_codigo=?",$usu_codigo);
                        }
            $result = $this->fetchRow($sql);
            if($result->co_ciap != null){
                $dc[$i] = $result;
                $i++;
            }

            return $dc;
    }

    public function getEstratificacaoRisco($usu_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), array("ate_estratificacao_risco_g1", "ate_estratificacao_risco_g2"))
                        ->join(array("usu" => "usuario"), "usu.usu_codigo=ate.usu_codigo", array("usu_sexo"))
                        ->order("ate.ate_codigo DESC")
                        ->limit(1);

            if($usu_codigo){
                $sql->where("ate.usu_codigo=?", $usu_codigo);
            }
            return $this->fetchRow($sql);
    }

    public function getVisitaInternamentoQtde($usr_codigo=FALSE, $dataInicial=FALSE, $dataFinal=FALSE){
        $dados = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), "COUNT(*) as total")
                        ->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo=ate.uni_codigo", array("uni.uni_desc"))
                        ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr.usr_codigo"))
                        ->where("ate.ate_inter_data is not null and ate.ate_inter_data != '1900-01-01'")
                        ->where("ate.ate_inter_motivo is not null")
                        ->where("ate.ate_tipo = 'V'")
                        ->group(array("uni.uni_desc", "usr.usr_codigo"));
                        if($usr_codigo){
                            $dados->where("ate.med_codigo=?",$usr_codigo);
                        }
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        //die($dados); 
            return $this->fetchAll($dados);
    }   

    public function getVisitaInternamento($usr_codigo=FALSE, $dataInicial=FALSE, $dataFinal=FALSE){
        $dados = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), array("ate_inter_data", "ate_inter_motivo"))
                        ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo=ate.usu_codigo", array("usu.usu_nome"))
                        ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr.usr_nome", "usr.usr_codigo"))
                        ->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo=ate.uni_codigo", array("uni.uni_desc"))
                        ->where("ate.ate_inter_data is not null and ate.ate_inter_data != '1900-01-01'")
                        ->where("ate.ate_inter_motivo is not null")
                        ->where("ate.ate_tipo = 'V'");
                        if($usr_codigo){
                            $dados->where("ate.med_codigo=?",$usr_codigo);
                        }
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        //die($dados); 
            return $this->fetchAll($dados);
    }

    public function getProfissionaisVisitaInternamento($dataInicial=FALSE, $dataFinal=FALSE){
        $dados = $this->select(FALSE)
                        ->distinct()  
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"), array("usr_codigo"=>"med_codigo"))
                        ->joinLeft(array("usr"=>"usuarios"), "usr.usr_codigo=ate.med_codigo", array("usr.usr_nome"))
                        ->where("ate.ate_inter_data is not null and ate.ate_inter_data != '1900-01-01'")
                        ->where("ate.ate_inter_motivo is not null")
                        ->where("ate.ate_tipo = 'V'");
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        //die($dados); 
            return $this->fetchAll($dados);
    }

    public function getProfissionalComProducaoPorPeriodo($dataInicial=FALSE, $dataFinal=FALSE, $proc_codigo=FALSE){
            $dados = $this->select(FALSE)
                        ->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("med_codigo"))
                        ->join(array("proca"=>"procedimento_atendimento"),"ate.ate_codigo=proca.ate_codigo","")
                        ->join(array("proc"=>"procedimento"),"proca.proc_codigo=proc.proc_codigo","")
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome", "usr_codigo"));

                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($proc_codigo){
                            $dados->where("proc.proc_codigo=?",$proc_codigo);
                        }
            return $this->fetchAll($dados);
    }

    public function getProducaoProfissional($dataInicial=FALSE, $dataFinal=FALSE, $proc_codigo=FALSE, $usr_codigo=FALSE){
        $dados = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("ate_data", "usr_codigo" => "med_codigo"))
                        ->join(array("proca"=>"procedimento_atendimento"),"ate.ate_codigo=proca.ate_codigo","")
                        ->join(array("proc"=>"procedimento"),"proca.proc_codigo=proc.proc_codigo",array("total" => "count(proc_codigo_sus)","proc_codigo_sus","proc_nome","proc_codigo"))
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo","")
                        ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo","")
                        ->group(array("proc_codigo_sus","proc_nome","proc.proc_codigo", "ate.ate_data", "ate.med_codigo"))
                        ->order("proc_codigo");
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($usr_codigo){
                            $dados->where("ate.med_codigo=?",$usr_codigo);
                        }
                        if($proc_codigo){
                            $dados->where("proc.proc_codigo=?",$proc_codigo);
                        }
            return $this->fetchAll($dados);
    }

    public function getPacientesComEstratificacaoRisco($dataInicial=FALSE, $dataFinal=FALSE, $uni_codigo=FALSE, $select_grupo=FALSE, $usr_codigo=FALSE){
        $dados = $this->select(FALSE)
                        ->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("med_codigo"))
                        ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome", "usu_codigo"))
                        ->join(array("er"=>"estratificacao_risco"), "er.er_codigo=ate.ate_estratificacao_risco_g1 or er.er_codigo=ate.ate_estratificacao_risco_g2", array("er_grupo"));

                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($uni_codigo){
                            $dados->where("ate.uni_codigo=?",$uni_codigo);
                        }
                        if($usr_codigo){
                            $dados->where("ate.med_codigo=?",$usr_codigo);
                        }

                        if($select_grupo == 1) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                        } else if($select_grupo == 2) {
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else if($select_grupo == 3) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else {
                           $dados->where("ate.ate_estratificacao_risco_g1 is not null or ate.ate_estratificacao_risco_g2 is not null"); 
                        }
            return $this->fetchAll($dados);

    }

    public function getProfissionaisEstratificacaoRisco($dataInicial=FALSE, $dataFinal=FALSE, $uni_codigo=FALSE, $select_grupo=FALSE){
        $dados = $this->select(FALSE)
                        ->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("med_codigo"))
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo",array("usr_nome", "usr_codigo"));

                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($uni_codigo){
                            $dados->where("ate.uni_codigo=?",$uni_codigo);
                        }

                        if($select_grupo == 1) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                        } else if($select_grupo == 2) {
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else if($select_grupo == 3) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else {
                           $dados->where("ate.ate_estratificacao_risco_g1 is not null or ate.ate_estratificacao_risco_g2 is not null"); 
                        }
            return $this->fetchAll($dados);

    }

    public function getDadosEstratificacaoRiscoG1($dataInicial=FALSE, $dataFinal=FALSE, $uni_codigo=FALSE, $select_grupo=FALSE, $usu_codigo=FALSE){
        $dados = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("usr_codigo"=>"med_codigo", "ate_data"))
                        ->join(array("er"=>"estratificacao_risco"), "er.er_codigo=ate.ate_estratificacao_risco_g1", array("er_desc", "er_cor", "er_grupo"))
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo","")
                        ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome", "usu_codigo", "usu_mae", "age"=>"EXTRACT(YEAR from AGE(CURRENT_DATE, usu_datanasc))"))
                        ->joinLeft(array("uni"=>"unidade"),"ate.uni_codigo=uni.uni_codigo",array("uni_desc", "uni_codigo"))
                        ->order("ate.ate_codigo DESC")
                        ->limit(1);
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($uni_codigo){
                            $dados->where("ate.uni_codigo=?",$uni_codigo);
                        }
                        if($usu_codigo){
                            $dados->where("ate.usu_codigo=?",$usu_codigo);
                        }


                        if($select_grupo == 1) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                        } else if($select_grupo == 2) {
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else if($select_grupo == 3) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null and ate.ate_estratificacao_risco_g2 is not null");
                        } else {
                           $dados->where("ate.ate_estratificacao_risco_g1 is not null or ate.ate_estratificacao_risco_g2 is not null"); 
                        }

            return $this->fetchAll($dados);

    }

    public function getDadosEstratificacaoRiscoG2($dataInicial=FALSE, $dataFinal=FALSE, $uni_codigo=FALSE, $select_grupo=FALSE, $usu_codigo=FALSE){
        $dados = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ate" => "atendimento"),array("usr_codigo"=>"med_codigo", "ate_data"))
                        ->join(array("er"=>"estratificacao_risco"), "er.er_codigo=ate.ate_estratificacao_risco_g2", array("er_desc", "er_cor", "er_grupo"))
                        ->join(array("usr"=>"usuarios"),"ate.med_codigo=usr.usr_codigo","")
                        ->join(array("usu"=>"usuario"),"ate.usu_codigo=usu.usu_codigo",array("usu_nome", "usu_codigo", "usu_mae", "age"=>"EXTRACT(YEAR from AGE(CURRENT_DATE, usu_datanasc))"))
                        ->joinLeft(array("uni"=>"unidade"),"ate.uni_codigo=uni.uni_codigo",array("uni_desc", "uni_codigo"))
                        ->order("ate.ate_codigo DESC")
                        ->limit(1);
                        if($dataInicial){
                            $dados->where("ate.ate_data>=?",$dataInicial);
                        }
                        if($dataFinal){
                            $dados->where("ate.ate_data<=?",$dataFinal);
                        }
                        if($uni_codigo){
                            $dados->where("ate.uni_codigo=?",$uni_codigo);
                        }
                        if($usu_codigo){
                            $dados->where("ate.usu_codigo=?",$usu_codigo);
                        }


                        if($select_grupo == 1) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null");
                        } else if($select_grupo == 2) {
                            $dados->where("ate.ate_estratificacao_risco_g2 is not null");
                        } else if($select_grupo == 3) {
                            $dados->where("ate.ate_estratificacao_risco_g1 is not null and ate.ate_estratificacao_risco_g2 is not null");
                        } else {
                           $dados->where("ate.ate_estratificacao_risco_g1 is not null or ate.ate_estratificacao_risco_g2 is not null"); 
                        }

            return $this->fetchAll($dados);

    }

    // public function coletarDadosDoAtendimento(){
    //     $recebeDadosDoAtendimento = $this->getDefaultAdapter()->query(
    //         "SELECT * FROM atendimento WHERE ate_data >= '2018/12/01' AND ate_data <= '2018/12/31' "
    //     )->fetchAll();

    //     echo "<pre>";print_r($recebeDadosDoAtendimento);die();
    // }

    public function coletarDadosDoAtendimento(){
        $recebeDadosDoAtendimento = $this->getDefaultAdapter()->query(
            "SELECT ate.ate_codigo, age.tat_codigo ,tbl.co_local_atend, usr.cnes_cod_cns ,esp.cod_cbo ,uni.uni_cnes,
            ate.ate_data, uni.uni_codigo_ibge ,usu.usu_cartao_sus ,usu.usu_datanasc ,usu.usu_prontuario , usu.usu_sexo 
        
            FROM atendimento as ate

            INNER JOIN 
                        agendamento AS age 
                    ON ate.age_codigo = age.age_codigo
                    INNER JOIN 
                        especialidade AS esp 
                    ON age.esp_codigo = esp.esp_codigo
                    INNER JOIN 
                        usuarios AS usr 
                    ON ate.med_codigo = usr.usr_codigo
                    INNER JOIN
                        usuario AS usu 
                    ON ate.usu_codigo = usu.usu_codigo
                    INNER JOIN
                        unidade AS uni 
                    ON ate.uni_codigo = uni.uni_codigo
                    INNER JOIN 
                        tb_local_atend AS tbl 
                    ON ate.co_local_atend = tbl.co_local_atend
                    INNER JOIN
                        rl_cds_atend_individual_ciap AS rlai 
                    ON ate.ate_codigo = rlai.ate_codigo
                    INNER JOIN 
                        rl_cds_atend_individual_condut AS rlaic 
                    ON ate.ate_codigo = rlaic.ate_codigo
            
            WHERE ate.ate_data >= '2018/12/03' AND ate.ate_data <= '2018/12/31'
        "
        )->fetchAll();

        // echo "<pre>";print_r($recebeDadosDoAtendimento);die();
    }

    public function atendimentoOdonto($ateCodigo){
        $ateCodigo = $ateCodigo;
        // die($ateCodigo);
        $sql = $this->getDefaultAdapter()->query(
            "
                  SELECT ate.ate_codigo, ate.ate_data, ate.med_codigo, ate.usu_codigo, ate.age_codigo,
                    ate.uni_codigo, ate.turno, ate.ate_atendido, ate.co_local_atend, ate.usu_dtnascimento, ate.usu_possui_necessidade_especial, 
                    ate.usu_possui_necessidade_especial, ate.fornecimento_odonto, ate.usu_sexo, ate.procedimento_odonto_ab_sia, 
                    ate.procedimento_odonto_ab_sia, usu.usu_nome, usr.usr_nome, uni.uni_desc, age.esp_codigo,age.age_codigo, 
                    pcont.odo_pcon_codigo, esp.esp_nome, usr.usr_codigo, usu.usu_datanasc, usu.usu_esta_gestante FROM atendimento AS ate
                    INNER JOIN usuario AS usu
                        on usu.usu_codigo = ate.usu_codigo
                    INNER JOIN usuarios AS usr
                        on usr.usr_codigo = ate.med_codigo
                    INNER JOIN unidade as uni
                        on uni.uni_codigo = ate.uni_codigo
                    Inner join agendamento as age
                        on  ate.age_codigo = age.age_codigo
                    INNER JOIN especialidade as esp
                        on age.esp_codigo = esp.esp_codigo 
                    Inner join odonto_procedimentos_controle as pcont
                        on ate.ate_codigo = pcont.ate_codigo
                    Inner join odonto_procedimentos_realizados as procr
                        on pcont.odo_pcon_codigo = procr.odo_pcon_codigo
                            
                    WHERE ate.ate_codigo = $ateCodigo

            "
            )->fetchAll();
        return $sql;
    }

    public function recuperaAtendimentoConsultaOdontologia($ateCodigo){
        $ateCodigo = $ateCodigo;

        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT ate.age_codigo, age.tat_codigo, age.tp_cod FROM atendimento AS ate
                INNER JOIN agendamento AS age
                    ON ate.age_codigo = age.age_codigo
                WHERE ate.ate_codigo = $ateCodigo
            "
        )->fetchAll();

        return $sql;
    }

    public function recuperaVigilanciaConduta($ateCodigo){
        $ateCodigo = $ateCodigo;

        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT ate.ate_codigo, vig.tp_cds_vig_saude_bucal, encam.tp_cds_encam_odonto FROM atendimento AS ate
                INNER JOIN rl_cds_atend_odont_tip_vig_buc AS vig
                    ON vig.ate_codigo = ate.ate_codigo
                INNER JOIN rl_cds_atend_odonto_tipo_encam AS encam
                    ON encam.ate_codigo = ate.ate_codigo
                WHERE ate.ate_codigo = $ateCodigo order by tp_cds_vig_saude_bucal desc
            "
        )->fetchAll();

        return $sql;
    }

    public function atualizaPesoAltura($dadosUsu){
        $dadosUsu = $dadosUsu;
        $usuCodigo = intval($dadosUsu[usu_codigo]);
        $usuPeso = floatval($dadosUsu[ate_peso]);
        $usuAltura = floatval($dadosUsu[ate_altura]);

        $sql = $this->getDefaultAdapter()->query(
            "UPDATE ATENDIMENTO SET ate_peso = $usuPeso, ate_altura = $usuAltura WHERE usu_codigo = $usuCodigo"
        )->fetchAll();


    }

    public function recuperaBeneficioAoSalvar($recebeCodigoAtendimento){
        $recuperaBeneficioAoSalvar = intval($recebeCodigoAtendimento);
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT usu.usu_cartao_sus, usu.usu_nome, usu.usu_mae, usu.usu_celular, proc.proc_nome, pro_ate.quantidade_total_do_procedimento, pro_ate.valor_do_procedimento, med.med_nome_fantasia, med.med_endereco, med.med_end_bairro, med.med_end_telefone from atendimento as ate
                    INNER JOIN agendamento AS age
                        ON ate.age_codigo = age.age_codigo
                    INNER JOIN usuario AS usu
                        ON usu.usu_codigo = age.usu_codigo
                    INNER JOIN procedimento_atendimento AS pro_ate
                        ON pro_ate.ate_codigo = ate.ate_codigo 
                    INNER JOIN procedimento AS proc
                        ON proc.proc_codigo = pro_ate.proc_codigo
                    left join medico as med
                        on med.med_codigo = ate.laboratorio_de_destino
                where ate.ate_codigo = $recuperaBeneficioAoSalvar
            "
        )->fetchAll();

        return $sql;
    }

}
