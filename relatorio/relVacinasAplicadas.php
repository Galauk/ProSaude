<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
$common = new commonClass();
$form = new classForm();
$table = new tableClass();
$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$uni_codigo = $_GET["uni_codigo"];

$sqlUnidade = "select * from unidade where uni_codigo = $uni_codigo";
$queryUnidade = pg_query($sqlUnidade);
$linhaUnidade = pg_fetch_array($queryUnidade);

$sqlCidade = "select * from cidade where cid_codigo_ibge = '$linhaUnidade[uni_codigo_ibge]'";
$queryCidade  = pg_query($sqlCidade);
$linhaCidade = pg_fetch_array($queryCidade);

$idade = verIdade("08/08/1990");

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
	
	echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"javascript:window.print();this.style.display='none';\"")."</center>";

	echo "<table class=table style='font-size:14px;font-family:verdana' border=0>
			<tr>
				<td width=130><b>GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE</b></td>
				<td width=10 align=right>".date("d/m/Y h:i:s")."</td>
			</tr>
			<tr>
				<td colspan=2>".strtoupper(html_entity_decode($Tit))."</td>
			</tr>
			<tr>
				<td><b> Unidade nome: </b> $linhaUnidade[uni_desc]</td>
				<td><b> CNES: </b>: $linhaUnidade[uni_cnes]</td>
			</tr>
			<tr>
				<td><b> Cidade: </b> $linhaCidade[cid_nome]</td>
				<td><b> cod.IBGE: </b>: $linhaUnidade[uni_cnes]</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
		
	echo "<table class=lista>
			<tr>
				<th colspan=7 style='font-size:16px;text-align:center'>
					VACINAS APLICADAS POR PERIODO
				</th>
			</tr>
			<tr>
				<th><b>DATA</b></th>
				<th><b>0 a 10 anos</b></th>
				<th><b>10 a 20 anos</b></th>
				<th><b>20 a 30</b></th>
				<th><b>30 a 40</b></th>
				<th><b>40 a 50</b></th>
			</tr>";
			if($data_inicial == ""){
				$whereData = "temp_data = '$data_final'";
			}
			if($data_inicial == "" && $data_final == ""){
				$whereData = "";	
			}
			if($data_inicial == true && $data_final == true){
				$whereData = "temp_data between '$data_inicial' and '$data_final'";	
			}
?>