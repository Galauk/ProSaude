<?php

include "../Cidadao.php";
class Exportacao {
    
    
    public function createCidadao(){

        $dados_cidadao = new esus\banco_cidadao\BancoCidadao();
        $cidadoes = $dados_cidadao->getDadosCidadao();
        $dados_cidadao->inserirCidadao($cidadoes);
        
    }
}

$tCidadao = new Exportacao();

$tCidadao->createCidadao();

?>
