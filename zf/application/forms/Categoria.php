<?php

class Application_Form_Cliente23 extends Zend_Form
{

    public function init()
    {
    	
		$this->setAction("leito/categoria/novo");

		$lgc_descricao = new Zend_Form_Element_Text("lgc_descricao");
		$lgc_descricao->setLabel("Categoria");
		$lgc_descricao->setRequired();
		
		$submit = new Zend_Form_Element_Submit("Enviar");
		
		$elementos = array($nome,$submit);
		$this->addElements($elementos);
		
    }
}
?>