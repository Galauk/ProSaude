<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_GradeDia extends Elotech_Db_Table_Abstract {

    protected $_name = 'grade_dia';
    protected $_primary = 'grad_codigo';

    public function salvar(array $data) {
		$this->notEmpty(array("coni_codigo","grad_cota_dia","grad_dia"), $data);
        return parent::salvar($data);
    }
	
	/**
	 * Salva uma lista de datas alteradas (distribuição)
	 * @param array $data dados do post,
	 * @param array $original dados originais
	 * @return int quantos registros foram inseridos/atualizados
	 */
	public function salvarDoArray($data, $original){
		$atualizados = 0;
		foreach($data as $coni_codigo => $dias){
			foreach($dias as $dia => $vagas){
				// se não houver alteração, pular
				if($vagas == $original[$coni_codigo][$dia])
					continue;
				
				$dados = array(
					"coni_codigo" => $coni_codigo,
					"grad_dia" => $dia,
					"grad_codigo" => $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$dia'")->grad_codigo,
					"grad_cota_dia" => ($vagas==""?-1:$vagas)
				);
				
				$this->salvar($dados);
				$atualizados++;
			}
		}
		
		return $atualizados;
	}
	
	/**
	 * Retorna quantos vagas foram liberadas para o dia informado.
	 * Procura por exceção no dia, se não houver, retornará o modelo.
	 * Obs.: não cria a exceção padrão
	 * @param int $coni_codigo
	 * @param string $data formato 2012-05-02
	 * @return int quantas vagas há no dia informado 
	 */
	public function getCotaDia($coni_codigo,$data){
		$grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$data'");
		
		if($grad){
			return $grad->grad_cota_dia;
		} else {
			$tbConi = new Application_Model_ConvenioItens();			
			return $tbConi->find($coni_codigo)->current()->coni_cota_dia;
		}
	}
        
        public function getCotaDiaManExcecao($coni_codigo,$data,$atendeQueDia){
		$grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$data'");
		if($grad){
                    return $grad->grad_cota_dia;
		} else {
		    $tbConi = new Application_Model_ConvenioItens();			
            	    $coni_cota_dia_lab = $tbConi->find($coni_codigo)->current()->coni_cota_dia;
                    // Se não retornar a cota do dia para o exame ou laboratório, busca do profissional
                    if ($coni_cota_dia_lab == 0) {
                        $tbConvDias = new Application_Model_ConvenioDiasSemanaAgendamento();
                        return $tbConvDias->getDadosVagaDia($coni_codigo,$data,$atendeQueDia);
                    } else {
                        return $coni_cota_dia_lab;
                    }
                    
		}
	}
        
       public function getVagasDia($coni_codigo=FALSE,$data=FALSE,$atendeQueDia =FALSE){
            // Consulta na tabela grade dia se existe vagas para aquela data
            $where = $this->select()
                          ->from(array("grad"=>"grade_dia"),
                                 array("((select grad_cota_dia
                                            from grade_dia 
                                           where coni_codigo = $coni_codigo
                                             and grad_dia = '$data') - 
                                         (select count(age_codigo) 
                                            from agendamento 
                                           where coni_codigo = $coni_codigo
                                             and age_data = '$data')) as cota"));
            //die($where); 
            $vagas = $this->fetchRow($where);
             // Se existir vagas joga o nÃºmero de vagas para inserÃ§Ã£o na data 
             if(count($vagas->cota)){
                 return $vagas->cota;
             }else{
                 // Chama FunÃ§Ã£o que pega o nÃºmero de vagas menos o nÃºmero de agendamento e retorna o nÃºmero de vagas disponivel
                 $tbConi = new Application_Model_ConvenioItens();
                 return $tbConi->getVagas($coni_codigo,$data,$atendeQueDia)->cota;
				// die("RALOUU");
             }
        }


	/**
	 * Copia o modelo (convenio_itens) para a tabela grade_dia somente se NÃO houver uma exceção criada
	 * Obs.: cria para todos os dias do mês
	 * @param int $coni_codigo
	 * @param date $dia
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function criarCotaFromModelo($coni_codigo, $data) {	
		list($y,$m,$d) = explode("-",$data);
		$mk = mktime(0, 0, 0, $m, $d, $y);
		
		// primeiro dia do mes:
		$primeiro = "$y-$m-01";
		$ultimo = "$y-$m-".date("t",$mk);
		
		$tbFun = new Application_Model_Funcoes();
		$arrDias = $tbFun->datasToArray($primeiro, $ultimo);

		$tbConi = new Application_Model_ConvenioItens();
		$coni = $tbConi->find($coni_codigo)->current();
		
		foreach($arrDias as $dia){
			$grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$dia'");
			if($grad)
				return $grad;

			$dados = array(
				"coni_codigo" => $coni_codigo,
				"grad_cota_dia" => $coni->coni_cota_dia,
				"grad_dia" => $dia,
				"grad_valor" => $coni->coni_valor // valor do procedimento
			);

			$grad_codigo = $this->salvar($dados);
			
			if($dia == $data)
				$grad_codigo_final = $grad_codigo;
		}
		
		return $this->find($grad_codigo_final)->current();
	}
	
	/**
	 * Atualiza as cotas disponível nas exceções que não tiveram alterações
	 * @param int $coni_codigo
	 * @param int $cota 
	 */
	public function atualizarCota($coni_codigo, $cota){
		$tbAge = new Application_Model_Agenda();
		$vagas = $tbAge->vagas($coni_codigo, date("Y-m-d"));
		
		foreach($vagas as $vaga){
			// Somente se o limite desse dia, for igual ao modelo do dia
			if(!$vaga->grad_alterada){
				$dia = $vaga->grad_dia;
				
				if($cota >= 0 && $cota < $vaga->agendado_dia) // foi alterado para uma quantidade menor que as vagas já distribuidas para o dia
					$cotaNova = $vaga->agendado_dia;						
				else
					$cotaNova = $cota;

				// atualizar a cota desse mês
				$this->alterarCota($dia, $coni_codigo, $cotaNova);							
			}
		}	
		
		
	}
	
	/**
	 * Altera o valor da cota
	 * Obs.: não valida nada
	 * @param date $dia
	 * @param int $coni_codigo
	 * @param int $cota 
	 */
	private function alterarCota($dia, $coni_codigo, $cota){
		$grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$dia'");
		$grad->grad_cota_dia = $cota;
		$grad->save();
	}
        
        
        
        public function getIntervaloDia($coni_codigo,$data,$condiAgeCod){
            $grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$data'");
            if($grad->grad_intervalo_horario){
                return $grad->grad_intervalo_horario;
            } else {
                //$tbConi = new Application_Model_ConvenioItens();
                //return $tbConi->getIntervalos($coni_codigo)->coni_intervalo;
                $tbConDiaAge = new Application_Model_ConvenioDiasSemanaAgendamento();
                return $tbConDiaAge->getIntervalos($coni_codigo,$condiAgeCod)->condi_age_intervalo;
            }
	}
        
        /*public function getIntervaloDia($coni_codigo,$data,$condiAgeCod){
            $grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$data'");
            if($grad->grad_intervalo_horario){
                return $grad->grad_intervalo_horario;
            } else {
                //$tbConi = new Application_Model_ConvenioItens();
                //return $tbConi->getIntervalos($coni_codigo)->coni_intervalo;
                $tbConDiaAge = new Application_Model_ConvenioDiasSemanaAgendamento();
                return $tbConDiaAge->getIntervalos($coni_codigo,$condiAgeCod)->condi_age_intervalo;
            }
	}*/
        
        public function geraGrade($dados){
            $grad = $this->fetchRow("coni_codigo=$dados[coni_codigo] AND grad_dia='$dados[grad_dia]'");
            if($grad){
                return $grad->grad_intervalo_horario;
            }else{
                $grad_intervalo_horario = $this->salvar($dados);
                return $grad_intervalo_horario;
            }
        }
        
         public function getGradeDia($coni_codigo,$data){
		$grad = $this->fetchRow("coni_codigo=$coni_codigo AND grad_dia='$data'");
                return $grad;
	}
        
        public function atualizaIntervalo($dados){
            $grad = $this->fetchRow("coni_codigo=$dados[coni_codigo] AND grad_dia='$dados[grad_dia]'");
            $grad->grad_intervalo_horario = $dados[grad_intervalo_horario];
            
            if($grad->grad_codigo)
                $dados[grad_codigo] = $grad->grad_codigo;
            
            $this->salvar($dados);
        }
}
