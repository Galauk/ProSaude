<?php
/**
 * @brief Separando por faixa etaria, arrumando o calculo das faixas
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

?>
<style>
	.right
	{
		text-align: right;
	}
	table {
		font-size: 12px;
	}
</style>
<?

// --- > parametros para o relatorio
$periodo_ini = $_GET["periodo_ini"];
$periodo_fim = $_GET["periodo_fim"];
$compet_mes = $_GET["mes"];
$compet_ano = $_GET["ano"];
$acao = $_GET["acao"];
// ---> fim parametros

$Tit="Internacoes por faixa etaria, sexo, municipio, por competencia ou periodo";
$dtIni=$periodo_ini;
$dtFin=$periodo_fim;

if ($acao == "periodo")
	$dados_compet = "PERIODO DE $periodo_ini A $periodo_fim";
else
	$dados_compet = "COMPETENCIA DE $compet_mes/$compet_ano";

//$btprint=1;
include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

if($_GET["municipio"] != -1)
{
	$and_select = " AND B.cid_codigo_ibge = $_GET[municipio] ";
}

//--- transforma data 
	list($dia,$mes,$ano)=split("/",$periodo_ini);
	$periodo_ini=array($ano,$mes,$dia);
	$periodo_ini=implode("-",$periodo_ini);
	list($dia,$mes,$ano)=split("/",$periodo_fim);
	$periodo_fim=array($ano,$mes,$dia);
	$periodo_fim=implode("-",$periodo_fim);
// ----- fim transforma data

/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/

	/*Faixas Etárias*/
	/*
		todas = $_GET["faixa_etaria"] -> com valor -1
		0 a 1 ano = $_GET["faixa_etaria"] -> com valor 0
		1 a 5 anos = $_GET["faixa_etaria"] -> com valor 1
		5 a 12 anos = $_GET["faixa_etaria"] -> com valor 5
		12 a 19 anos = $_GET["faixa_etaria"] -> com valor 12
		19 a 25 anos = $_GET["faixa_etaria"] -> com valor 19
		25 a 49 anos = $_GET["faixa_etaria"] -> com valor 25
		49 a 65 anos = $_GET["faixa_etaria"] -> com valor 49
		acima de 65 anos = $_GET["faixa_etaria"] -> com valor 65
	*/
	
	if($_GET["faixa_etaria"] != -1)
	{
		$and_query = " AND
			(
				CASE WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 1 THEN 0
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 5 THEN 1
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 12 THEN 5 
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 19 THEN 12
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 25 THEN 19
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 49 THEN 25
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 65 THEN 49
				WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 65 THEN 65
				END
			) = $_GET[faixa_etaria] "; 
	}


if ($acao=="periodo") {
	$sql = "SELECT b.cid_nome, b.cid_codigo
			FROM usuario a, cidade b, aih c
			WHERE c.usu_codigo = a.usu_codigo
			AND b.cid_codigo_ibge = a.muni_cd_cod_ibge_resid
			AND c.aih_dataini BETWEEN '$periodo_ini' AND '$periodo_fim'
			AND c.aih_ativo = 'S'
			$and_select
			GROUP BY b.cid_nome, b.cid_codigo
			ORDER BY b.cid_nome ASC";
	$query = db_query($sql);
	
	/**
	 * Gambiarra Mortal, cuidado
	 */
	
	// Inicializa as variaveis
	$faixa_masc_1 = 0;
	$faixa_masc_2 = 0;
	$faixa_masc_3 = 0;
	$faixa_masc_4 = 0;
	$faixa_masc_5 = 0;
	$faixa_masc_6 = 0;
	$faixa_masc_7 = 0;
	$faixa_masc_8 = 0;
	
	$faixa_fem_1 = 0;
	$faixa_fem_2 = 0;
	$faixa_fem_3 = 0;
	$faixa_fem_4 = 0;
	$faixa_fem_5 = 0;
	$faixa_fem_6 = 0;
	$faixa_fem_7 = 0;
	$faixa_fem_8 = 0;
	
	echo "
	<table cellspacing=0 cellpadding=0 border=0 width=100%>
		<tr>
			<td width=\"200\">
				C&oacute;d. Do Procedimento
			</td>
			<td>
				Munic&iacute;pio
			</td>
			<td width=\"50\">
				Sexo
			</td>
			<td width=\"150\">
				Faixa et&aacute;ria
			</td>
			<td width=\"50\">
				Quantidade
			</td>
		</tr>";
	
	// A gambiarra é tão foda que eu nem vou por comentário
	while ($row = pg_fetch_array($query)) {
		$sql2 = "SELECT count(c.usu_codigo) AS qtd, a.usu_sexo, b.cid_nome, b.cid_codigo, d.proc_classificacao_sus as proc_codigo,
				(CASE WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 1 THEN '0 a 1 ano '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 5 THEN '1 a 5 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 12 THEN '5 a 12 anos ' 
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 19 THEN '12 a 19 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 25 THEN '19 a 25 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 49 THEN '25 a 49 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 65 THEN '49 a 65 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 65 THEN 'Acima de 65 anos '
				 END) AS faixa_etaria,
				 (CASE WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 1 THEN 0
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 5 THEN 1
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 12 THEN 5
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 19 THEN 12
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 25 THEN 19
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 49 THEN 25
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 65 THEN 49
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 65 THEN 65
				 END) AS faixa_etaria_num
				FROM usuario a, cidade b, aih c, procedimento d
				WHERE c.usu_codigo = a.usu_codigo
				AND c.aih_desc_proc_soli = d.proc_codigo
				AND b.cid_codigo_ibge = a.muni_cd_cod_ibge_resid
				AND c.aih_dataini BETWEEN '$periodo_ini' AND '$periodo_fim'
				AND b.cid_codigo = $row[cid_codigo]
				AND c.aih_ativo = 'S'
				$and_query
				GROUP BY a.usu_sexo, d.proc_classificacao_sus, b.cid_nome, faixa_etaria, faixa_etaria_num, b.cid_codigo
				ORDER BY b.cid_nome, faixa_etaria_num, faixa_etaria, d.proc_classificacao_sus, a.usu_sexo ASC";
		
		/*echo "<pre>";
			echo $sql2;
		echo "</pre>";*/
		
		$query2 = db_query($sql2);
		
		while ($row2 = pg_fetch_array($query2)) {
			// Gambiarra nunca acaba
			if($row2[faixa_etaria_num] == 0) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_1 += $row2[qtd];
				} else {
					$faixa_fem_1 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 1) {
				if ($row2[usu_sexo] == "M") { 
					$faixa_masc_2 += $row2[qtd];
				} else {
					$faixa_fem_2 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 5) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_3 += $row2[qtd];
				} else {
					$faixa_fem_3 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 12) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_4 += $row2[qtd];
				} else {
					$faixa_fem_4 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 19) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_5 += $row2[qtd];
				} else {
					$faixa_fem_5 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 25) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_6 += $row2[qtd];
				} else {
					$faixa_fem_6 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 49) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_7 += $row2[qtd];
				} else {
					$faixa_fem_7 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 65) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_8 += $row2[qtd];
				} else {
					$faixa_fem_8 += $row2[qtd];
				}
			}			
			
			echo "
				<tr>
					<td>
						$row2[proc_codigo]
					</td>
					<td>
						$row[cid_nome]
					</td>
					<td>
						$row2[usu_sexo]
					</td>
					<td>
						$row2[faixa_etaria]
					</td>
					<td>
						$row2[qtd]
					</td>
				</tr>
			";
			
		}
	}
	
	echo "
			<tr>
				<td colspan='5'>
					<hr />
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% align=center>
			<tr>
				<td width=\"100\">
					<b>Total da faixa:</b>
				</td>
				<td width=\"175\" class='right'>
					0 a 1 ano =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_1 + $faixa_fem_1,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					1 a 5 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_2 + $faixa_fem_2,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					5 a 12 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_3 + $faixa_fem_3,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					12 a 19 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_4 + $faixa_fem_4,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					19 a 25 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_5 + $faixa_fem_5,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					25 a 49 anos =&nbsp;
				</td>
				<td>",$faixa_masc_6 + $faixa_fem_6,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					49 a 65 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_7 + $faixa_fem_7,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					acima de 65 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_8 + $faixa_fem_8,"</td>
			</tr>
			<tr>
				<td colspan='4'>
					<hr/>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% align=center>
			<tr>
				<td width='160'>
					<b>Total de Procedimentos: </b>
				</td>
				<td>
					<b>",$faixa_masc_1+$faixa_masc_2+$faixa_masc_3+$faixa_masc_4+$faixa_masc_5+$faixa_masc_6+$faixa_masc_7+$faixa_masc_8+$faixa_fem_1+$faixa_fem_2+$faixa_fem_3+$faixa_fem_4+$faixa_fem_5+$faixa_fem_6+$faixa_fem_7+$faixa_fem_8,"</b>
				</td>
			</tr>			
		</table>";
	
} else {
	$sql = "SELECT b.cid_nome, b.cid_codigo
			FROM usuario a, cidade b, aih c
			WHERE c.usu_codigo = a.usu_codigo
			AND b.cid_codigo_ibge = a.muni_cd_cod_ibge_resid
			AND c.aih_mes_compet = $compet_mes AND c.aih_ano_compet = $compet_ano
			AND c.aih_ativo = 'S'
			$and_select
			GROUP BY b.cid_nome, b.cid_codigo
			ORDER BY b.cid_nome ASC";
	$query = db_query($sql);
	
	/**
	 * Gambiarra Mortal, cuidado
	 */
	
	// Inicializa as variaveis
	$faixa_masc_1 = 0;
	$faixa_masc_2 = 0;
	$faixa_masc_3 = 0;
	$faixa_masc_4 = 0;
	$faixa_masc_5 = 0;
	$faixa_masc_6 = 0;
	$faixa_masc_7 = 0;
	$faixa_masc_8 = 0;
	
	$faixa_fem_1 = 0;
	$faixa_fem_2 = 0;
	$faixa_fem_3 = 0;
	$faixa_fem_4 = 0;
	$faixa_fem_5 = 0;
	$faixa_fem_6 = 0;
	$faixa_fem_7 = 0;
	$faixa_fem_8 = 0;
	
	echo "
	<table cellspacing=0 cellpadding=0 border=0 width=100%>
		<tr>
			<td width=\"200\">
				C&oacute;d. Do Procedimento
			</td>
			<td>
				Munic&iacute;pio
			</td>
			<td width=\"50\">
				Sexo
			</td>
			<td width=\"150\">
				Faixa et&aacute;ria
			</td>
			<td width=\"50\">
				Quantidade
			</td>
		</tr>";
	
	// A gambiarra é tão foda que eu nem vou por comentário
	while ($row = pg_fetch_array($query)) {
		$sql2 = "SELECT count(c.usu_codigo) AS qtd, a.usu_sexo, b.cid_nome, b.cid_codigo, d.proc_classificacao_sus as proc_codigo,
				(CASE WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 1 THEN '0 a 1 ano '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 5 THEN '1 a 5 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 12 THEN '5 a 12 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 19 THEN '12 a 19 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 25 THEN '19 a 25 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 49 THEN '25 a 49 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 65 THEN '49 a 65 anos '
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 65 THEN 'Acima de 65 anos '
				 END) AS faixa_etaria,
				 (CASE WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 1 THEN 0
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 5 THEN 1
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 12 THEN 5
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 19 THEN 12
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 25 THEN 19
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 49 THEN 25
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) <= 65 THEN 49
				 WHEN EXTRACT(YEAR FROM age(now(),a.usu_datanasc)) > 65 THEN 65
				 END) AS faixa_etaria_num
				FROM usuario a, cidade b, aih c, procedimento d
				WHERE c.usu_codigo = a.usu_codigo
				AND c.aih_desc_proc_soli = d.proc_codigo
				AND b.cid_codigo_ibge = a.muni_cd_cod_ibge_resid
				AND c.aih_mes_compet = $compet_mes AND c.aih_ano_compet = $compet_ano
				AND b.cid_codigo = $row[cid_codigo]
				AND c.aih_ativo = 'S'
				$and_query
				GROUP BY a.usu_sexo, d.proc_classificacao_sus, b.cid_nome, faixa_etaria, faixa_etaria_num, b.cid_codigo
				ORDER BY b.cid_nome, faixa_etaria_num, faixa_etaria, d.proc_classificacao_sus, a.usu_sexo ASC";
		$query2 = db_query($sql2);
		
		while ($row2 = pg_fetch_array($query2)) {
			// Gambiarra nunca acaba
			if($row2[faixa_etaria_num] == 0) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_1 += $row2[qtd];
				} else {
					$faixa_fem_1 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 1) {
				if ($row2[usu_sexo] == "M") { 
					$faixa_masc_2 += $row2[qtd];
				} else {
					$faixa_fem_2 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 5) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_3 += $row2[qtd];
				} else {
					$faixa_fem_3 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 12) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_4 += $row2[qtd];
				} else {
					$faixa_fem_4 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 19) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_5 += $row2[qtd];
				} else {
					$faixa_fem_5 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 25) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_6 += $row2[qtd];
				} else {
					$faixa_fem_6 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 49) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_7 += $row2[qtd];
				} else {
					$faixa_fem_7 += $row2[qtd];
				}
			}
			
			if($row2[faixa_etaria_num] == 65) {
				if ($row2[usu_sexo] == "M") {
					$faixa_masc_8 += $row2[qtd];
				} else {
					$faixa_fem_8 += $row2[qtd];
				}
			}
			
			echo "
				<tr>
					<td>
						$row2[proc_codigo]
					</td>
					<td>
						$row[cid_nome]
					</td>
					<td>
						$row2[usu_sexo]
					</td>
					<td>
						$row2[faixa_etaria]
					</td>
					<td>
						$row2[qtd]
					</td>
				</tr>
			";
			
		}
		
	}
	echo "
			<tr>
				<td colspan='5'>
					<hr />
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% align=center>
			<tr>
				<td width=\"100\">
					<b>Total da faixa:</b>
				</td>
				<td width=\"175\" class='right'>
					0 a 1 ano =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_1 + $faixa_fem_1,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					1 a 5 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_2 + $faixa_fem_2,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					5 a 12 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_3 + $faixa_fem_3,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					12 a 19 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_4 + $faixa_fem_4,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					19 a 25 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_5 + $faixa_fem_5,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					25 a 49 anos =&nbsp;
				</td>
				<td>",$faixa_masc_6 + $faixa_fem_6,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					49 a 65 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_7 + $faixa_fem_7,"</td>
			</tr>
			<tr>
				<td></td>
				<td class='right'>
					acima de 65 anos =&nbsp;
				</td>
				<td colspan='2'>",$faixa_masc_8 + $faixa_fem_8,"</td>
			</tr>
			<tr>
				<td colspan='4'>
					<hr/>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0 border=0 width=100% align=center>
			<tr>
				<td width='160'>
					<b>Total de Procedimentos: </b>
				</td>
				<td>
					<b>",$faixa_masc_1+$faixa_masc_2+$faixa_masc_3+$faixa_masc_4+$faixa_masc_5+$faixa_masc_6+$faixa_masc_7+$faixa_masc_8+$faixa_fem_1+$faixa_fem_2+$faixa_fem_3+$faixa_fem_4+$faixa_fem_5+$faixa_fem_6+$faixa_fem_7+$faixa_fem_8,"</b>
				</td>
			</tr>			
		</table>";
}
?>

