<?php

class Elotech_View_Helper_AbreviaNome extends Zend_View_Helper_Abstract {


    function abreviaNome($nomecompleto=FALSE, $maxtamanho=FALSE){
        
	//die($nomecompleto."---".strlen($nomecompleto)."--".$maxtamanho)	;
	if(strlen($nomecompleto)>$maxtamanho){

            $nome = explode(" ", $nomecompleto);			
            for	($i=0; $i<(count($nome))-1; $i++){
					$nomedomeio = $nome[$i];
						if (($nomedomeio == "de") || ($nomedomeio == "da") || ($nomedomeio == "dos") || ($nomedomeio == "das") || ($nomedomeio == "di")){
								$nomecartao .= $nomedomeio." ";
								
						} else {
							    if($i == 1 || $i == 0 || $i == count($nome) - 2  ){
									
									$nomecartao .= $nomedomeio." ";
								}else{
								
									$reducao = substr($nomedomeio, 0, 1);
									$nomecartao .= $reducao.". ";
								}
						}
					
            }//fim do for
            $nomecartao .= $nome[$i];
            
            return $nomecartao;
	}else{
            return $nomecompleto;
        } 
    }

} 