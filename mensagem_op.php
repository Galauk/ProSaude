<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

$_SESSION[modulo] = "WebSocialSaude/"; $_SESSION[root] = $_SERVER[DOCUMENT_ROOT] . "/"; $_SESSION[linkroot] = "http://" . $_SERVER[HTTP_HOST] . "/"; $_SESSION[comum] = "WebSocialComum/"; $_SESSION[modulo] = "WebSocialSaude/"; require_once $_SESSION[root].$_SESSION[modulo]."sessao_controller.php";

$sessao = new TempoSessao();
$sessao->primeiraPagina();

if( $acao == 'busca_uni' || $acao == 'busca_usr' )
{
    if( $acao == 'busca_uni' )
        $stmt   = "SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc";
    else
        $stmt = "SELECT usr_codigo, usr_nome FROM usuarios ORDER BY usr_nome";        
 
    $qry    = db_query($stmt);
    $result = array();
    while( $row = pg_fetch_array($qry) )
        $result[] = array( 'cod' => $row[0], 'nome' => $row[1] );
    
    $Json = & new Services_JSON;
    $out = $Json->encode( $result );
    print $out;
}
else if( $acao == 'apagar' )
{
    $stmt = "DELETE FROM mensagem WHERE msg_codigo IN ($codigos)";
    db_query( $stmt );
}
