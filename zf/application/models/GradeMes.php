<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_GradeMes extends Elotech_Db_Table_Abstract {

	protected $_name = 'grade_mes';
	protected $_primary = 'gram_codigo';

	public function salvar(array $data) {					
		$this->notEmpty(array("coni_codigo","gram_cota_mes","gram_mes"), $data);
		
		return parent::salvar($data);
	}
	
	/**
	 * Salva uma lista de datas alteradas (distribuição)
	 * @param array $data dados do post,
	 * @param array $original dados originais
	 * @return int quantos registros foram inseridos/atualizados
	 */
	public function salvarDoArray($data, $original) {
		$atualizados = 0;
		foreach ($data as $coni_codigo => $dias){
			foreach($dias as $dia => $vagas){
				// se não houver alteração, pular
				if($vagas == $original[$coni_codigo][$dia])
					continue;
				
				$mes = substr($dia, 0, -3)."-01";
				
				$dados = array(
					"coni_codigo" => $coni_codigo,
					"gram_mes" => $mes,
					"gram_codigo" => $this->fetchRow("coni_codigo=$coni_codigo AND gram_mes='$mes'")->gram_codigo,
					"gram_cota_mes" => ($vagas==""?-1:$vagas)
				);
				
				$this->salvar($dados);
				$atualizados++;
			}
		}
		
		return $atualizados;
	}

	/**
	 * Retorna quantos vagas foram liberadas para o mês informado.
	 * Procura por exceção no mês, se não houver, retornará o modelo.
	 * Obs.: não cria a exceção padrão
	 * @param int $coni_codigo
	 * @param string $data formato 2012-05-02 (passar qualquer dia)
	 * @return int quantas vagas há no mês informado 
	 */
	public function getCotaMes($coni_codigo, $data) {
		$data = substr($data, 0, -3) . "-01";
                $gram = $this->fetchRow("coni_codigo=$coni_codigo AND gram_mes='$data'");
                if ($gram) {
                    return $gram->gram_cota_mes;
		} else {
                    $tbConi = new Application_Model_ConvenioItens();
                    return $tbConi->find($coni_codigo)->current()->coni_cota_mes;
		}
	}

	/**
	 * Copia o modelo (convenio_itens) para a tabela grade_mes somente se NÃO houver uma exceção criada
	 * @param int $coni_codigo
	 * @param date $mes
	 * @return Zend_Db_Table_Row_Abstract
	 */
	public function criarCotaFromModelo($coni_codigo, $mes) {	
		$mes = substr($mes, 0, -3)."-01";
		
		$gram = $this->fetchRow("coni_codigo=$coni_codigo AND gram_mes='$mes'");
		if($gram)
			return $gram;
		
		$tbConi = new Application_Model_ConvenioItens();
		$tbConv = new Application_Model_Convenio();
		
		$coni = $tbConi->find($coni_codigo)->current();
		$conv = $tbConv->find($coni->conv_codigo)->current();
		
		$dados = array(
			"coni_codigo" => $coni_codigo,
			"gram_cota_mes" => $coni->coni_cota_mes,
			"gram_mes" => $mes,
			"gram_valor" => $conv->conv_valor_mes // valor máximos disponível por mes
		);
		
		$gram_codigo = $this->salvar($dados);
		return $this->find($gram_codigo)->current();
	}

	/**
	 * Atualiza as cotas disponível nas exceções que não tiveram alterações
	 * Atualiza as exceções com data >= hoje
	 * @param int $coni_codigo
	 * @param int $cota 
	 */
	public function atualizarCota($coni_codigo, $cota){
		$tbAge = new Application_Model_Agenda();
		$vagas = $tbAge->vagas($coni_codigo, date("Y-m-d"));
		
		$cache = array();
		
		foreach($vagas as $vaga){
			// Somente se o limite desse mês, for igual ao modelo do mês
			if(!$vaga->gram_alterada){
				$mes = substr($vaga->grad_dia, 0, -3)."-01";
				
				// cache para diminuir as consultas
				if(!in_array($mes, $cache)){
					$cache []= $mes;
					
					if($cota >= 0 && $cota < $vaga->agendado_mes) // foi alterado para uma quantidade menor que as vagas já distribuidas para o mês
						$cota = $vaga->agendado_mes;						
					
					// atualizar a cota desse mês
					$this->alterarCota($mes, $coni_codigo, $cota);
				}				
			}
		}		
	}
	
	/**
	 * Altera o valor da cota
	 * Obs.: não valida nada
	 * @param date $mes
	 * @param int $coni_codigo
	 * @param int $cota 
	 */
	private function alterarCota($mes, $coni_codigo, $cota){
		$gram = $this->fetchRow("coni_codigo=$coni_codigo AND gram_mes='$mes'");
		$gram->gram_cota_mes = $cota;
		$gram->save();
	}
}
