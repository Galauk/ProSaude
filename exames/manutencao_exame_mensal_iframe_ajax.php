<?php
/**
 * Arquivo de operacoes ajax do iframe
*/

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

// variaveis    
$id_login 	= $_GET['id_login'];
$valor      = $_GET['valor'];
$codigo	    = $_GET['codigo'];
$gex_tipo 	= $_GET['gex_tipo'];
$usr        = db_get("SELECT usr_nome FROM usuarios WHERE usr_codigo = $id_login" );
$acao       = trim( strtolower($_GET['acao']) );

// usado pra teste !
$med_codigo	= $_GET['med_codigo'];
$gex_periodo= $_GET['gex_periodo'];
$agt_codigo = $_GET['agt_codigo'];


if( $acao == 'upd_tipo_1' )
{
    // arrumando os valores
    if( $gex_tipo == 'Q' )
    {
        $valor = intval( abs($valor) );
        $coluna = 'gex_qtde';
    }
    else if( $gex_tipo == 'V' )
    {
        $valor = str_replace( ',', '.', $valor );
        $valor = floatval( abs($valor) );
        $coluna = 'gex_valor';
    }

    //print
	$stmt = "UPDATE grade_exame_mensal SET ".
        "$coluna = $valor, ".
        "usr_codigo_alt = $id_login ".
        "WHERE gex_codigo = $codigo";
	
    $sql = db_query($stmt);

    reglog($id_login,"Atualizando Exame Mensal. Cod.: $codigo, Tipo: $tipo Valor: $valor");

    // OK
    print "( {".
        "'tipo': 1, ".
        "'ok': true, ".
        "'msg' : 'Grade atualizada [".date('d/m H:i')."] !', ".
        "'codigo' : $codigo, ".
        "'usr' : '$usr'".
    "} )";
}
else
{
    //print
    $stmt_teste = 
    "SELECT gem_valor, total, gem_valor - total AS diferenca
	FROM 
		( SELECT laboratorio_calcula_custo_agt( {$med_codigo}::int8, '{$gex_periodo}'::date, 30::int2, {$agt_codigo}::int8 ) AS total ) AS total_temp,
		grade_exame_mensal_manut AS g1 
	WHERE g1.med_codigo={$med_codigo}
        AND g1.gem_periodo = '{$gex_periodo}'
        AND g1.agt_codigo = {$agt_codigo} ";
    
    $teste  = db_getRow( $stmt_teste );
    $dif    = (float) $teste['diferenca'];
    
    //var_dump($teste);
    
    if( $dif < 0 )//|| true )
    {
        // Nao OK
        print "( {".
            "'tipo': 2, ".
            "'ok': false, ".
            "'msg' : 'Grade nao atualizada [".date('d/m H:i')."] ! Valor negativo (R$) $dif', ".
            "'codigo' : $codigo, ".
            "'usr' : null, ".
            "'total': null, ".
            "'dif': null ".
        "} )";
    }
    else
    {
        
        //print
        $stmt = "UPDATE grade_exame_mensal_manut SET 
            gem_valor = $valor,
            usr_codigo_alt = $id_login
            WHERE gem_codigo = $codigo";
            
        $sql = db_query($stmt);
    
        reglog($id_login,"Atualizando Exame Mensal. (Manut) Cod.: $codigo, Valor: $valor");
    
        $stmt_result = 
        "SELECT total, ( COALESCE(gem_valor,0) - COALESCE(total,0) ) AS diferenca
        FROM 
            ( SELECT laboratorio_calcula_custo_agt( {$med_codigo}::int8, '{$gex_periodo}'::date, 30::int2, {$agt_codigo}::int8 ) AS total ) AS total_temp,
            grade_exame_mensal_manut AS g1 
        WHERE g1.med_codigo={$med_codigo}
            AND g1.gem_periodo = '{$gex_periodo}'
            AND g1.agt_codigo = {$agt_codigo} ";
    
        $result = db_getRow($stmt_result);
        
        $result['total']        = number_format( $result['total'], 2 );
        $result['diferenca']    = number_format( $result['diferenca'], 2 );
        
        // OK
        print "( {".
            "'tipo': 2, ".
            "'ok': true, ".
            "'msg' : 'Grade atualizada [".date('d/m H:i')."] !', ".
            "'codigo' : {$codigo}, ".
            "'usr' : '{$usr}', ".
            "'total': '{$result[total]}', ".
            "'dif': '{$result[diferenca]}' ".
        "} )";
    } 
}