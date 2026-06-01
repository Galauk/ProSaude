<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Convenio extends Elotech_Db_Table_Abstract {

	protected $_name = 'convenio';
	protected $_primary = 'conv_codigo';
        //protected $_schema = 'aise';
        
        /* -----------------------------------------------------------------
         * MÉTODOS CONVÊNIOS AGENDAMENTO ESTABELECIMENTO DE SAÚDE
         * ----------------------------------------------------------------*/
        
        // Busca Estabelecimento de Saúde(Unidade) de acordo com o autocomplete
        public function buscarEstabelecimentosDeSaude($term){
            $sql = $this->select(FALSE)
                         ->setIntegrityCheck(FALSE)
                         ->from(array("uni" => "unidade"), array("uni_codigo as codigo_convenio", "nome_convenio" => "uni_desc", "prestador_servico" => "('U')", "categoria" => "('Unidade')")) // prestador_servico: U
                         ->where("uni_desc ilike '%$term%'");
            $all = $this->fetchAll($sql);
            return $this->constroiArrayBuscaAutocomplete($all);
        }
            // Realiza a busca dos dados para edição
    public function buscaDados($conv_codigo)
    {
        $where = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("conv" => "convenio"))
            ->join(array("med" => "medico"), "med.med_codigo=conv.med_codigo", array("med_nome", "med_codigo"))
            ->where("conv_codigo = $conv_codigo");
        return $this->fetchRow($where);
    }

        // Salva um novo vinculo de Estabelecimento(Unidade) com o Agendamento(Convênio)
        public function salvarVinculoAgendamentoEstabelecimentoDeSaude($dados){
            if ($this->fetchAll("conv_status = 't' AND uni_codigo = ".$dados['uni_codigo'])->count()){
                    throw new Zend_Validate_Exception("Estabelecimento de Saúde já possui um cadastrado!");			
            } else {
                $this->emptyToUnset($dados);
                return parent::salvar($dados);
            }
        }
        
        // Desativa o Estabelecimento(Convênio), se não tiver agendamento programado
        public function excluirVinculoAgendamentoEstabelecimentoDeSaude($convCodigo,$uniCodigo){
                $item = $this->fetchRow("conv_codigo=$convCodigo");
                if ($item) {
                    $item->conv_status = "F";
                    $item->save();
                }
        }
        
        // Pega os dados de um estabelecimento de saúde conveniado
        public function getDadosAgendamentoEstabelecimentoDeSaude($conv_codigo){
            $sql = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("conv" => "convenio"), "conv_codigo")
				->join(array("uni" => "unidade"), "uni.uni_codigo=conv.uni_codigo", array("uni_codigo as codigo_convenio", "uni_desc as nome_convenio", "prestador_servico" => "('U')", "categoria" => "('Unidade')"))
				->where("conv_status='t'");
            if ($conv_codigo)
                    $sql->where("conv_codigo = $conv_codigo");
            //die($sql);
            return $this->fetchRow($sql);
        }
        
        // Retorna número de agendamento para os proximos dias no Estabelecimento(Unidade)
        public function getNumAgendamentoEstabelecimentoDeSaude($uniCodigo){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("age"=>"agendamento"),array("COUNT(age_codigo) as numAge"))
                        ->where("uni_codigo =? ",$uniCodigo)
                        ->where("age_atendido = 'N'");
            return $this->fetchRow($sql);
        }
        
        public function getDadosConvAgendamentoEstabelecimentoDeSaude($uniCod,$usrCod,$espCod){
            $sql = $this->select(FALSE)
                        ->distinct()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("conv"=>"convenio"),array("conv.uni_codigo","conv_codigo"))
                        ->join(array("condi"=>"convenio_itens"),"conv.conv_codigo=condi.conv_codigo",array("coni_codigo","usr_codigo","esp_codigo"))
                        ->join(array("convd"=>"convenio_dias_semana_agendamento"),"condi.coni_codigo=convd.coni_codigo","")
                        ->join(array("convh"=>"convenio_horarios"),"condi.coni_codigo=convh.coni_codigo","")
                        ->where("conv.uni_codigo =?",$uniCod)
                        ->where("condi.usr_codigo =?",$usrCod)
                        ->where("condi.esp_codigo =?",$espCod);
            return $this->fetchRow($sql);
        }
        
        // Pesquisa o estabelecimento pelo nome 
        public function pesquisaAgendamentoEstabelecimentosDeSaude($dados){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("conv"=>"convenio"),array("conv_codigo","conv_sabado","conv_domingo"))
                        ->join(array("uni"=>"unidade"),"uni.uni_codigo=conv.uni_codigo",array("uni_desc as convenio","uni_codigo"))
                        ->where("conv_status = 'T'")
                        ->where("uni_desc ilike '%$dados%'")
                        ->order("convenio ASC");
            return $this->fetchAll($sql);   
        }
        
        /* -----------------------------------------------------------------
         * MÉTODOS CONVÊNIOS
         * ----------------------------------------------------------------*/
        
        // Realiza a busca por convênios
        public function pesquisar($dados=FALSE, $limit=FALSE) {
        $where = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("conv" => "convenio"), array("conv_codigo", "conv_sabado", "conv_domingo", "conv_status", "tipo_convenio"))
            ->join(array("med" => "medico"), "med.med_codigo=conv.med_codigo", array("med_nome as convenio", "med_codigo", "prestador_servico as tipo_prestador"))
            //->where("conv_status = 't'")
            ->where("med_nome ilike '%$dados%'")
            ->order(array("conv_status DESC", "med_nome ASC"));
        //if ($limit) { $where->limit(15); }
        return $this->fetchAll($where);
	}
        
        // Pesquisa pelos Laboratórios cadastrado como prestador de serviço na tabela Médico
        public function buscarConvenios($term){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("med" => "medico"), array("med_codigo as codigo_convenio", "med_nome as nome_convenio", "prestador_servico", "categoria" => "(CASE WHEN prestador_servico='H' THEN 'Hospital' WHEN prestador_servico='L' THEN 'Laboratório' ELSE 'Médico' END)"))
                        ->where("prestador_servico IN (?)", array(Application_Model_Medico::LABORATORIO, Application_Model_Medico::HOSPITAL))
                        ->where("med_nome ilike '%$term%'");
            $all = $this->fetchAll($sql);
            return $this->constroiArrayBuscaAutocomplete($all);
        }
        
        // Salva um novo vinculo de Estabelecimento(Unidade) com o Agendamento(Convênio)
        public function salvarConvenio($dados){
            if ($this->fetchAll("conv_status = 't' AND med_codigo = ".$dados['med_codigo'])->count()){
                throw new Zend_Validate_Exception("Estabelecimento de Saúde já possui um cadastrado!");			
            } else {
                $this->emptyToUnset($dados);
                return parent::salvar($dados);
            }
        }
        
        // Desabilita o convênio
        public function excluir($conv_codigo) {
            $item = $this->fetchRow("conv_codigo=$conv_codigo");
            if ($item) {
                    $item->conv_status = "F";
                    $item->save();
            }
            return true;
	}
        
        // Pega quantidade de agendamentos a ser realizado
        public function getNumConvAgendados($medCodigo){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("age"=>"agenda"),array("COUNT(age_codigo) AS numConvAgendado"))
                        ->where("age_status = 'A'")
                        ->where("med_codigo =?",$medCodigo);
            return $this->fetchRow($sql);
        }
        
        /* -----------------------------------------------------------------
         * MÉTODOS GERAIS UTIL PARA CONVÊNIO DE LABORATORIO OU AGENDAMENTO
         * ----------------------------------------------------------------*/
        
        public function constroiArrayBuscaAutocomplete($all){
            $out = array();
            foreach ($all as $med) {
                    $out [] = array(
                            "id" => $med->codigo_convenio,
                            "label" => $med->nome_convenio,
                            "data" => $med->toArray()
                    );
            }
            if (!count($out)) {
                $out [] = array(
                        "id" => 0,
                        "label" => "Nenhum item encontrado",
                        "data" => array("categoria" => "Nenhum item encontrado")
                );
            }
            return $out;
        }
        
        // Diz se o convênio atende no sabado ou domingo
	public function atendeSabadoEDomingo($conv_codigo) {
            $conv = $this->find($conv_codigo)->current();
            $retorno = new stdClass();
            $retorno->sabado = $conv->conv_sabado;
            $retorno->domingo = $conv->conv_domingo;
            return $retorno;
	}
        
        /* -----------------------------------------------------------------
        * OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
        * ----------------------------------------------------------------*/
        
        public function salvar(array $data) {
           //echo "<pre>".print_r($data,1);exit;
		if (empty($data['conv_status']))
			$data['conv_status'] = 't';

		if (empty($data['conv_codigo']))
			$this->peloMenosUm(array("med_codigo","uni_codigo"), $data);
		else if (!empty($data['tipo'])) {
			$this->atualizaSabadoDomingo($data);
		}
		
		if(!empty ($data['med_codigo'])){
			if ($this->fetchAll("conv_status = 't' AND med_codigo = ".$data['med_codigo'])->count()){
				throw new Zend_Validate_Exception("Já existe um convênio para o local cadastrado.");			
			}
		}
                if(!empty ($data['uni_codigo'])){
			if ($this->fetchAll("conv_status = 't' AND uni_codigo = ".$data['uni_codigo'])->count()){
				throw new Zend_Validate_Exception("Já existe um convênio para o local cadastrado.");			
			}
		}
                //echo "<pre>".print_r($data,1);exit;
		$this->emptyToUnset($data);
		return parent::salvar($data);
	}

	private function atualizaSabadoDomingo(&$data) {
		if ($data['tipo'] == 'sabado') {
			$data['conv_sabado'] = $data['to'];
		} else {
			$data['conv_domingo'] = $data['to'];
		}
		unset($data['to'], $data['tipo']);
	}

    public function getMaxDia(){
        $where = $this->getDefaultAdapter()->query()->fetch();
    }

	/**
	 * Busca genérica
	 * @param string $term filtro
	 */
	public function buscar($term, $limite=FALSE, $somenteConveniados=TRUE) {
                
		if ($somenteConveniados) {
			$where = $this->buscarConveniado($term);
		} else {
			$where = $this->buscarTodos($term);
		}

		if ($limite)
			$where->limit($limite);

		//die($where->__toString());
		$all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $med) {
			$out [] = array(
				"id" => $med->codigo_convenio,
				"label" => $med->nome_convenio,
				"data" => $med->toArray()
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("categoria" => "Nenhum item encontrado")
			);
		}

		return $out;
	}

	private function buscarTodos($term) {
		$sql1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("med" => "medico"), array("med_codigo as codigo_convenio", "med_nome as nome_convenio", "prestador_servico", "categoria" => "(CASE WHEN prestador_servico='H' THEN 'Hospital' WHEN prestador_servico='L' THEN 'Laboratório' ELSE 'Médico' END)"))
				->where("prestador_servico IN (?)", array(Application_Model_Medico::LABORATORIO, Application_Model_Medico::HOSPITAL))
				->where("med_nome ilike '%$term%'");

		$sql2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("uni" => "unidade"), array("uni_codigo as codigo_convenio", "nome_convenio" => "uni_desc", "prestador_servico" => "('U')", "categoria" => "('Unidade')")) // prestador_servico: U
				->where("uni_desc ilike '%$term%'");

		$where = $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->union(array($sql1, $sql2), Zend_Db_Select::SQL_UNION_ALL)
						->order(array("prestador_servico", "nome_convenio"));
                
               // die($where);
                return $where;
	}

	private function buscarConveniado($term=FALSE, $conv_codigo=FALSE) {
		$sql1 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("conv" => "convenio"), "conv_codigo")
				->join(array("med" => "medico"), "med.med_codigo=conv.med_codigo", array("med_codigo as codigo_convenio", "med_nome as nome_convenio", "prestador_servico", "categoria" => "(CASE WHEN prestador_servico='H' THEN 'Hospital' WHEN prestador_servico='L' THEN 'Laboratório' ELSE 'Médico' END)"))
				->where("conv_status = 't'");

		if ($term)
			$sql1->where("med_nome ilike '%$term%'");

		if ($conv_codigo)
			$sql1->where("conv_codigo = $conv_codigo");


		$sql2 = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("conv" => "convenio"), "conv_codigo")
				->join(array("uni" => "unidade"), "uni.uni_codigo=conv.uni_codigo", array("uni_codigo as codigo_convenio", "uni_desc as nome_convenio", "prestador_servico" => "('U')", "categoria" => "('Unidade')"))
				->where("conv_status='t'"); // prestador_servico: U
		if ($term)
			$sql2->where("uni_desc ilike '%$term%'");

		if ($conv_codigo)
			$sql2->where("conv_codigo = $conv_codigo");


	//die($sql1.$sql2);
		return $this->select(FALSE)
						->setIntegrityCheck(FALSE)
						->union(array($sql1, $sql2), Zend_Db_Select::SQL_UNION_ALL)
						->order(array("prestador_servico", "nome_convenio"));
	}

	public function buscarPeloConv($conv_codigo) {
		$where = $this->buscarConveniado(FALSE, $conv_codigo);
                return $this->fetchRow($where);
	}

	protected function selectTag($where, $texto, $value=NULL, $first=NULL, $tag=TRUE, $name=NULL, $id=NULL, $foco=FALSE) {
		if (!$value)
			$value = current($this->_primary);

		if (!$name)
			$name = $value;

		if (!$id)
			$id = $name;

		$all = $this->fetchAll($where);
		$out = "";
		if ($tag)
			$out = "<select name=\"$name\" id=\"$id\"" . ($foco ? " class=\"focus\"" : "") . ">\n";

		if ($first) {
			if (is_array($first))
				$out .= "\t<option value=\"" . $first[0] . "\">" . $first[1] . "</option>\n";
			else
				$out .= "\t<option value=\"0\">-- Selecione --</option>\n";
		}

		foreach ($all as $option) {
			$out .= "\t<option value=\"" . $option->$value . "\">" . trim($option->$texto) . "</option>\n";
		}

		if ($tag)
			$out .= "</select>\n";

		return $out;
	}

	public function getUnidadePorConvenio($uni_codigo){
            $sql = $this->select()
                        ->from(array("conv"=>"convenio"))
                        ->setIntegrityCheck(FALSE)
                        ->join(array("uni"=>"unidade"), "uni.uni_codigo=conv.uni_codigo",array("uni_codigo","uni_desc"))
                        ->where("conv.uni_codigo=?",$uni_codigo);
            return $this->fetchRow($sql);
        }
        
    public function getConvenioPorAgendamento($age_codigo=FALSE){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("a"=>"agenda"),"")
                        ->join(array("ai"=>"agenda_itens"),"a.age_codigo=ai.age_codigo","")
                        ->join(array("ci"=>"convenio_itens"),"ci.coni_codigo=ai.coni_codigo","")
                        ->join(array("c"=>"convenio"),"c.conv_codigo=ci.conv_codigo","conv_codigo")
                        ->join(array("m"=>"medico"),"m.med_codigo=c.med_codigo",array("med_codigo","med_nome","med_endereco","med_end_numero","med_end_bairro","med_end_cep","med_end_telefone","med_cnpj"))
                        ->where("a.age_codigo=$age_codigo");
        
        return $this->fetchRow($where);
    }
    
    public function getConvenioPorAgendamentoImprimir($age_codigo=FALSE){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("a"=>"agenda"),"turno")
                ->join(array("ai"=>"agenda_itens"),"a.age_codigo=ai.age_codigo","")
                ->join(array("ci"=>"convenio_itens"),"ci.coni_codigo=ai.coni_codigo","")
                ->joinLeft(array("conv"=>"convenio"),"conv.conv_codigo=ci.conv_codigo", array("conv_codigo", "max_dia_manha_horario", "max_dia_tarde_horario"))
                ->join(array("m"=>"medico"),"m.med_codigo=conv.med_codigo",array("med_codigo","med_nome","med_endereco","med_end_numero","med_end_bairro","med_end_cep","med_end_telefone","med_cnpj"))
                ->where("a.age_codigo=$age_codigo");
        // die($where);
        return $this->fetchRow($where);
    }

}

