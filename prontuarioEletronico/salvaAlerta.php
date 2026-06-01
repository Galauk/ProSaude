<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$alerta_desc = $_GET['alerta'];
$usu_codigo = $_GET['usu_codigo'];
$usr_codigo = $_GET['id_login'];
$alepac_data = $_GET['age_data'];
$stmt = 
	"INSERT INTO alerta ( 
				alerta_desc,
				usu_codigo 
				 ) VALUES (  
				'$alerta_desc',
				'$usu_codigo')";
$qry = pg_query($stmt);

$s = "select *  from alerta where usu_codigo = $usu_codigo";
$qrys = pg_query($s);

while ($linha = pg_fetch_array($qrys))
{
	$alerta_cod = $linha['alerta_cod']; 
	$stmt2 ="INSERT INTO 
				  alerta_usuario ( 
				  usr_codigo, 
				  alepac_data, 
				  alerta_cod, 
				  alepac_status,
				  usu_codigo
				   ) VALUES (  
				  '$usr_codigo', 
				  '$alepac_data', 
				  '$alerta_cod', 
				  'A',
				  $usu_codigo )";
	$exec = pg_query($stmt2);
}

echo '1';
?>