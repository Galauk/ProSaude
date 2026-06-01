<?php
include_once 'global.php';
//echo "<pre>".print_r($_GET,true);
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$vac_acao = $_GET['resposta'];
$usuario = $_GET['pac_codigo'];
$pro_cod = $_GET['pro_codigo'];
$dose = $_GET['dose'];
$unidade = $_GET['unidade'];
$linha = $_GET['linha'];
$usu_codigo_prontuario = $_GET["usu_codigo_prontuario"];

// validações: impedir aplicar/preencher com data futura.
if($vac_acao == "A" || $vac_acao == "P"){
	list($d,$m,$y) = explode("/",$data);
	$mkVacina = mktime(0, 0, 0, $m, $d, $y);
	$mkAgora  = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	if($mkAgora < $mkVacina){
		return false;
	}
}

$selectIteCodigo = "SELECT * 
					  FROM itens_movimento im
					  JOIN controlefracionado c
					    ON c.ite_codigo = im.ite_codigo
					 WHERE im.pro_codigo = $pro_cod   
					   AND cont_dose > 0";
		//die($selectIteCodigo);
		$querySelectIte = pg_query($selectIteCodigo);
		$resQueryIte = pg_fetch_array($querySelectIte);
		$ite_codigo = $resQueryIte['ite_codigo'];
		$cont_dose= $resQueryIte['cont_dose'];
		$cont_codigo = $resQueryIte['cont_codigo'];
$consulta = "SELECT * 
			   FROM vacina_usuario 
			  WHERE usu_codigo = $usuario 
			    AND pro_codigo = $pro_cod 
				AND vac_dose = $dose";
$query = pg_query($consulta);

$umaLinha = pg_fetch_array($query);
if($umaLinha['vac_usu_codigo'] != '' && $dose < 6)
{
	$sobrepoe = "UPDATE vacina_usuario 
					SET vac_acao = '$vac_acao',
						vac_qtde = '1',
						cont_codigo = $cont_codigo, 
					 	vac_unidade = '$unidade',
					 	vac_data = '$data'
				  WHERE vac_usu_codigo = $umaLinha[vac_usu_codigo]";
	$exe = pg_query($sobrepoe);
	
}else {
	$stms = "INSERT INTO vacina_usuario 
						(usu_codigo,
						 pro_codigo,
						 vac_qtde,
						 cont_codigo,
						 vac_acao,
						 vac_dose,
						 vac_data,
						 vac_unidade)
					VALUES 
						(".($usuario == "" ? "'$usu_codigo_prontuario'" : "'$usuario'").",
						'$pro_cod',
						 '1',
						".($cont_codigo?"'$cont_codigo'":"NULL").",
						'$vac_acao',
						'$dose',
						'$data',
						".($unidade == "" ? "null" : "upper('$unidade')").")";
	$executa = pg_query($stms) or die ($stms."<br />".pg_last_error());
}

if($vac_acao == 'A'){
	$cont_dose_aplicada = $cont_dose - 1;
}else{
	$cont_dose_aplicada = $cont_dose; // CASO A AÇÃO NÂO FOR APLICAR, NÃO DESCONTA A DOSE.
}

$update = "UPDATE controlefracionado 
			  SET cont_dose = $cont_dose_aplicada 
			WHERE ite_codigo = $ite_codigo ";
$executaUpdate = pg_query($update);
echo $cont_dose_aplicada."_".str_pad($linha, 2, 0, STR_PAD_LEFT);
?>
