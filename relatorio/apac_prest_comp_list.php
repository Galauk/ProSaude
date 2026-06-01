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
//-------------------  Includes  -------------------------->
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
//----------------  Monta Dados Recebidos  ---------------->
//echo "INICIAL->".$dt_inicial."<br>";
//echo "FINAL  ->".$dt_final."<br>";
//echo "Agente->".$agt_codigo."<br>";

$uni_codigo_tudo 	= @ $_GET['uni_codigo'];
$uni_codigo_arr		= split( ';', $uni_codigo_tudo );
$uni_codigo			= (int) trim($uni_codigo_arr[0]);
$uni_tabela			= trim($uni_codigo_arr[1]);

if( !empty($uni_codigo) )
{
	
	/*$sql_unidade = "SELECT medico.med_nome FROM medico WHERE med_codigo = '".$uni_codigo."'";
	$res_unidade = db_query($sql_unidade);
	$row_unidade = pg_fetch_array($res_unidade);
	$Unidade = $row_unidade[0];*/
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
$titulo="Numero de APACs por Prestador e Competência";    //       NOME DO RELATÓRIO
//$dt_final = date("d/m/Y");

//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtFin, $Unid, $tpCab, $dados_compet)
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
		echo " <tr>\n";
		echo "  <td width=350 colspan=1 style=\"font-weight:bold\">Prestador</td>\n";
		echo "  <td width=200 colspan=1 style=\"font-weight:bold\">Origem</td>\n";
//		echo "  <td width=150 colspan=1 style=\"font-weight:bold\">Compet&ecirc;ncia</td>\n";
		echo "  <td width=80 colspan=1 style=\"font-weight:bold\">APAC</td>\n";
		echo " </tr>\n";
	}
}

//----------------  Rotina de Impressão  ------------------>

$temp_cidade=''; // criado por Marcos para auxiliar na alternacia das cidades (imprimir apenas 1 vez o nome da cidade
$temp_compet=$_GET["mes_ini"]."/".$_GET["ano_ini"]; // criado por Marcos para auxiliar na alternacia das competencias (imprimir apenas 1 vez a competencia por cidade
$dados_compet = "COMPETENCIA: ".$_GET["mes_ini"]."/".$_GET["ano_ini"];
$lin=999;
$total_por_unidade=0;

/*$sql = "SELECT apac.apac_mes_competencia, apac.apac_ano_competencia,

	(CASE WHEN apac.med_sol_codigo IS NOT NULL THEN m0.med_nome
	WHEN apac.med_sol_apac_codigo IS NOT NULL THEN m1.med_nome ELSE 'none'::character varying
	END) AS prestador,
	
	apac.apac_num,
	(case 
	when usuario.usu_end_cidade is not null  and length(usuario.usu_end_cidade) > 0 then usuario.usu_end_cidade
	when apac_paciente.pac_end_cidade is not null and length(apac_paciente.pac_end_cidade) > 0 then apac_paciente.pac_end_cidade 
	when cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
	else 'SEM CIDADE'
	END) as cidade
	
	FROM apac 
	LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
	LEFT JOIN apac_paciente ON apac_paciente.pac_codigo=apac.pac_apac_codigo
	LEFT JOIN cidade on cidade.cid_codigo_ibge=usuario.muni_cd_cod_ibge_resid
	LEFT JOIN medico m0 ON m0.med_codigo = apac.med_sol_codigo
	LEFT JOIN apac_medico m1 ON m1.med_codigo = apac.med_sol_apac_codigo

	WHERE apac.apac_mes_competencia = '". $_GET["mes_ini"] ."'
	AND apac.apac_ano_competencia = '". $_GET["ano_ini"] ."' ";*/

$sql = "SELECT apac.apac_mes_competencia, apac.apac_ano_competencia,

	(CASE WHEN apac.uni_pres_codigo IS NOT NULL THEN u0.uni_desc
	WHEN apac.uni_pres_apac_codigo IS NOT NULL THEN u1.uni_desc
	ELSE 'NENHUM'::character varying
	END) AS prestador,
	
	apac.apac_num,
	(case 
	when usuario.usu_end_cidade is not null  and length(usuario.usu_end_cidade) > 0 then upper(	usuario.usu_end_cidade)
	when apac_paciente.pac_end_cidade is not null and length(apac_paciente.pac_end_cidade) > 0 then upper(apac_paciente.pac_end_cidade)
	when cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
	else 'SEM CIDADE'
	END) as cidade
	
	FROM apac
	
	LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
	LEFT JOIN apac_paciente ON apac_paciente.pac_codigo=apac.pac_apac_codigo
	LEFT JOIN cidade on cidade.cid_codigo_ibge=usuario.muni_cd_cod_ibge_resid
	
	LEFT JOIN unidade u0 ON u0.uni_codigo = apac.uni_pres_codigo
	LEFT JOIN apac_unidade u1 ON u1.uni_codigo = apac.uni_pres_apac_codigo

	WHERE apac.apac_mes_competencia = '". $_GET["mes_ini"] ."'
	AND apac.apac_ano_competencia = '". $_GET["ano_ini"] ."' ";
	
if( !empty($uni_codigo) )
{
	/*$sql .= "AND (CASE WHEN apac.med_sol_codigo IS NOT NULL THEN apac.med_sol_codigo
		WHEN apac.med_sol_apac_codigo IS NOT NULL THEN apac.med_sol_apac_codigo
		ELSE NULL END) = '".$uni_codigo."'";*/

	$sql .= "AND (CASE WHEN apac.uni_pres_codigo IS NOT NULL THEN apac.uni_pres_codigo
		WHEN apac.uni_pres_apac_codigo IS NOT NULL THEN apac.uni_pres_apac_codigo
		ELSE NULL END) = $uni_codigo";
}
$sql .= " ORDER BY cidade";

//print "<pre>$sql</pre>";

$query=db_query($sql);
$qtd_apac = pg_num_rows($query);
if ( $qtd_apac == 0 )
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "PRESTADOR	->".$Unidade."<br>";
    echo "COMPETÊNCIA	->".$_GET["mes_ini"]."/".$_GET["ano_ini"]."<br>";
}
else
{
	cabeca($titulo, $dt_final, $Unidade, '1', $dados_compet);
	//cabeca($titulo, $dt_final, $Unidade, '0');
	$last_unid = "";
	$while_control = 0;
	while($row=pg_fetch_row($query))
	{
		/*
		$sql_unidade = "SELECT unidade.uni_desc FROM unidade 
				WHERE uni_codigo = '".$row[2]."' 
				GROUP BY unidade.uni_desc ORDER BY unidade.uni_desc";
		$res_unidade = db_query($sql_unidade);
		$row_unidade = pg_fetch_array($res_unidade);
		*/
		if( $last_unid != $row[2] && $last_unid != '' )
		{
			echo "	<tr><td valign='top' colspan='4'><b>Total de APAC da unidade = ".$total_por_unidade."</b></td></tr>\n";
			$total_por_unidade=0;
			echo "	<tr><td valign='top' colspan='4'>&nbsp;</td></tr>\n";
		}	
		$total_por_unidade++;
		
		
		echo "<tr>\n";
		
		if ((( $last_unid!=$row[2] ) &&  ($temp_cidade==$row[4])) &&
		    (( $last_unid==$row[2] ) &&  ($temp_cidade!=$row[4])) )
		{
			echo "<td></td></tr>";
			echo "<tr><td valign='top' colspan='4'><b>Total de APAC da unidade = ".$total_por_unidade."</b></td></tr>\n";
			echo "<tr>";
			$total_por_unidade=0;
		}
		
		if( $last_unid != $row[2] )
		{
			if ($temp_cidade!=$row[4])
			{
				echo "<td></td></tr>";
				echo "</table>";
				cabeca($titulo, $dt_final, $Unidade, '0');
				echo "	<tr><td valign='top' colspan='4'><hr></td></tr>\n";				
				echo "<tr>";
			}					
			echo "	<td valign='top'>".$row[2]."</td>\n";
		}
		else
		{
			echo "	<td valign='top'>&nbsp;</td>\n";
		}
		
//		echo "	<td valign='top'>".$row[4]."</td>\n";
		echo "	<td valign='top'>".$temp_cidade=($temp_cidade!=$row[4] || $row[2]!=$last_unid) ? $row[4] : ''."</td>\n";
//		echo "	<td valign='top'>".$_GET["mes_ini"]."/".$_GET["ano_ini"]."</td>\n";
//		echo "	<td valign='top'>".$temp_compet=($temp_cidade==$row[4]) ? $_GET["mes_ini"]."/".$_GET["ano_ini"] : ''."</td>\n";
//		A linha acima foi comentado conforme solicitacao da geise
		echo "	<td valign='top'>".$row[3]."\n";
		echo "	</td></tr>";
		
		$last_unid = $row[2];
		$temp_cidade=$row[4];
	}
	echo "<td></td></tr>";
	echo "<tr><td valign='top' colspan='4'><b>Total de APAC da unidade = ".$total_por_unidade."</b></td></tr>\n";

	echo "</table>";
	echo "<tr><td colspan='3'><hr></td></tr>";
	echo "<br /><p style=\"font-size:12px;font-family:Tahoma,Arial;\" align='center'><b> Total de ".$qtd_apac." APACs</b></p>";
}
?>
