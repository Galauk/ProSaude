<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

/**
 * Essa agenda é resposável pelo novo agendamento
 * @todo agendar exames e consultas, por quantidade (cota) e valor
 */
class Application_Model_Agenda extends Elotech_Db_Table_Abstract {

	protected $_name = 'agenda';
	protected $_primary = 'age_codigo';
	protected $_dependentTables = array();

	/**
	 * Insert ou update na agenda, com seus filhos
	 * @param array $data dados do formulário
	 * @return int chave primária do registro inserido ou atualizado
	 */
	public function salvar(array $data) {

		$this->valoresPadrao($data);
		$this->notEmpty(array("usu_codigo"), $data);
		$this->solicitanteInternoOuExterno($data);
		$this->peloMenosUm(array("med_codigo", "usr_codigo_medico"), $data);
		$this->emptyToUnset($data);

		$itens = $data['itens'];
		unset($data['itens']);


		$this->getDefaultAdapter()->beginTransaction();
		try {
			$age_codigo = parent::salvar($data);

			$tbAgeI = new Application_Model_AgendaItens();

			$tbAgeI->salvarDoArray($itens, $age_codigo, $data);

			$this->getDefaultAdapter()->commit();
		} catch (Exception $exc) {
			$this->getDefaultAdapter()->rollBack();
			throw new Zend_Validate_Exception($exc->getMessage());
		}

		return $age_codigo;
	}


	/**
	 * Trabalha os dados recebidos e diz se o médico solicitante é interno ou externo
	 * @param array $data dados do formulário
	 */
	private function solicitanteInternoOuExterno(&$data) {
		if (!$data['interno']) { // 1=interno, 0=externo
			$data["med_codigo"] = $data["usr_codigo_medico"];
			unset($data["usr_codigo_medico"]);
		}

		unset($data['interno']);
	}

	/**
	 * Retorna os dados do agendamento
	 * @param int $age_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getAgendamento($age_codigo,$coletados=FALSE) {
        $tbConf = new Application_Model_Configuracao();
        $valor = $tbConf->getConfig('VALOR_GUIA_EXAME');
        $coni_valor = "";
        if($valor == 1){
            $coni_valor = "coni_valor";
        }

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("age" => "agenda"), "usu_codigo")
                ->join(array("agei" => "agenda_itens"), "agei.age_codigo=age.age_codigo", array("agei_data","agei_codigo", "age_codigo"))
                ->join(array("coni" => "convenio_itens"), "coni.coni_codigo=agei.coni_codigo", array("$coni_valor"))
                ->join(array("conv" => "convenio"), "conv.conv_codigo=coni.conv_codigo", "")
                ->join(array("med" => "medico"), "med.med_codigo=conv.med_codigo", array("med_nome", "med_endereco", "med_end_telefone")) // joinLeft + joinLeft(unidade)
                ->joinLeft(array("medp"=>"medico"),"age.med_codigo=medp.med_codigo",array("medico_e"=>"med_nome"))
                ->joinLeft(array("usr"=>"usuarios"),"age.usr_codigo_medico=usr.usr_codigo","usr_nome")
                ->joinLeft(array("cid" => "cidade"), "cid.cid_codigo=med.cid_codigo", "cid_nome")
                ->join(array("proc" => "procedimento"), "proc.proc_codigo=coni.proc_codigo", array("proc_nome", "proc_codigo_sus"))
                ->join(array("usu"=>"usuario"),"age.usu_codigo=usu.usu_codigo","usu_nome")
                ->where("age.age_codigo=?", $age_codigo)
                ->order("proc_nome");

        if($coletados == 1){
            $where->join(array("col"=>"coleta"),"col.agei_codigo=agei.agei_codigo","col_data_entrega"); // apenas os coletados
        }else{
            $where->joinLeft(array("col"=>"coleta"),"col.agei_codigo=agei.agei_codigo","col_data_entrega");
        }
        //die($where->__toString());
        return $this->fetchAll($where);
	}

	/**
	 * Retorna quais as orientações para o agendamento informado
	 * @param int $age_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getOrientacoes($age_codigo) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->distinct()
				->from(array("age" => "agenda"), "")
				->join(array("agei" => "agenda_itens"), "agei.age_codigo=age.age_codigo", "")
				->join(array("coni" => "convenio_itens"), "coni.coni_codigo=agei.coni_codigo", "")
				->join(array("proo" => "procedimento_orientacoes"), "proo.proc_codigo=coni.proc_codigo", "")
				->join(array("ori" => "orientacoes_exames"), "ori.ori_exa_codigo=proo.ori_exa_codigo", "ori_exa_orientacoes")
				->where("age.age_codigo=?", $age_codigo)
				->group("ori_exa_orientacoes");

		return $this->fetchAll($where);
	}

	/**
	 * Valores padrão do insert/update
	 * @param array $data valores do insert
	 */
	private function valoresPadrao(&$data) {
		if (empty($data['age_data_insert'])) {
			$data['age_data_insert'] = date("Y-m-d H:i:s");
		}

		if (empty($data['usr_codigo'])) {
			$tbUsr = new Application_Model_Usuarios();
			$data['usr_codigo'] = $tbUsr->getUsrAtual()->usr_codigo; // pode gerar exception
		}
	}

	/**
	 * Retorna quantas vagas há entre as datas informadas
	 * @param array $coni_codigos lista de itens do convenio
	 */
	public function getVagas($coni_codigos, $data_inicial, $data_final) {
		$coni_codigos = (array) $coni_codigos;

		$tbConv = new Application_Model_Convenio();
		$tbConI = new Application_Model_ConvenioItens();
		$conv_codigo = $tbConI->find($coni_codigos[0])->current()->conv_codigo;

		// verificar se o local(L/H/U) atende aos sábados e domingos
		$sd = $tbConv->atendeSabadoEDomingo($conv_codigo);

		$tbFun = new Application_Model_Funcoes();
		$arrDatas = $tbFun->datasToArray($data_inicial, $data_final);
		$arrFinal = array();

		foreach ($coni_codigos as $coni_codigo) {
			$arrConi = array();
			$vagas = $this->vagas($coni_codigo, $data_inicial, $data_final);
			foreach ($vagas as $dia) {
				if ($dia->limite_dia < 0){
					$vagasRestantesDia = -1;
                } else {
					$vagasRestantesDia = $dia->limite_dia - $dia->agendado_dia;
                }

				if ($dia->limite_mes < 0){
					$vagasRestantesMes = -1;
                } else {
					$vagasRestantesMes = $dia->limite_mes - $dia->agendado_mes;
                }

				if (!$this->sabadoDomingoOuFeriado($dia->grad_dia, $sd)){
					$arrConi[$dia->grad_dia] = 0; // não podem haver vagas nos sábados*, domingos* e feriados
                } else {
                    $arrConi[$dia->grad_dia] = $this->vagasRestantes($vagasRestantesDia, $vagasRestantesMes);
                }
			}

			if (count($arrConi) < count($arrDatas)) {
				$tbGram = new Application_Model_GradeMes();
				$tbGrad = new Application_Model_GradeDia();

				foreach ($arrDatas as $dia) {
					if (!array_key_exists($dia, $arrConi)) {
						if (!$this->sabadoDomingoOuFeriado($dia, $sd)) {
							$arrConi[$dia] = 0; // não podem haver vagas nos sábados*, domingos* e feriados
						} else {
							$limite_dia = $tbGrad->getCotaDia($coni_codigo, $dia);
							$limite_mes = $tbGram->getCotaMes($coni_codigo, $dia);
							$arrConi[$dia] = min(array($limite_dia, $limite_mes));
						}
					}
				}
			}
            
            ksort($arrConi);
			$arrFinal [$coni_codigo] = $arrConi;
        }
        
        // var_dump($arrFinal);die;
		return $arrFinal;
	}

	/**
	 * Retornar quantas vagas há por dia
	 * @param int $coni_codigo
	 * @param date $data_inicial
	 * @param date $data_final
	 * @return array
	 */
	public function getTotalVagas($coni_codigo, $data_inicial, $data_final) {
		$arrConi = array();
		// Verifica quantos exames foram foram agendados por dia, quantas vagas há no mês, quantos foram agendados no mês,
        // o mês foi alterado? quanto pode por dia da tabela agenda_itens, grade_dia, grade_mes, nada do convenio itens
        $vagas = $this->vagas($coni_codigo, $data_inicial, $data_final);
        // Pega os dias e coloca um array
        foreach ($vagas as $dia) {
            $arrConi[$dia->grad_dia] = (object) $dia->toArray();
		}
        
        $tbFun = new Application_Model_Funcoes();
		// Monta um array de datas por meio da data inicial e a data final
        $arrDatas = $tbFun->datasToArray($data_inicial, $data_final);
        // Verfica se a quantidade de dias da grade é menor que os dias atuais
        if (count($arrConi) < count($arrDatas)) {
			$tbGram = new Application_Model_GradeMes();
			$tbGrad = new Application_Model_GradeDia();
            // Iniciando variaveis
			$aux = new stdClass();
			$aux->agendado_dia = 0;
			$aux->agendado_mes = 0;
            // Percorrendo array de datas
			foreach ($arrDatas as $dia) {
				// Verifica se dia não existe no array de grades
                if (!array_key_exists($dia, $arrConi)) {
                    $atendeQueDia = $tbFun->diaSemana($dia);
                    // Validação Laboratório ou Profissional do dia
                    if ($tbGrad->getCotaDiaManExcecao($coni_codigo,$dia,$atendeQueDia)->coni_cota_dia != "") {
                        $coni_cota_dia = $tbGrad->getCotaDiaManExcecao($coni_codigo,$dia,$atendeQueDia)->coni_cota_dia;
                        $agendado_dia = $tbGrad->getCotaDiaManExcecao($coni_codigo,$dia,$atendeQueDia)->agendado_dia;
                    } else {
                        $coni_cota_dia = $tbGrad->getCotaDiaManExcecao($coni_codigo,$dia,$atendeQueDia);
                    }
                    $dados = array(
                        "agendado_dia" => $agendado_dia,
                        "agendado_mes" => "",
                        "grad_dia" => $dia,
                        "coni_cota_mes" => $tbGram->getCotaMes($coni_codigo,$dia),
                        "limite_mes" => $tbGram->getCotaMes($coni_codigo,$dia),
                        "coni_cota_dia" => $coni_cota_dia,
                        "gram_alterada" => "",
                        "limite_dia" => $coni_cota_dia);
                    $arrConi[$dia] = (object)$dados;
                }

			}
            
            //Ordena um array pelas chaves em ordem inversa
            ksort($arrConi);
        }
        return $arrConi;
	}

	/**
	 * Retornar quantas vagas há por dia, por coni_codigo
	 * @param array $coni_codigos
	 * @param date $data_inicial
	 * @param date $data_final
	 * @return array
	 */
	public function getTotalVagasArr($coni_codigos, $data_inicial, $data_final) {
		$out = array();
        // Percorrendo o array de coni codigo, tanto de exame, quanto de profissional
        foreach ($coni_codigos as $coni_codigo){
            $out [$coni_codigo] = $this->getTotalVagas($coni_codigo, $data_inicial, $data_final);
        }
        
        return $out;
	}

	/**
	 * Verifica se ha um limitador no dia ou mes
	 * @param int $dia
	 * @param int $mes
	 */
	private function vagasRestantes($dia, $mes) {
		if ($dia < 0) {
			return $mes;
		} elseif ($dia == 0) {
			return 0;
		} else {
			if ($mes < 0) {
				return $dia;
            }

			return min(array($dia, $mes));
		}
	}

	/**
	 * Retorna quantas vagas foram usadas por dia, quantas há disponíveis por dia e quantas há disponível por mês
	 * @see SQL Anderson
	 * @see Zend_Db_Expr - http://stackoverflow.com/questions/6663473/zend-db-select-left-join-on-a-subselect
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function vagas($coni_codigo, $data_inicial=FALSE, $data_final=FALSE) {
		$sql1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agenda_itens"), "count(agei_codigo)") // quantos exames foram foram agendados por dia
				->where("agei_data=grad_dia")
				->where("age.coni_codigo=$coni_codigo");
		$sql1 = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("age"=>"agenda_itens"),"count(agei_codigo)")
                    ->where("coni_codigo =?",$coni_codigo)
                    ->where("agei_data=grad_dia");

		$sql2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gram" => "grade_mes"), array("gram_cota_mes")) // quantas vagas há no mês
				->where("to_char(gram_mes,'mm/yyyy') = to_char(grad_dia,'mm/yyyy')")
				->where("gram.coni_codigo=$coni_codigo");

		$sql3 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("age" => "agenda_itens"), "count(agei_codigo)") // quantos foram agendados no mês
				->where("to_char(agei_data,'mm/yyyy') = to_char(grad_dia,'mm/yyyy')")
				->where("age.coni_codigo=$coni_codigo");

		$sql4 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("gram" => "grade_mes"), array("gram_alterada")) // o mês foi alterado?
				->where("to_char(gram_mes,'mm/yyyy') = to_char(grad_dia,'mm/yyyy')")
				->where("gram.coni_codigo=$coni_codigo");

		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)		  // quanto pode por dia
				->from(array("grad" => "grade_dia"), array("grad_dia", "limite_dia" => "grad_cota_dia", "grad_alterada"))
				->columns(array(
					"agendado_dia" => new Zend_Db_Expr('(' . $sql1->assemble() . ')'),
					"limite_mes" => new Zend_Db_Expr('(' . $sql2->assemble() . ')'),
					"agendado_mes" => new Zend_Db_Expr('(' . $sql3->assemble() . ')'),
					"gram_alterada" => new Zend_Db_Expr('(' . $sql4->assemble() . ')')
				))
				->join(array("coni" => "convenio_itens"), "coni.coni_codigo=grad.coni_codigo", array("coni_cota_mes", "coni_cota_dia"))
				->where("coni.coni_codigo=?", $coni_codigo)
				->order("grad_dia");

		if ($data_inicial)
			$where->where("grad_dia >= '$data_inicial'");

		if ($data_final)
			$where->where("grad_dia <= '$data_final'");
//die($where);
                return $this->fetchAll($where);
	}

        /**
	 * Informa a data final, para montar o grid de agendamento
	 * Também valida a data inicial, impedindo data passada
	 * @param date $data_inicial
	 * @param bool $fimDoMes Verdadeiro para retornar o ultimo dia do mês, falso para retornar N* dias (N = configuração)
	 * @return date
	 */
	public function calculaDataFinal($data_inicial, $fimDoMes=FALSE) {
		if ($fimDoMes) {
			list($y, $m, $d) = explode("-", $data_inicial);
			$mk = mktime(0, 0, 0, $m, $d, $y);
			return "$y-$m-" . date("t", $mk);
		}

		$tbConf = new Application_Model_Configuracao();
		$dias = $tbConf->getConfig('AGENDA_MOSTRAR_N_OPCOES');
		$dtRetro = $tbConf->getDadosConfigPelaChave('AGENDA_MOSTRAR_N_OPCOES')->conf_valor_int;
		list($y, $m, $d) = explode("-", $data_inicial);
                if(empty($dtRetro)) {
                       if ((int) "$y$m$d" < (int) date("Ymd")) {
                               $data_inicial = date("Y-m-d");
                               list($y, $m, $d) = explode("-", $data_inicial);
                       }
                }

//exit;
		$mk = mktime(0, 0, 0, $m, $d + $dias - 1, $y);
		return date("Y-m-d", $mk);
	}

	/**
	 * Diz se pode haver atendimento nesse dia
	 * @param date $data
	 * @param stdClass $sd se o local atende no sábado e domingo
	 * @see Application_Model_Convenio::atendeSabadoEDomingo
	 */
	private function sabadoDomingoOuFeriado($data, $sd) {
		list($y, $m, $d) = explode("-", $data);
		$mk = mktime(0, 0, 0, $m, $d, $y);

		// se não pode fazer no sábado:
		if (!$sd->sabado && date("w", $mk) == 6) {
			return FALSE;
		}

		// se não pode fazer no domingo:
		if (!$sd->domingo && date("w", $mk) == 0) {
			return FALSE;
		}

		$tbFer = new Application_Model_Feriado();
		if ($tbFer->ehFeriado($data)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Retorna o agendamentos de exame do paciente
	 * Atenção: local do exame: somente med_codigo
	 * @param int $usu_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getHistoricoDeExames($usu_codigo,$data_inicial=FALSE, $data_final=FALSE) {
			$tbConf = new Application_Model_Configuracao();
			$regra = $tbConf->getDadosConfigPelaChave('HISTORICO_EXAME_SISTCOM')->conf_valor_bool;

//die("asdfasdf".$regra);

                $where1 = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->from(array("age"=>"agenda"),array("age_codigo","age_data_insert","ate_codigo"))
                              ->join(array("agei"=>"agenda_itens"),"age.age_codigo=agei.age_codigo",array("agei_codigo","agei_data","agei_status"))
                              ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo = age.usr_codigo_medico",array("usr_nome"))
                              ->join(array("coni"=>"convenio_itens"),"agei.coni_codigo=coni.coni_codigo",array())
                              ->join(array("conv"=>"convenio"),"conv.conv_codigo = coni.conv_codigo",array("conv_codigo"))
                              ->join(array("med"=>"medico"), "med.med_codigo=conv.med_codigo", array("med_nome"))
                              ->join(array("proc" => "procedimento"), "proc.proc_codigo=coni.proc_codigo", array("proc_nome"))
                              ->join(array("usu" => "usuario"),"usu.usu_codigo = age.usu_codigo",array("usu_nome"))
                              ->where("age.usu_codigo=?",$usu_codigo);
                if($data_inicial)
			$where1->where ("agei_data >= ?", $data_inicial);

		if($data_final)
			$where1->where ("agei_data <= ?", $data_final);


 $where2 = $this->select(FALSE)
 	->setIntegrityCheck(FALSE)
 	->from(array("cli"=>"cli_agenda"),array("cli.id_cli_agenda_local as age_codigo","cli.data as age_data_insert","cli.id_cli_medicos as ate_codigo","cli.id_cli_agenda_local_vagas as agei_codigo","cli.data as agei_data","cli.flag_compareceu as agei_status","cli.observacao as usr_nome","cli.id_cli_agenda_local_vagas as conv_codigo","cli.observacao as med_nome"))
 	->join(array("med"=>"cli_medicos"),"med.id_cli_medicos = cli.id_cli_medicos",array("med.nome as proc_nome"))
 	->join(array("usu"=>"usuario"),"usu.usu_codigo = cli.id_cli_clientes",array("usu.usu_nome as usu_nome"))
 	->where("cli.id_cli_clientes=?",$usu_codigo);
// 	->where("med.cbos = '2253'");

      $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where1,$where2), Zend_Db_Select::SQL_UNION_ALL);
              // die("aaaaaaaaaaaaaa".$where);

		return $this->fetchAll($where);
	}

        public function getDadosUsuarioPorAgendamento($age_codigo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("a"=>"agenda"))
                          ->join(array("u"=>"usuario"),"u.usu_codigo=a.usu_codigo",array("usu_nome","usu_datanasc","usu_mae","usu_sexo","usu_celular"))
                          ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=u.dom_codigo",array("dom_numero","dom_telefone"))
                          ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo","rua_nome")
                          ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=a.usr_codigo_medico","usr_nome")
                          ->joinLeft(array("med"=>"medico"),"med.med_codigo=a.med_codigo","med_nome")
                          ->where("a.age_codigo=$age_codigo");
            return $this->fetchRow($where);
        }


        public function atualizarUsu($de, $para){
            $de = (array)$de;

            $data = array("usu_codigo" => $para);
            $where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

            return $this->update($data, $where);
    }

        public function relPaciente($usu_codigo=FALSE,$data_inicial=FALSE, $data_final=FALSE, $solicitante=FALSE,$interno=FALSE) {
                $where = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->distinct()
                              ->from(array("age"=>"agenda"),array("age_codigo","age_data_insert","ate_codigo","usu_codigo"))
                              ->join(array("agei"=>"agenda_itens"),"age.age_codigo=agei.age_codigo","")
                              ->join(array("coni"=>"convenio_itens"),"coni.coni_codigo=agei.coni_codigo","")
                              ->join(array("proc"=>"procedimento"),"proc.proc_codigo=coni.proc_codigo","")
                              ->join(array("col" => "coleta"),"col.agei_codigo=agei.agei_codigo","")
                              ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo = age.usr_codigo_medico",array("usr_nome"))
                              ->joinLeft(array("med"=>"medico"), "med.med_codigo=age.med_codigo", array("med_nome"))
                              ->join(array("usu" => "usuario"),"usu.usu_codigo = age.usu_codigo",array("usu_nome","usu_datanasc","usu_sexo","usu_prontuario"))
                              ->order("usu.usu_nome");

                if($data_inicial)
                    $where->where ("agei_data >= ?", $data_inicial);

		if($data_final)
                    $where->where ("agei_data <= ?", $data_final);

                if($usu_codigo)
                    $where->where("age.usu_codigo=?",$usu_codigo);

                 if($interno == 1)
                    $where->where("age.usr_codigo_medico = $solicitante");
                 else if($interno == 0 && $solicitante != "")
                     $where->where("age.med_codigo = $solicitante");
		//die($where);
		return $this->fetchAll($where);
	}

        public function relProcedimento($usu_codigo=FALSE,$data_inicial=FALSE, $data_final=FALSE,$proc_codigo=FALSE,$med_codigo=false) {
                $where = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->distinct()
                              ->from(array("age"=>"agenda"),array(""))
                              ->join(array("agei"=>"agenda_itens"),"age.age_codigo=agei.age_codigo","")
                              ->join(array("col"=>"coleta"),"col.agei_codigo=agei.agei_codigo","")
                              ->join(array("coni" => "convenio_itens"),"agei.coni_codigo=coni.coni_codigo","")
                              ->join(array("conv" => "convenio"),"conv.conv_codigo=coni.conv_codigo","")
                              ->join(array("proc" => "procedimento"),"proc.proc_codigo=coni.proc_codigo",array("proc_codigo","proc_nome","proc_codigo_sus","count(*) as qtde"))
                              ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo = age.usr_codigo_medico",array(""))
                              ->joinLeft(array("med"=>"medico"), "med.med_codigo=age.med_codigo", array(""))
                              ->order("proc_nome")
                              ->group(array("proc.proc_codigo","proc_nome","proc_codigo_sus"));

                if($data_inicial)
                    $where->where ("agei_data >= ?", $data_inicial);

				if($data_final)
                    $where->where ("agei_data <= ?", $data_final);

                if($usu_codigo)
                    $where->where("age.usu_codigo=?",$usu_codigo);

                if($proc_codigo)
                    $where->where("coni.proc_codigo=$proc_codigo");

                if($med_codigo)
                    $where->where("conv.med_codigo=$med_codigo");

                //
		return $this->fetchAll($where);
	}


        public function relLabProc($age_codigo) {
                $where = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->from(array("age"=>"agenda"),"")
                              ->join(array("agei"=>"agenda_itens"),"age.age_codigo=agei.age_codigo","")
                              ->join(array("coni"=>"convenio_itens"),"agei.coni_codigo=coni.coni_codigo","")
                              ->join(array("proc" => "procedimento"), "proc.proc_codigo=coni.proc_codigo", array("proc_nome","proc_codigo","proc_codigo_sus",))
                              ->order("proc_nome")
                              ->where("age.age_codigo=$age_codigo");
                //die($where);
		return $this->fetchAll($where);
	}

        public function relProcPac($age_codigo=FALSE,$data_inicial=FALSE, $data_final=FALSE) {
                $where = $this->select(FALSE)
                                ->setIntegrityCheck(FALSE)
                                ->distinct()
                                ->from(array("age"=>"agenda"),array("age_codigo","age_data_insert","ate_codigo","usu_codigo"))
                                ->join(array("agei"=>"agenda_itens"),"age.age_codigo=agei.age_codigo","")
                                ->join(array("col"=>"coleta"),"col.agei_codigo=agei.agei_codigo","col_data_coleta")
                                ->join(array("coni"=>"convenio_itens"),"agei.coni_codigo=coni.coni_codigo","")
                                ->join(array("proc" => "procedimento"),"proc.proc_codigo=coni.proc_codigo",array("proc_codigo","proc_nome"))
                                ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo = age.usr_codigo_medico",array("usr_nome"))
                                ->joinLeft(array("med"=>"medico"), "med.med_codigo=age.med_codigo", array("med_nome"))
                                ->join(array("usu" => "usuario"),"usu.usu_codigo = age.usu_codigo",array("usu_nome","usu_datanasc","usu_sexo","usu_prontuario","usu_mae"))
                                ->where("age.age_codigo in ($age_codigo)")
                                ->order("usu_nome");

                if($data_inicial)
                    $where->where ("agei_data >= ?", $data_inicial);

                if($data_final)
                    $where->where ("agei_data <= ?", $data_final);

               //die($where)."<br/>";
                return $this->fetchAll($where);
	}

	public function updateStatusColeta($age_codigo = FALSE){
			$where = $this->update(array('age_status'=>'R'))
									->where("age_codigo=$age_codigo");
			return $this->fetchRow($where);


	}

	public function getAgendados($uni_codigo=FALSE, $data_inicial=FALSE, $data_final=FALSE, $proc_sus=FALSE, $proc_codigo=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("age"=>"agenda"),array("age_codigo", "age_data_insert"))
                    ->joinLeft(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo", array("usu.usu_nome"))
                    ->joinLeft(array("agei"=>"agenda_itens"), "agei.age_codigo=age.age_codigo", array("agei.agei_valor"))
                    ->joinLeft(array("coni"=>"convenio_itens"), "coni.coni_codigo=agei.coni_codigo", "")
                    ->joinLeft(array("proc"=>"procedimento"), "proc.proc_codigo=coni.proc_codigo", array("proc.proc_nome", "proc.proc_codigo"))
                    ->joinLeft(array("conv"=>"convenio"),"conv.conv_codigo=coni.conv_codigo", "")
					->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo=conv.uni_codigo", array("uni_desc"))
                    // ->group("age.age_data")
                    // ->group("proc.proc_nome")
                    // ->group("proc.proc_codigo")
                    // ->group("age.age_codigo");
                    ->order("age.age_data_insert");
                if($uni_codigo){
                    $sql->where("uni.uni_codigo=?",$uni_codigo);
                }
                if($data_inicial){
                    $sql->where("age.age_data_insert>=?",$data_inicial);
                }
                if($data_final){
                    $sql->where("age.age_data_insert<=?",$data_final);
                }
                if($proc_sus == "S" || $proc_sus == "N"){
                    $sql->where("proc.proc_sus ilike ?",$proc_sus);
                }
                if($proc_codigo){
                    $sql->where("proc.proc_codigo =?",$proc_codigo);
                }
                //die($sql);
        return $this->fetchAll($sql);
    }

	public function getPacientesAgendadosPorPeriodo($uni_codigo=FALSE, $dataInicial=FALSE, $dataFinal=FALSE, $proc_codigo=FALSE){
			$sql = $this->select(FALSE)
						->distinct()
						->setIntegrityCheck(FALSE)
						->from(array("age"=>"agenda"), "")
						->joinLeft(array("usu"=>"usuario"), "age.usu_codigo=usu.usu_codigo", array("usu.usu_codigo","usu.usu_nome"))
						->joinLeft(array("agei"=>"agenda_itens"), "agei.age_codigo=age.age_codigo","")
						->joinLeft(array("coni"=>"convenio_itens"), "coni.coni_codigo=agei.coni_codigo", "")
						->joinLeft(array("proc"=>"procedimento"), "proc.proc_codigo=coni.proc_codigo", "")
						->joinLeft(array("conv"=>"convenio"),"conv.conv_codigo=coni.conv_codigo", "")
						->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo=conv.uni_codigo", "");
						if($dataInicial){
                    		$sql->where("age.age_data_insert >= ?", $dataInicial);
						}
                		if($dataFinal){
                    		$sql->where("age.age_data_insert <= ?", $dataFinal);
						}
						if($uni_codigo){
                    		$sql->where("uni.uni_codigo=?",$uni_codigo);
						}
						if($proc_codigo){
		                    $sql->where("proc.proc_codigo =?",$proc_codigo);
		                }
						//die($sql);
			return $this->fetchAll($sql);
	}

}
