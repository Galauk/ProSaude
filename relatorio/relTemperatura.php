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
$gel_codigo = $_GET["gel_codigo"];
//$uni_codigo = $_GET["uni_codigo"];

$sqlConfig = "select * from config where conf_chave = 'NOME_CIDADE'";
$queryConfig = pg_query($sqlConfig);
$linhaCidade = pg_fetch_array($queryConfig);

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
				<td colspan=2><b> Cidade: </b> $linhaCidade[conf_valor_string]</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
		
	echo "<table class=lista>
			<tr>
				<th colspan=8 style='font-size:16px;text-align:center'>
					CONTROLE DE TEMPERATURA
				</th>
			</tr>
			<tr>
				<th><b>DATA</b></th>
				<th><b>GELADEIRA</b></th>
				<th><b>SETOR</b></th>
				<th><b>MAXIMA</b></th>
				<th><b>MOMENTO</b></th>
				<th><b>MINIMA</b></th>
				<th><b>PERIODO</b></th>
				<th><b>OBSERVA&Ccedil;&Atilde;O</b></th>
			</tr>";
			if($data_inicial == ""){
				$whereData = "AND temp_data = '$data_final'";
			}
			if($data_inicial == "" && $data_final == ""){
				$whereData = "";	
			}
			if($data_inicial == true && $data_final == true){
				$whereData = "AND temp_data between '$data_inicial' and '$data_final'";	
			}
			if ($gel_codigo != 0) {
				$whereGel = "AND gel.gel_codigo = '$gel_codigo'";
			}
			//$whereData AND 
			$sqlRelatorio = "SELECT 
								temp_periodo,
								temp_minima,
								temp_maxima,
								temp_momento,
								gel_marca,
								set_nome,
								temp_data,
								observacoes
							 FROM 
								temperatura_geladeira AS temp
							 INNER JOIN 
								geladeira AS gel ON temp.gel_codigo=gel.gel_codigo
							 INNER JOIN 
								setor AS set ON gel.set_codigo=set.set_codigo
						     INNER JOIN unidade AS uni
							    ON uni.uni_codigo=set.uni_codigo
							 WHERE 
							  1=1 
							  $whereData 
							  $whereGel
						     ORDER BY 
								set_nome, temp_data DESC";
			
			$queryRelatorio = pg_query($sqlRelatorio);
			
			while($linha = pg_fetch_array($queryRelatorio)){
				if($linha[temp_periodo] == '2'){
					$periodo = "Manh&atilde;";
				}else if($linha[temp_periodo] == '3'){
					$periodo = "Tarde";
				}else if($linha[temp_periodo] == '4'){
					$periodo = "Noite";
				}else if($linha[temp_periodo] == '1'){
					$periodo = "Madrugada";
				}
				
				echo "<tr>
					  	<td>".formatarData($linha[temp_data])."</td>
						<td align='left'>$linha[gel_marca]</td>
						<td align='left'>$linha[set_nome]</td>
						<td align='left'>$linha[temp_maxima]</td>
						<td align='left'>$linha[temp_momento]</td>
						<td align='left'>$linha[temp_minima]</td>
						<td>$periodo</td>
						<td>$linha[observacoes]</td>
					  </tr>";
			}
?>