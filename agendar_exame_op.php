<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

if( $acao == 'agente' && ! empty($uni_codigo) )
{
    $uni_codigo = intval($uni_codigo);
    $stmt_agt = "SELECT agt_codigo, agt_numero, COALESCE(agt_responsavel,agt_descricao) 
    FROM agente WHERE uni_codigo = {$uni_codigo} ORDER BY 3";
    $qry_agt = db_query($stmt_agt);
    $arr = array();
    while( $row_agt = pg_fetch_array($qry_agt) )
    {
        $arr[] = array( 'agt_codigo' => $row_agt[0], 'agt_desc' => $row_agt[2] );
    }

    // create a new instance of Services_JSON
    $json = new Services_JSON();
    // convert a complexe value to JSON notation, and send it to the browser
    $output = $json->encode($arr);
    print($output);

}
