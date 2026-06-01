<link href="styleRelatorio.css" rel="stylesheet" type="text/css">

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
		
	echo "<table style='border: 1px solid black;' border='1' cellpadding='2' cellspacing='0' width='100%'>
			<tbody><tr>

				<td class='tabtitulo' align='Center' width='16%'><span class='titrel'>Total</span></td>
				<td class='tabtitulo' align='Center' width='10%'><span class='titrel'>Hidro.</span></td>
				<td class='tabtitulo' align='Center' width='10%'><span class='titrel'>Prop.</span></td>
				<td class='tabtitulo' align='Center' width='10%'><span class='titrel'>Cap.</span></td>
				<td class='tabtitulo' align='Center' width='10%'><span class='titrel'>Gli.</span></td>
				<td class='tabtitulo' align='Center' width='10%'><span class='titrel'>Metf.</span></td>
			</tr>";
			if($data_inicial == ""){
				$whereData = "and hiper_data = '$data_final'";
			}
			if($data_inicial == "" && $data_final == ""){
				$whereData = "";	
			}
			if($data_inicial == true && $data_final == true){
				$whereData = " and hiper_data between '$data_inicial' and '$data_final'";	
			}
			

			echo "<table width=100% border=1>
				  	<tr>
						<td class='tabtitulo' align='Center' width='16%'> Medicamentos </td>";
			for($i = 1; $i<6 ;$i++){
				$sqlRelatorioHidro= "SELECT sum(valor) as total
									  from (select sum((SELECT CAST( 
											 translate (hipermedac_dosagem,',','.') AS numeric(2,1))
										   ))as valor 
										  from hiperdia_medicamentos_acompanhamento
										 where pro_codigo = '0$i' 
									     union all select sum ((SELECT CAST( 
											translate(hipermed_dosagem,',','.') AS numeric(2,1))
										   )) as valor 
											 from hiperdia_medicamentos as hipermed
											where pro_codigo = '0$i' ) as u ";
				$queryHidro = pg_query($sqlRelatorioHidro);
				$regHidro = pg_fetch_array($queryHidro);
				$total = $regHidro[total];
				if($total == null){
					$total = 0;
				}
				echo "<td class='CorpoAzul' align='Center' width='10%'>$total</td>";
			}
			echo "</tr>
				  <tr>
				  	<td class='tabtitulo' align='Center' width='16%'> Usu&aacute;rios </td>";
			for($i = 1; $i<6 ;$i++){
				$sqlNumeroUsuarios =  "select count(distinct hip.hiper_codigo) as tot 
									  from hiperdia as hip
									  join hiperdia_medicamentos as hipmed
										on hip.hiper_codigo = hipmed.hiper_codigo
									 where hipmed.pro_codigo = '0$i' $whereData ";
				$queryNumeroUsuarios = pg_query($sqlNumeroUsuarios);
				$regNumeroUsuarios = pg_fetch_array($queryNumeroUsuarios);
				echo "<td class='CorpoAzul' align='Center' width='10%'>$regNumeroUsuarios[tot]</td>";
			}
			
?>