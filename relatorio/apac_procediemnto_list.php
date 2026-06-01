<!-- --------------  Funções javascript  --------------- -->
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
}
table
{
	font-size: 12px;
}
</style>

<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>

<body>

<?php
//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//----------------  Monta Dados Recebidos  ---------------->

if( !empty( $_GET["proc_codigo"] ) )
{
	$sql_proc = "SELECT procedimento.proc_nome 
		FROM procedimento WHERE proc_codigo = '".$_GET["proc_codigo"]."'";
	$res_proc = pg_query($sql_proc);
	$row_proc = pg_fetch_array($res_proc);
	$Procedimento = $row_proc[0];
}
else
{
	$Procedimento = "TODOS";
}

$Tit = html_entity_decode("PROCEDIMENTOS DA APAC POR FAIXA ETÁRIA");    //       NOME DO RELATÓRIO

//------------------  Funções php  ------------------------>

if($acao == "periodo")
{
	$dtIni = $dt_inicial;
	$dtFin = $dt_final;
} else {
	$dados_compet = "COMPET&Ecirc;NCIA $mes/$ano";
}

//$dtIni, $dtFin, $dados_compet
include "cabecalho.php";


//----------------  Rotina de Impressão  ------------------>
if( !empty($mes) )
{
	$stmt = " AND apac.apac_mes_competencia = $mes AND apac.apac_ano_competencia = $ano ";
}
elseif( !empty($dt_inicial) && !empty($dt_final) )
{
	$stmt = " AND ( apac.apac_periodo_validade >= '$dt_inicial' 
		AND apac.apac_periodo_validade <= '$dt_final' ) ";
}

if($faixa_etaria > -1)
{
	$and_query = " AND (faixa_etaria(usuario.usu_datanasc) = $_GET[faixa_etaria]) ";
}

$dt_idade_i = (date("Y")-$idade_i)."-".date("m-d");
$dt_idade_f = (date("Y")-$idade_f)."-".date("m-d");

$lin = 999;
$sql = "SELECT
		count(apac.apac_codigo) as qtde,
		(CASE WHEN usuario.usu_codigo IS NOT NULL THEN usuario.usu_sexo
		 ELSE apac_paciente.pac_sexo END) AS sexo,
		(CASE
		WHEN usuario.usu_end_cidade IS NOT NULL AND length(usuario.usu_end_cidade) > 0 THEN usuario.usu_end_cidade
		WHEN apac_paciente.pac_end_cidade IS NOT NULL AND length(apac_paciente.pac_end_cidade) > 0 THEN apac_paciente.pac_end_cidade 
		WHEN cidade.cid_nome  IS NOT NULL AND length(cidade.cid_nome) > 0 THEN cidade.cid_nome 
		ELSE 'SEM CIDADE'
		END) AS cidade,
		procedimento.proc_codigo||' / '||procedimento.proc_nome AS procedimento,
		(SELECT faixa_etaria_str(usuario.usu_datanasc)) AS faixa_etaria, 
		(SELECT faixa_etaria(usuario.usu_datanasc)) AS faixa_etaria_num
		FROM procedimento
		LEFT JOIN apac_procedimento ON apac_procedimento.proc_codigo = procedimento.proc_codigo
		LEFT JOIN apac ON apac.apac_codigo = apac_procedimento.apac_codigo
		LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
		LEFT JOIN apac_paciente ON apac_paciente.pac_codigo = apac.pac_apac_codigo
		LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		WHERE true
		$stmt
		$and_query
		";
if( !empty($_GET["proc_codigo"]) )
{
	$sql .= " AND procedimento.proc_codigo = ".$_GET["proc_codigo"]." ";
}
$sql .= "GROUP BY cidade, faixa_etaria_num, faixa_etaria, sexo, procedimento.proc_codigo,
		procedimento.proc_nome
		ORDER BY cidade, faixa_etaria_num, procedimento, faixa_etaria, sexo, procedimento.proc_codigo,
		procedimento.proc_nome";

/*echo "<pre>";
	print_r($_REQUEST);
	echo $sql;
echo "</pre>";*/
$query=pg_query($sql);
$qtd_apac = pg_num_rows($query);
if ( $qtd_apac == 0 )
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "PROCEDIMENTO	->".$Procedimento."<br>";
}
else
{	
	echo "
	<table cellspacing=0 cellpadding=0 border=0 width=100%>
		<tr>
			<td>
				C&oacute;d./ Procedimento
			</td>
			<td width=\"100\">
				Faixa Et&aacute;ria
			</td>
			<td width=\"50\">
				Sexo
			</td>
			<td>
				Munic&iacute;pio
			</td>
			<td width=\"50\">
				Quantidade
			</td>
		</tr>";
	
	while($row = pg_fetch_array($query))
	{
		echo "
			<tr>
				<td>
					$row[procedimento]
				</td>
				<td>
					$row[faixa_etaria]
				</td>
				<td>
					$row[sexo]
				</td>
				<td>
					$row[cidade]
				</td>
				<td>
					$row[qtde]
				</td>
			</tr>
		";
	}
	echo "
		<tr>
			<td colspan='5'>
				<hr />
			</td>
		</tr>
	</table>";
	
	if( !empty($_GET["proc_codigo"]) )
	{
		$and = " AND procedimento.proc_codigo = ".$_GET["proc_codigo"]." ";
	}
	
	//SQL para Total da faixa
	$sql = "SELECT
			count(apac.apac_codigo) as qtde,
			(SELECT faixa_etaria_str(usuario.usu_datanasc)) AS faixa_etaria, 
			(SELECT faixa_etaria(usuario.usu_datanasc)) AS faixa_etaria_num
			FROM procedimento
			LEFT JOIN apac_procedimento ON apac_procedimento.proc_codigo = procedimento.proc_codigo
			LEFT JOIN apac ON apac.apac_codigo = apac_procedimento.apac_codigo
			LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
			LEFT JOIN apac_paciente ON apac_paciente.pac_codigo = apac.pac_apac_codigo
			LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
			WHERE true 
			$stmt
			$and_query
			$and
			GROUP BY faixa_etaria_num, faixa_etaria
			ORDER BY faixa_etaria_num, faixa_etaria";
	
	$exec_sql = db_query($sql);
	
	//echo "<pre>$sql</pre>";
	
	echo "
	<table>
	";
	$x = 0;
	while($row = pg_fetch_array($exec_sql))
	{
		echo "<tr>";
		$x++;
		if($x == 1)
		{
			echo "
			<td>
				Total da Faixa
			</td>
			";
		} else {
			echo "
			<td>
				&nbsp;
			</td>
			";
		}
		echo "
			<td align='right'>
				$row[faixa_etaria] = 
			</td>
			<td>
				$row[qtde]
			</td>
		</td>";
	}
	echo "</table>";
	
}
?>
