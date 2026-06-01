<?php
/**
 * @brief Arquivo auxiliar dos procedimentos
*/ 

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

#if( $acao == 'busca_uni' || $acao == 'busca_usr' )
#{
    $stmt   = "SELECT proc_codigo, proc_nome FROM procedimento where proc_exame = 'S' ORDER BY proc_nome";
 
    $qry    = db_query($stmt);
    $result = array();
    while( $row = pg_fetch_array($qry) )
        $result[] = array( 'cod' => $row[0], 'nome' => $row[1] );
    
    $Json = & new Services_JSON;
    $out = $Json->encode( $result );
    print $out;
#}
#else if( $acao == 'apagar' )
#{
#    $stmt = "DELETE FROM mensagem WHERE msg_codigo IN ($codigos)";
#    db_query( $stmt );
#}
