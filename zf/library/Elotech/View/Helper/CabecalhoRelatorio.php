<?php

class Elotech_View_Helper_CabecalhoRelatorio extends Zend_View_Helper_Abstract {
	
	protected $v;
	
	public function setView(Zend_View_Interface $view) {
		parent::setView($view);
		$this->v = $view;
	}

	function cabecalhoRelatorio($params) {
		$p = (object) unserialize($params);
		
		// echo "<pre>".print_r($p);exit;
		$out = "<ul>";
		if($p->data_inicial && $p->data_final){
			$out .= "<li><strong>Data:</strong> De ".$this->v->data($p->data_inicial)." Até ".$this->v->data($p->data_final)."</li>";
		} elseif($p->data_inicial){
			$out .= "<li><strong>Data:</strong> Apartir de ".$this->v->data($p->data_inicial)."</li>";			
		} elseif($p->data_final){
			$out .= "<li><strong>Data:</strong> Até ".$this->v->data($p->data_final)."</li>";			
		}
		
		// Passagem de titulo e dados por 3 parâmetro no relatório
		if ($p->dados) {
			$out .= "<li style='padding-top:5px'><strong>$p->titulo </strong> $p->dados</li>";
		}
		
		if($p->set_nome){
			$out .= "<li><strong>Setor:</strong> ".$p->set_nome."</li>";	
		}
                if($p->uni_desc){
			$out .= "<li><strong>Unidade:</strong> ".$p->uni_desc."</li>";	
		}
                if($p->usr_nome){
			$out .= "<li><strong>Profissional:</strong> ".$p->usr_nome."</li>";	
		}
		$out .= "</ul>";
		return $out;
	}
}

