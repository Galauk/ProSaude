<?php

class Elotech_View_Helper_ThTabelaData extends Zend_View_Helper_Abstract {
	

    public function setView(Zend_View_Interface $view) {
		parent::setView($view);
        $this->_view = $view;
    }

	/**
	 * Cria os titulos para tabela de agendamento
	 * @param date $data_inicial
	 * @param date $data_final
	 * @return string tags html
	 */
	function thTabelaData($data_inicial, $data_final, $pular=2) {
		$table = "\n\t<tr class=\"ui-widget-header\">";
		
		// celula do canto esquerdo superior em branco
		for($x=0; $x<$pular; $x++)
			$table .= "\n\t\t<th rowspan=\"3\">&nbsp;</th>";
		
		$arrDias = $this->datasToArray($data_inicial, $data_final);
		$diasMes = $this->getDiasPorMes($arrDias);
				
		foreach($diasMes as $mes => $dias){
			$table .= "\n\t\t<th colspan=\"$dias\">".$this->nomeMes($mes)."</th>";
		}
		
		$table .= "\n\t</tr>\n\t<tr>";
		$index = 0;
		
		foreach($arrDias as $data){
			$dia = substr($data, -2);
			$mes = substr($data, 5, 2);
			$table .= "\n\t\t<th style=\"font-size:10px\" data-index=\"".(++$index)."\" data-dia=\"$data\" class=\"cor".($mes%2)."\">".$dia."</th>";
		}
		
		$table .= "\n\t</tr>\n\t<tr class=\"ui-widget-header\">";
		$index = 0;
		
		foreach($arrDias as $dia){
			$dow = $this->diaDaSemana($dia, FALSE);
			$table .= "\n\t\t<th style=\"font-size:10px\" data-index=\"".(++$index)."\" data-dow=\"$dow\">".$this->diaDaSemana($dia)."</th>";
		}
		
		$table .= "\n\t</tr>";		
		
		return $table;
	}
	
	private function diaDaSemana($dia,$nome=TRUE){
		list($y,$m,$d) = explode("-", $dia);
		$nomes = array("D","S","T","Q","Q","S","S");
		$diaSemana = date("w", mktime(0, 0, 0, $m, $d, $y));
		if($nome)
			return $nomes[$diaSemana];
		else 
			return $diaSemana;
	}
	
	private function nomeMes($mes){
		$nomes = array(null, "Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		return $nomes[(int)$mes];
	}
	
	private function getDiasPorMes($arrDias){
		$mes = array();
		foreach($arrDias as $dia){
			$mesAtual = substr($dia, 5, 2);
			if(!isset($mes[ $mesAtual ]))
				$mes[ $mesAtual ] = 1;
			else
				$mes[ $mesAtual ]++;
		}		
		return $mes;
	}
	
	/**
	 * @see Application_Model_Funcoes::datasToArray()
	 */
	private function datasToArray($data_inicial, $data_final){
		$tbFun = new Application_Model_Funcoes();
		return $tbFun->datasToArray($data_inicial, $data_final);
	}

}

