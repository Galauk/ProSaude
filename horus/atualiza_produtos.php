<?php
set_time_limit("10000000000000000000");
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

// Conexão Banco de dados
pg_query("SET CLIENT_ENCODING=UTF8");
$sqlHorus = "SELECT * FROM horus WHERE 
				trim(hor_codigo) not in (SELECT pro_horus FROM produto where pro_horus is not null or hor_codigo = 'hor_codigo_') order by hor_descricao";
$queryHorus = pg_query($sqlHorus) or die("haha".pg_last_error());
echo "<form method='POST' action='atualiza_produtos_envia.php'>";
echo "<input type='hidden' name='host_correto' value='$host_correto'>";
echo "<input type='hidden' name='porta_correto' value='$porta_correto'>";
echo "<input type='hidden' name='banco_correto' value='$banco_correto'>";
echo "<input type='hidden' name='usuario_correto' value='$usuario_correto'>";
echo "<input type='hidden' name='senha_correto' value='$senha_correto'>";
echo "<table border=1>
	 	<tr>
 			<td width=150>
 				Produto Horus
 			</td>
 			<td>
 				Produto Sistema
 			</td>
 		</tr>";
pg_query("SET CLIENT_ENCODING=LATIN1");
while($regHorus = pg_fetch_array($queryHorus)){
	$array_lista = array();
	$exp = explode(" ",$regHorus[hor_descricao]);
	$string1 = utf8_decode($exp[0]);
	$string2 = utf8_decode($exp[1]);
	$string1 = str_replace(",","",$string1);
	$string2 = str_replace(",","",$string2);
	$strintTotalHorus = $regHorus[hor_descricao];
	$soNumeros = preg_replace("/[^0-9]/","", $strintTotalHorus);
	//PEGAR OS NUMEROS QUE VEM QUE VAI SER EX:5MG e FAZER A 3 CONSULTA QUE VAI SER MUITO PROVAVEL 
	//echo $soNumeros; // 123456
	echo "<tr>
			<td>";
				echo utf8_decode($regHorus[hor_descricao])." ".$regHorus[hor_concentracao]." ".$regHorus[hor_volume]." ".$regHorus[hor_forma_farmaceutica];
	echo"
			</td>
			<td>
				<table>";
	
	$sqlUnidadeMedida = "SELECT * FROM UNIDMEDIDA WHERE umed_nome ilike '%$regHorus[hor_forma_farmaceutica]%'";
	$queryUnidadeMedida = pg_query($sqlUnidadeMedida);
	$regUnidadeMedida = pg_fetch_array($queryUnidadeMedida);
	echo "<tr>
			<td>
				<input type='radio' id='teste|$regHorus[hor_codigo][]' name='teste|$regHorus[hor_codigo][]' value='".utf8_decode(($regHorus[hor_descricao] == "null" ? "" : "$regHorus[hor_descricao]")." ".($regHorus[hor_volume] == "null" ? "" : "$regHorus[hor_volume]")." ".($regHorus[hor_concentracao] == "null" ? "" : "$regHorus[hor_concentracao]"))."|".($regUnidadeMedida[umed_codigo] != "" ? "$regUnidadeMedida[umed_codigo]" : "17")."|'>
				<label for='teste|$regHorus[hor_codigo][]'><b>Novo</b></label>
				
			</td>
		  </tr>";
	$sqlProduto = "SELECT * FROM produto where pro_nome ilike retira_acentos('%$string1%')";
	$queryProduto = pg_query($sqlProduto);
	while($regProduto = pg_fetch_array($queryProduto)){
		
		$sqlProdutoBemProvavel = "SELECT * FROM produto where pro_nome ilike retira_acentos('%$string1 $string2%')";
		$queryProdutoBemProvavel = pg_query($sqlProdutoBemProvavel);
		
		$sqlProdutoMuitoProvavel = "SELECT * FROM produto where pro_nome ilike retira_acentos('%$string1 $string2%') and pro_nome ilike '%$soNumeros%'";
		$queryProdutoMuitoProvavel = pg_query($sqlProdutoMuitoProvavel);
		
		while($regProdutoMuitoProvavel = pg_fetch_array($queryProdutoMuitoProvavel)){
			if(!in_array($regProdutoMuitoProvavel[pro_codigo],$array_lista)){
			echo "<tr>
					<td>
						<input type='radio' id='red|$regHorus[hor_codigo][]' name='teste|$regHorus[hor_codigo][]' value='$regProdutoMuitoProvavel[pro_codigo]'>
						<label for='red|$regHorus[hor_codigo][]'><font  color='red'>$regProdutoMuitoProvavel[pro_nome]   ------- Muito Provavel $regProdutoMuitoProvavel[pro_codigo]</font></label>
						
					</td>
				  </tr>";
			}
				array_push($array_lista,$regProdutoMuitoProvavel[pro_codigo]);
		}
		
		
		while($regProdutoBemProvavel = pg_fetch_array($queryProdutoBemProvavel)){
			
			if(!in_array($regProdutoBemProvavel[pro_codigo],$array_lista)){
				
				echo "<tr>
						<td>
							<input type='radio' id='blue|$regHorus[hor_codigo][]' name='teste|$regHorus[hor_codigo][]' value='$regProdutoBemProvavel[pro_codigo]'> 
							<label for='blue|$regHorus[hor_codigo][]'><font color='blue'>$regProdutoBemProvavel[pro_nome]   ------- Bem Provavel $regProdutoBemProvavel[pro_codigo]</font></label>
							
						</td>
					  </tr>";
			}
				$soNumeros = preg_replace("/[^0-9]/","", $regProdutoBemProvavel[pro_nome]);
				array_push($array_lista,$regProdutoBemProvavel[pro_codigo]);
		}
		if(!in_array($regProduto[pro_codigo],$array_lista)){
			echo "<tr>
					<td>
						<input type='radio' id='green|$regHorus[hor_codigo][]' name='teste|$regHorus[hor_codigo][]' value='$regProduto[pro_codigo]'> 
						<label for='green|$regHorus[hor_codigo][]'><font color='green'>$regProduto[pro_nome]   -------  Provavel Apenas---------- $regProduto[pro_codigo]</font></label>
						
					</td>
				  </tr>";
		}
		array_push($array_lista,$regProduto[pro_codigo]);
		//echo "<pre>".print_r($array_lista,1);
	}
	echo "</table>";
}
echo "</table>";
echo "<input type=submit value='cadastrar'>";
echo "</form>";
