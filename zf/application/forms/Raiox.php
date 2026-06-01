<?php

class Application_Form_Raiox extends Zend_Form
{

    public function init()
    {
    	
		$element = new Zend_Form_Element_File('uploadedfile');
		$element->setLabel('Enviar arquivo:')
			   ->setDestination('c:Dilee');

		// Quero que o usuário envia apenas 1 arquivo
		$element->addValidator('Count', false, 1);
		// com o tamanho limite de 100K
		$element->addValidator('Size', false, 102400);
		// apenasJPEG, PNG, e GIFs
		$element->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($element, 'foo');
		
    }
}
