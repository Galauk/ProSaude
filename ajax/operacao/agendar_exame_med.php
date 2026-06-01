<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";

$esp_codigo = abs(intval($esp_codigo));

$stmt = "SELECT med_codigo, med_nome 
		FROM medico
		WHERE prestador_servico = 'N'
		AND med_codigo IN 
			(SELECT med_codigo FROM medico_especialidade WHERE esp_codigo = $esp_codigo )
		ORDER BY med_nome  ";

$qry = db_query($stmt);

if( pg_num_rows($qry) == 0 )
{
	echo "[ nenhum médico nesta especialidade ]";
}
else
{
	echo "\n<select name='r_med_codigo' id='r_med_codigo' class='box'>";
	
	while( $row = pg_fetch_array($qry) )
	{
		echo "\n<option value='$row[0]'>$row[1]</option>";
	}
	
	echo "\n</select>";
}
?>