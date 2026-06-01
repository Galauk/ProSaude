<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

$nome_vac = $_GET['nome_vac'];
$num_doses = $_GET['num_doses'];
$marca = $_GET['marca'];
$validade = $_GET['dataVal'];
$dose_um = $_GET['dose_um'];
$dose_dois = $_GET['dose_dois'];
$dose_tres = $_GET['dose_tres'];
$dose_quatro = $_GET['dose_quatro'];
$dose_cinco = $_GET['dose_cinco'];
$reforco = $_GET['reforco'];
$tempo = $_GET['tempo'];

$sql = "select * from produto where pro_nome = UPPER('$nome_vac')";
$queryDois = pg_query($sql);
$linha = pg_fetch_array($queryDois);
$pro_codigo = $linha['pro_codigo'];
echo $linha['pro_codigo'];

$stmt = "UPDATE produto SET
				pro_nome = '$nome_vac'
		  where pro_codigo = $pro_codigo";
$query = pg_query($stmt);
//echo $stmt;
$sqlCarteirinha = "UPDATE carteirinha SET
							dose_um = '$dose_um',
							dose_dois = '$dose_dois',
							dose_tres = '$dose_tres',
							dose_quatro = '$dose_quatro',
							dose_cinco = '$dose_cinco',
							reforco = '$reforco' 
						WHERE pro_codigo = $pro_codigo";
						  
//echo "<br/>".$sqlCarteirinha;
$queryCarteirinha = pg_query($sqlCarteirinha);

$sqlPart = "UPDATE vacinas_part SET
				   marca = '$marca',
				   doses_vac = $num_doses,
				   tempo_vida_vac = $tempo,
				   validade_vac = '$validade'
			 where pro_codigo = $pro_codigo";
//echo "<br/>".$sqlPart;
$queryPart = pg_query($sqlPart);
?>