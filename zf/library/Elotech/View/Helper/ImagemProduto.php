<?php

class Elotech_View_Helper_ImagemProduto {

    protected $_view;

    public function setView($view) {
        $this->_view = $view;
    }

    function imagemProduto($pro_codigo, $pro_nome="", $width=100) {
		if(file_exists(APPLICATION_PATH . "/../public/images/medicamentos/$pro_codigo.jpg")){
			return "<img class=\"pro_imagem\" src=\"".$this->_view->baseUrl("/public/images/medicamentos/$pro_codigo.jpg")."\" alt=\"$pro_nome\" title=\"$pro_nome\" width=\"$width\" />";
		} else {
			return "<img class=\"pro_imagem\" src=\"".$this->_view->baseUrl("/public/images/medicamentos/sem_imagem.jpg")."\" alt=\"$pro_nome\" title=\"$pro_nome\" width=\"$width\" />";
		}		
    }

} 