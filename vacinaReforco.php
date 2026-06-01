<?
	
/*
 * Lista quais os reforços (de vacinas) foram aplicados no paciente
 * Requisitado por: vacina.php (carteirinha)
 *    \-> CTRL + Click no reforço
 */

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$paciente   = $_GET['paciente'];
$pro_codigo = $_GET['pro_codigo'];
$selectReforco = "SELECT to_char(vac.vac_data,'dd/mm/yyyy') AS data,
						  u.uni_desc,
						  vac.vac_acao
					 FROM vacina_usuario AS vac 
				LEFT JOIN unidade AS u ON u.uni_codigo = vac.vac_unidade::integer
					WHERE usu_codigo = '$paciente' 
					  AND vac_dose = '6' 
					  AND pro_codigo = $pro_codigo
					ORDER BY vac_data ASC;";
//echo $selectReforco; exit;
$query = pg_query($selectReforco) or die("E: ". pg_last_error());

while ($r = pg_fetch_array($query)){
	printf("<span class=\"%s\">%dº reforço em %s - %s</span><br />",".bg".$r['vac_acao'], ++$i, $r['data'], $r['uni_desc']);
}
