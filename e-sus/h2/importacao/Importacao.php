<?php

include "../Endereco.php";

class Importacao{
    
    public function importaDadosEnderecos(){
        
        $inst_bd = new esus\banco_endereco\BancoEndereco();
       /* $inst_bd->importaPaisesH2();
        $inst_bd->importUf();
        $inst_bd->importSituacaoLocalidade();
        $inst_bd->importLocalidade();
        $inst_bd->importBairro();
        $inst_bd->importEndereco();
        $inst_bd->importEscolaridade();
        $inst_bd->importTipoSanguineo();*/
       
        
        /*$inst_bd->tipoPergunta();
        $inst_bd->perguntacontexto();
        $inst_bd->importaPergunta();
        $inst_bd->perguntaDetalhe();
        $inst_bd->visitaMotivo();
        $inst_bd->visitaDesfecho();
        $inst_bd->importaCiap();*/
        $inst_bd->tipoEquipe();
    }
}

$import = new Importacao();
$import->importaDadosEnderecos()

?>
