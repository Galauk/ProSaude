<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    
$id_login 	= $_GET['id_login'];    
$valor      = $_GET['valor'];
$gex_codigo	= $_GET['gex_codigo'];
$gex_tipo 	= $_GET['gex_tipo'];
         
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

//$query = pg_query("select *from grade_exame where proc_codigo = '$proc_codigo'");
//if(pg_num_rows($query) == "0") {
//   $sql = pg_query("insert into grade_exame_ (proc_codigo,gex_status,gex_qtde) values ('$proc_codigo','S','$gex_qtde')");
//} else {
	$stmt = "update grade_exame_mensal set $coluna = $valor where gex_codigo = '$gex_codigo'";
	$sql = pg_query($stmt) or die( pg_last_error() );
//}


reglog($id_login,"Atualizando Exame Mensal. Cod.: $gex_codigo, Tipo: $gex_tipo Valor: $valor");
        
/*
        $sql_x = "select from grade_mensal where esp_codigo = '$esp_codigo' 
                  and med_codigo = '$med_codigo' and 
                  --age_item = '$agt_item' and 
                  grm_periodo='$grm_periodo' 
                  and  grm_codigo='$grm_codigo'";
        $exec_sql_x = pg_query($sql_x); 

        while ($r = pg_fetch_array($exec_sql_x)){
         
            echo $r['usr_login_alt']; 
        }
*/
	echo "Atualizado"; 
?>
