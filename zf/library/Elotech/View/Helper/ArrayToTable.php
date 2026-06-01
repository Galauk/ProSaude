<?php

class Elotech_View_Helper_ArrayToTable extends Zend_View_Helper_Abstract {
	
	private $th;
	private $cols;
    protected $_view;

    public function setView(Zend_View_Interface $view) {
		parent::setView($view);
        $this->_view = $view;
    }

	function arrayToTable($arr, $config,$total=FALSE) {
		$this->th = $config['th'];
		$this->cols = count($this->th);
		$i = 0;
		$table = "<table>";
		foreach ($arr as $row) {
			if (!$i++ && count($row) > 1) { // a primeira linha não é um grupo
				$table .= $this->th($row);				
			}

			if (count($row) == 1) { // grupo
				$table .= "\n\t<tr>";
				$table .= "\n\t\t<th colspan=\"{$this->cols}\">" .$row[0] . "</th>\n\t</tr>";
				$table .= $this->th($row);
				$table .= "\n\t<tr>";
			} else {
				$table .= "\n\t<tr>";
				foreach ($this->th as $key => $value) {
					$class = "";
					$valor = $this->formatar($row[$key], $config['formato'][$key], $class);
					$table .= "\n\t\t<td class=\"{$class}\">" . $valor . "</td>";
				}
			}
			$table .= "\n\t</tr>";
		}
		$table .= "\n</table>";               
		return $table;
	}
	
	private function formatar($valor, $formato, &$class){
		switch ($formato) {
			case "data":
				$class = "c";
				return $this->_view->data($valor);
				break;
			case "num":
				$class = " d";
				return number_format($valor, 0, ",", ".");
				break;

			default:
				return $valor;
				break;
		}
	}

	private function th($row) {
		$table = "\n\t<tr>";
		foreach ($this->th as $key => $value) {
			$table .= "\n\t\t<th>" . $value . "</th>";
		}
		$table .= "\n\t</tr>";
		
		return $table;
	}

}

