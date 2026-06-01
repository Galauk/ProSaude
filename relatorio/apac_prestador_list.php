<!-- --------------  Funções javascript  --------------- -->
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
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
//-------------------  require_onces  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

$uni_codigo_tudo 	= @ $_GET['uni_codigo'];
$uni_codigo_arr		= split( ';', $uni_codigo_tudo );
$uni_codigo			= (int) trim($uni_codigo_arr[0]);
$uni_tabela			= trim($uni_codigo_arr[1]);

//var_dump($uni_codigo, $uni_tabela);
//die;

//----------------  Monta Dados Recebidos  ---------------->
//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "Agente->".$agt_codigo."<br>";
if( !empty($uni_codigo) )
{
	if( $uni_tabela == 'medico' )
		$sql_unidade = "SELECT med_nome FROM medico WHERE med_codigo = {$uni_codigo}";
	else
		$sql_unidade = "SELECT uni_desc FROM apac_unidade WHERE uni_codigo = {$uni_codigo} ";

	//$res_unidade = db_query($sql_unidade);
	//$row_unidade = pg_fetch_array($res_unidade);
	//$Unidade = $row_unidade[0];
	$Unidade = db_get($sql_unidade);
}
else
{
	$Unidade = "TODOS";
}

$titulo = "APAC por Prestador";    //       NOME DO RELATÓRIO

//$dt_final = date("d/m/Y");
//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtFin, $dtIni, $dados_compet, $Unid, $tpCab, $btprint)
{
	//---------  Cabeçalho do Relatorio  ----------------->
	if ($tpCab == 1)
	{
		include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
	}
	//---------  Cabeçalho dos Dados  ----------------->
	if ($tpCab == 0)
	{
		echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=1 border=0 topmargin=0 leftmargin=0>\n";
		echo " <tr>\n";
		echo "  <td width=100 style=\"font-weight:bold\">N&uacute;mero de APAC</td>\n";
		echo "  <td width=250 style=\"font-weight:bold\">Procedimento</td>\n";
		echo "  <td width=250 style=\"font-weight:bold\">Paciente</td>\n";
		echo " </tr>\n";
	}
}

//----------------  Rotina de Impressão  ------------------>
$mudou=-1;
$lin=999;

$dt_ini = $_GET["dt_ini"];
$dt_fim = $_GET["dt_fim"];
if( !empty($dt_ini) && empty($dt_fim) )
{
	$dt_fim = date("d/m/Y");
}
$mes = $_GET["mes"];
$ano = $_GET["ano"];
if( empty($ano) )
{
	$ano = date("Y");
}
if( ! empty($mes) && ! empty($ano) )
{
	$dados_compet = "COMPETENCIA: ".$_GET["mes"]."/".$_GET["ano"];
}
/*
$sql = "SELECT apac.apac_num, usuario.usu_nome, apac.apac_codigo, apac.uni_pres_codigo
	FROM apac
	LEFT JOIN usuario on usuario.usu_codigo = apac.pac_codigo
	WHERE true ";
*/

/*$sql = "SELECT
	(CASE WHEN app.pac_nome IS NOT NULL THEN app.pac_nome
	WHEN usu_nome IS NOT NULL THEN usu_nome ELSE 'none' END) AS nome,

	(CASE WHEN tab1.pac_codigo IS NULL THEN pac_apac_codigo
	WHEN pac_apac_codigo IS NULL THEN tab1.pac_codigo ELSE NULL END) AS codigo,

	(CASE WHEN proc.proc_codigo IS NULL THEN proc_apac_codigo
	WHEN proc_apac_codigo IS NULL THEN proc.proc_codigo ELSE NULL END) AS codigo_procedimento,

	apac_num, tab1.apac_codigo,

	(CASE WHEN tab1.med_sol_codigo IS NOT NULL THEN m0.med_nome
	WHEN tab1.med_sol_apac_codigo IS NOT NULL THEN m1.med_nome ELSE 'none'::character varying
	END) AS prestador

	FROM apac AS tab1
	LEFT JOIN usuario AS usu ON usu_codigo=tab1.pac_codigo or usu_codigo=tab1.pac_apac_codigo
	LEFT JOIN apac_paciente AS app ON app.pac_codigo=tab1.pac_apac_codigo
	LEFT JOIN apac_procedimento AS apacpro ON apacpro.apac_codigo=tab1.apac_codigo
	LEFT JOIN procedimento AS proc ON proc.proc_codigo=apacpro.proc_codigo
	LEFT JOIN medico m0 ON m0.med_codigo = tab1.med_sol_codigo
	LEFT JOIN apac_medico m1 ON m1.med_codigo = tab1.med_sol_apac_codigo
	WHERE true ";*/

$sql = "SELECT

	(CASE WHEN app.pac_nome IS NOT NULL THEN app.pac_nome
	WHEN usu_nome IS NOT NULL THEN usu_nome ELSE 'none' END) AS nome,

	(CASE WHEN tab1.pac_codigo IS NULL THEN pac_apac_codigo
	WHEN pac_apac_codigo IS NULL THEN tab1.pac_codigo ELSE NULL END) AS codigo,

	(CASE WHEN proc.proc_codigo IS NULL THEN proc_apac_codigo
	WHEN proc_apac_codigo IS NULL THEN proc.proc_codigo ELSE NULL END) AS codigo_procedimento,

	apac_num, tab1.apac_codigo,

	(CASE WHEN tab1.uni_pres_codigo IS NOT NULL THEN u0.uni_desc
	WHEN tab1.uni_pres_apac_codigo IS NOT NULL THEN u1.uni_desc
	ELSE 'NENHUM'::character varying
	END) AS prestador

	FROM apac AS tab1

	LEFT JOIN usuario AS usu ON usu_codigo=tab1.pac_codigo or usu_codigo=tab1.pac_apac_codigo
	LEFT JOIN apac_paciente AS app ON app.pac_codigo=tab1.pac_apac_codigo
	LEFT JOIN apac_procedimento AS apacpro ON apacpro.apac_codigo=tab1.apac_codigo
	LEFT JOIN procedimento AS proc ON proc.proc_codigo=apacpro.proc_codigo

	LEFT JOIN unidade u0 ON u0.uni_codigo = tab1.uni_pres_codigo
	LEFT JOIN apac_unidade u1 ON u1.uni_codigo = tab1.uni_pres_apac_codigo

	WHERE true ";

if( !empty($uni_codigo) )
{
	//$sql .= "AND (CASE WHEN tab1.med_sol_codigo IS NOT NULL THEN tab1.med_sol_codigo
		//WHEN tab1.med_sol_apac_codigo IS NOT NULL THEN tab1.med_sol_apac_codigo
		//ELSE NULL END) = '".$uni_codigo."'";

	$sql .= "AND (CASE WHEN tab1.uni_pres_codigo IS NOT NULL THEN tab1.uni_pres_codigo
		WHEN tab1.uni_pres_apac_codigo IS NOT NULL THEN tab1.uni_pres_apac_codigo
		ELSE NULL END) = $uni_codigo";
}
if( ! empty($mes) )
{
	$sql .= "AND tab1.apac_mes_competencia = '".$mes."'
		AND tab1.apac_ano_competencia = '".$ano."'";
}
if( ! empty($dt_ini) )
{
	$sql .= "AND tab1.apac_dt_cadastro >= '".$dt_ini."'
		AND tab1.apac_dt_cadastro <= '".$dt_fim."'";
}

/*GROUP BY prestador, nome, codigo, apac_num, tab1.apac_codigo, codigo_procedimento*/
$sql .= " ORDER BY prestador, tab1.apac_num, nome";

//print "<pre>$sql</pre>";

$query = db_query($sql);

if (pg_num_rows($query) == 0)
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "PRESTADOR      ->".$Unidade."<br>";
}
else
{
	cabeca($titulo, $dt_fim, $dt_ini, $dados_compet, $Unidade, '1');
	$last_unid = "";
	while($row=pg_fetch_row($query))
	{
		/*
		$sql_unidade = "SELECT medico.med_nome FROM medico WHERE med_codigo = '".$row[5]."'";
		$res_unidade = db_query($sql_unidade);
		$row_unidade = pg_fetch_array($res_unidade);
		*/
		if( $last_unid != $row[5] )

		{
			if( $last_unid != "" )
			{
				echo "<td colspan=\"3\"></td></table>";
			}
			if($mudou!=-1)
			{

				echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\"><tr><td><b>Total de apac da unidade = ".$mudou."</b></td></tr></td></table>";
				echo "<hr>";
				$total_geral+=$mudou;
			}
			echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=1 border=0 topmargin=0 leftmargin=0>\n";
			echo "<tr>\n";
			echo "	<td valign=\"top\"><b>".$row[5]."</b></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			cabeca($titulo, $dt_final, $Unidade, '0');
			$mudou=0;
		}

		if ($comp_apac!=$row[3])
		{
			$mudou++;
		}
		echo "<tr>\n";
		echo "	<td width=140 valign=\"top\">".$row[3]."&nbsp;</td>\n";
		echo "	<td width=330>";
		$comp_apac=$row[3];
		//Procedimentos
		//$sql_procedimento = "SELECT proc.proc_nome, proc.proc_codigo
		$sql_procedimento = "SELECT proc.proc_nome, proc.proc_classificacao_sus
					FROM procedimento AS proc
					WHERE proc.proc_codigo = ".$row[2];
		$res_procedimento = db_query($sql_procedimento);
		if( pg_num_rows($res_procedimento) == 0 )
		{
			$sql_procedimento = "SELECT proc.proc_nome, proc.proc_numero
				FROM apac_procedimento_cad AS proc
				WHERE proc.proc_codigo = ".$row[2];
			$res_procedimento = db_query($sql_procedimento);
		}
		$row_procedimento = pg_fetch_array($res_procedimento);
		echo $row_procedimento[1]." - ".$row_procedimento[0]."<br>";
		echo "	&nbsp;</td>\n";
		echo "	<td width=250 valign=\"top\">".$row[0]."&nbsp;</td>\n";
		echo "</tr>\n";

		$last_unid = $row[5];
	}

	$total_geral+=$mudou;
	echo "<tr><td colspan='3'>Total de apac do prestador = ".$mudou."</tr></td>";
	echo "<tr><td colspan=3><hr></td></tr>";
//	echo "<tr><td colspan=3><b>Quantidade total de APAC = ".pg_num_rows($query)."</b></td></tr>";
	echo "<tr><td colspan=3><b>Quantidade total de APAC = ".$total_geral."</b></td></tr>";
	echo "</table>";

}
?>
