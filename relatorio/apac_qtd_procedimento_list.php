<!-- --------------  Funções javascript  --------------- -->
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
}
.letra
{
	font-size:12px;
	font-family:Tahoma,Arial;
}
.bold
{
	font-weight: bold;
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
if( !empty($uni_codigo) )
{
	$sql_unidade = "SELECT medico.med_nome FROM medico WHERE med_codigo = '".$uni_codigo."'";
	$res_unidade = pg_query($sql_unidade);
	$row_unidade = pg_fetch_array($res_unidade);
	$Unidade = $row_unidade[0];
}
else
{
	$Unidade = "TODOS";
}

$titulo = html_entity_decode("APAC - QUANTIDADE DE PROCEDIMENTOS POR MUNICÍPIO POR PRESTADOR");    //       NOME DO RELATÓRIO

//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtIni, $dtFin, $Unid, $tpCab, $dados_compet)
{
	//---------  Cabeçalho do Relatorio  ----------------->
	if ($tpCab == 1)
	{
		include "cabecalho.php";
	}
	//---------  Cabeçalho dos Dados  ----------------->
	if ($tpCab == 0)
	{
		echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=0 leftmargin=0>\n";
	}
}

//----------------  Rotina de Impressão  ------------------>

/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/

$lin=999;
if( !empty($pac_codigo) && !empty($uni_codigo) )
{
	$condicao = "AND";
}
else
{
	$condicao = "OR";
}

if(!empty($proc_codigo))
{
	$and = " AND apac_procedimento.proc_codigo = $proc_codigo ";
}
	

if( !empty($mes) )
{
	$stmt = " AND apac.apac_mes_competencia = $mes AND apac.apac_ano_competencia = $ano ";
	$dados_compet = "COMPET&Ecirc;NCIA: ".$mes."/".$ano;
}
elseif( !empty($dt_inicial) && !empty($dt_final) )
{
	$stmt = " AND ( apac.apac_periodo_validade >= '$dt_inicial' 
		AND apac.apac_periodo_validade <= '$dt_final' ) ";
	$dtIni = $dt_inicial;
	$dtFin = $dt_final;
}

/*$sql = "SELECT
		apac_procedimento.proc_codigo,
		(CASE WHEN apac.med_sol_codigo IS NOT NULL THEN m0.med_nome
		WHEN apac.med_sol_apac_codigo IS NOT NULL THEN m1.med_nome ELSE 'none'::character varying
		END) AS prestador,
		(case 
		when usuario.usu_end_cidade is not null and length(usuario.usu_end_cidade) > 0 then usuario.usu_end_cidade
		when apac_paciente.pac_end_cidade is not null and length(apac_paciente.pac_end_cidade) > 0 then apac_paciente.pac_end_cidade 
		when cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
		else 'SEM CIDADE'
		END) as cidade, count(proc_codigo) as qtde
		FROM apac 
		LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
		LEFT JOIN apac_paciente ON apac_paciente.pac_codigo = apac.pac_apac_codigo
		LEFT JOIN cidade on cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		LEFT JOIN apac_procedimento ON apac_procedimento.apac_codigo = apac.apac_codigo
		LEFT JOIN medico m0 ON m0.med_codigo = apac.med_sol_codigo
		LEFT JOIN apac_medico m1 ON m1.med_codigo = apac.med_sol_apac_codigo
		WHERE apac_procedimento.proc_codigo is not null"; */
	
	
$sql = "SELECT
		(CASE
		WHEN apac_procedimento.proc_codigo IS NOT NULL THEN apac_procedimento.proc_codigo
		ELSE apac_procedimento.proc_apac_codigo
		END) as proc_codigo,
		(CASE WHEN apac.uni_pres_codigo IS NOT NULL THEN u0.uni_desc
		WHEN apac.uni_pres_apac_codigo IS NOT NULL THEN u1.uni_desc
		ELSE 'NENHUM'::character varying
		END) AS prestador,
		(case 
		when usuario.usu_end_cidade is not null and length(usuario.usu_end_cidade) > 0 then upper(usuario.usu_end_cidade)
		when apac_paciente.pac_end_cidade is not null and length(apac_paciente.pac_end_cidade) > 0 then upper(apac_paciente.pac_end_cidade)
		when cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
		else 'SEM CIDADE'
		END) as cidade, count(*) as qtde
		FROM apac 
		LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
		LEFT JOIN apac_paciente ON apac_paciente.pac_codigo = apac.pac_apac_codigo
		LEFT JOIN cidade on cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		LEFT JOIN apac_procedimento ON apac_procedimento.apac_codigo = apac.apac_codigo
		LEFT JOIN unidade u0 ON u0.uni_codigo = apac.uni_pres_codigo
		LEFT JOIN apac_unidade u1 ON u1.uni_codigo = apac.uni_pres_apac_codigo
		WHERE true ";
//	if( !empty($pac_codigo) || !empty($uni_codigo))
if( !empty($pac_codigo) )
{
	$sql .= " AND (case 
		when usuario.usu_end_cidade is not null and length(usuario.usu_end_cidade) > 0 then usuario.usu_end_cidade
		when apac_paciente.pac_end_cidade is not null and length(apac_paciente.pac_end_cidade) > 0 then apac_paciente.pac_end_cidade 
		when cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
		else 'SEM CIDADE'
		END) = '$pac_codigo' ";
	//".$condicao." apac.uni_pres_codigo = '".$uni_codigo."' ) ";
	/*if($condicao == "AND")
	{
		$sql .= " $condicao (CASE WHEN apac.med_sol_codigo IS NOT NULL THEN apac.med_sol_codigo
		WHEN apac.med_sol_apac_codigo IS NOT NULL THEN apac.med_sol_apac_codigo
		ELSE NULL END) = '".$uni_codigo."' ";
	}*/
}

if(!empty($uni_codigo))
{
	/*if($condicao == "AND")
	{
		$sql .= " $condicao (CASE WHEN apac.med_sol_codigo IS NOT NULL THEN apac.med_sol_codigo
		WHEN apac.med_sol_apac_codigo IS NOT NULL THEN apac.med_sol_apac_codigo
		ELSE NULL END) = '".$uni_codigo."' ";
	}*/
		/*$sql .= " AND (CASE WHEN apac.uni_pres_codigo IS NOT NULL THEN u0.uni_codigo
		WHEN apac.uni_pres_apac_codigo IS NOT NULL THEN u1.uni_codigo) = $uni_codigo ";*/
		$sql .= " AND (apac.uni_pres_codigo = $uni_codigo OR apac.uni_pres_apac_codigo = $uni_codigo ) ";
}

$sql .= " ".$stmt." ".$and."
GROUP BY apac_procedimento.proc_codigo, apac_procedimento.proc_apac_codigo, prestador, cidade
ORDER BY cidade, prestador";

/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/
//echo "<pre>$sql</pre>";

$query=pg_query($sql);
$query2=pg_query($sql);
if (pg_num_rows($query) == 0)
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "PRESTADOR    ->".$Unidade."<br>";
    echo "MUNICÍPIO    ->".$pac_codigo."<br>";
}
else
{
	cabeca($titulo, $dt_inicial, $dt_final, $Unidade, 1, $dados_compet);
	$controle = 0;
	$last_municipio = "";
	$last_undiade = "";
	$qtd_procedimentos = 0;
	echo "<table class='letra'>";
	echo "<tr>";
		echo "<td class='bold' width='31%'>";
			echo "PRESTADOR";
		echo "</td>";
		echo "<td class='bold' width='31%'>";
			echo "PROCEDIMENTO";
		echo "</td>";
		echo "<td class='bold' width='31%'>";
			echo "MUNÍCIPIO";
		echo "</td>";
		echo "<td class='bold' width='7%'>";
			echo "QUANTIDADE";
		echo "</td>";
	echo "</tr>";
	
	$qtde_cidade = 0;
	$qtde_total = 0;
	
	$x = 0;
	while( $row = pg_fetch_array($query) )
	{
		$x++;
		
		$sql_procedimento = "SELECT procedimento.proc_nome 
							FROM procedimento
							WHERE proc_codigo = ".$row["proc_codigo"];
		$res_procedimento = pg_query($sql_procedimento);
		$row_procedimento = pg_fetch_array($res_procedimento);
		
		if ($row["cidade"] != $last_cidade && $x > 1)
		{
			//echo "<tr><td colspan='4' style='font-weight:bold;color:blue'> -------- atual : $row[cidade] <-> ultima : $last_cidade  <-> X = $x </td></tr>";
			echo "
			<tr>
				<td class='bold'>
					TOTAL POR CIDADE:
				</td>
				<td valign=\"top\" colspan='3' class='bold'>"
					.$qtde_cidade.
				"</td>
			</tr>";
			$qtde_cidade = 0;
		}
			$qtde_total = $qtde_total + $row["qtde"];
		
			$qtde_cidade = $qtde_cidade + $row["qtde"];
			
		echo "\n\t\t<tr>";
			echo "\n\t\t\t<td width=\"31%\" valign=\"top\">";
				echo $row["prestador"];
			echo "</td>";
			echo "\n\t\t\t<td width=\"31%\" valign=\"top\">";
				echo $row_procedimento[0];
			echo "</td>";
			echo "\n\t\t\t<td width=\"31%\" valign=\"top\">";
				echo $row["cidade"];
			echo "</td>";
			echo "\n\t\t\t<td width=\"7%\" valign=\"top\">";
				echo $row["qtde"];
			echo "</td>";
		echo "\n\t\t\t</tr>";
		
		$last_cidade = $row["cidade"];
		
	}
	echo "
		<tr>
			<td class='bold'>
				TOTAL POR CIDADE:
			</td>
			<td valign=\"top\" colspan='3' class='bold'>"
				.$qtde_cidade.
			"</td>
		</tr>
		<tr>
			<td class='bold'>
				TOTAL GERAL: 
			</td>
			<td valign=\"top\" colspan='3' class='bold'>"
				.$qtde_total.
			"</td>
		</tr>
	</table>";
}
?>
