<?php

class Elotech_View_Helper_ArrayDispensacao extends Zend_View_Helper_Abstract {
	
	private $th;
	private $cols;
    protected $_view;

    public function setView(Zend_View_Interface $view) {
		parent::setView($view);
        $this->_view = $view;
    }

	function arrayDispensacao($arr) {
		$table = "<table class=\"grid ui-widget ui-widget-content ui-corner-all\" width=\"400px\">";
		$cont = array();
		
		foreach($arr as $pro_codigo => $produto){ //
			$table .= "<tr>";
			$table .= "<th colspan=\"3\" class=\"ui-widget-header\">".$produto['pro_nome']."</th>";
			$table .= "</tr>";			
			$table .= "<tr>";
			if(isset($produto['cont'])){
				foreach($produto['cont'] as $cont_codigo => $cont){
					$table .= "<td class=\"ui-state-default\">";
					$table .= "<input type=\"hidden\" name=\"cont[$cont_codigo]\" value=\"".$cont['total']."\" />";
					$table .= "Lote: ".$cont['pro_lote']."</td>";
					$table .= "<td class=\"ui-state-default c\">Validade: ".$this->_view->data($cont['pro_validade'])."</td>";
					$table .= "<td class=\"ui-state-default d\">Qtd: ".number_format($cont['total'],0,",",".")."</td>";
				}
			}
			if(isset($produto['saldo'])){
				foreach($produto['saldo'] as $sal_codigo => $saldo){
					$table .= "<td class=\"ui-state-active\">";
					$table .= "<input type=\"hidden\" name=\"saldo[$sal_codigo]\" value=\"".$saldo['total']."\" />";
					$table .= "Lote: ".$saldo['pro_lote']."</td>";
					$table .= "<td class=\"ui-state-active c\">Validade: ".$this->_view->data($saldo['pro_validade'])."</td>";
					$table .= "<td class=\"ui-state-active d\">Qtd: ".number_format($saldo['total'],0,",",".")."</td></tr>";
				}
			}
			if(isset($produto['cfr'])){
				foreach($produto['cfr'] as $cfr_codigo => $reserva){
					$table .= "<td class=\"ui-state-active\">";
					$table .= "<input type=\"hidden\" name=\"cfr[$cfr_codigo]\" value=\"".$reserva['total']."\" />";
					$table .= "Lote: ".$reserva['pro_lote']."</td>";
					$table .= "<td class=\"ui-state-active c\">Validade: ".$this->_view->data($reserva['pro_validade'])."</td>";
					$table .= "<td class=\"ui-state-active d\">Qtd: ".number_format($reserva['total'],0,",",".")."</td>";
				}
			}
			if(isset($produto['faltou'])){
				$table .= "<td colspan=\"2\" class=\"ui-state-error\">Faltou</td>";
				$table .= "<td class=\"ui-state-error d\">Qtd: ".$produto['faltou']."</td>";
			}
			$table .= "</tr>";
		}
		
		$table .= "</table>";
		return $table;
	}

}

