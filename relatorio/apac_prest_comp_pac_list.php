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
$titulo="Numeros de apacs origem do paciente por prestador e competencia";    //       NOME DO RELATÓRIO
//$dt_final = date("d/m/Y");

//------------------  Funções php  ------------------------>

function cabeca($Tit, $dtFin, $Unid, $tpCab, $dados_compet, $btprint)
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
                echo "  <td width=450 colspan=1 style=\"font-weight:bold\">Prestador</td>\n";
                echo "  <td width=200 colspan=1 style=\"font-weight:bold\">Origem do Paciente</td>\n";
		echo "  <td width=150 colspan=1 style=\"font-weight:bold\" align=\"center\">APAC</td>\n";
		echo " </tr>\n";
	}
}

//----------------  Rotina de Impressão  ------------------>

$lin=999;
/*
SELECT procedimento.proc_sexo, procedimento.proc_idade_minima, 
procedimento.proc_idade_maxima, apac_paciente.pac_end_cidade
FROM procedimento
LEFT JOIN apac_procedimento ON apac_procedimento.proc_codigo = procedimento.proc_codigo
LEFT JOIN apac ON apac.pac_apac_codigo = apac_procedimento.apac_codigo
LEFT JOIN apac_paciente ON apac.pac_apac_codigo = apac_paciente.pac_codigo
WHERE procedimento.proc_codigo = $proc_codigo
*/

/*$sql = "SELECT COUNT(apac.apac_num),
        UPPER(CASE
            WHEN usuario.usu_end_cidade IS NOT NULL AND LENGTH(usuario.usu_end_cidade) > 0 THEN usuario.usu_end_cidade
            WHEN apac_paciente.pac_end_cidade IS NOT NULL AND LENGTH(apac_paciente.pac_end_cidade) > 0 THEN apac_paciente.pac_end_cidade 
            WHEN cidade.cid_nome  IS NOT NULL AND LENGTH(cidade.cid_nome ) > 0 THEN cidade.cid_nome 
            ELSE 'SEM CIDADE'
	END) as cidade,
        
        (CASE WHEN apac.med_sol_codigo IS NOT NULL THEN m0.med_nome
	WHEN apac.med_sol_apac_codigo IS NOT NULL THEN m1.med_nome ELSE 'none'::character varying
	END) AS prestador
	
        FROM apac
        LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
        LEFT JOIN apac_paciente ON apac_paciente.pac_codigo=apac.pac_apac_codigo
        LEFT JOIN cidade on cidade.cid_codigo_ibge=usuario.muni_cd_cod_ibge_resid
        LEFT JOIN medico m0 ON m0.med_codigo = apac.med_sol_codigo
	LEFT JOIN apac_medico m1 ON m1.med_codigo = apac.med_sol_apac_codigo
	WHERE apac_mes_competencia = '". $_GET["mes_ini"] ."'
	AND apac_ano_competencia = '". $_GET["ano_ini"] ."' ";*/


$sql = "SELECT COUNT(apac.apac_num),
        UPPER(CASE
            WHEN usuario.usu_end_cidade IS NOT NULL AND LENGTH(usuario.usu_end_cidade) > 0 THEN usuario.usu_end_cidade
            WHEN apac_paciente.pac_end_cidade IS NOT NULL AND LENGTH(apac_paciente.pac_end_cidade) > 0 THEN apac_paciente.pac_end_cidade 
            WHEN cidade.cid_nome  IS NOT NULL AND LENGTH(cidade.cid_nome ) > 0 THEN cidade.cid_nome 
            ELSE 'SEM CIDADE'
		END) as cidade,
        
        (CASE WHEN apac.uni_pres_codigo IS NOT NULL THEN u0.uni_desc
		WHEN apac.uni_pres_apac_codigo IS NOT NULL THEN u1.uni_desc
		ELSE 'NENHUM'::character varying
		END) AS prestador
	
        FROM apac
        
		LEFT JOIN usuario ON usuario.usu_codigo = apac.pac_codigo
		LEFT JOIN apac_paciente ON apac_paciente.pac_codigo=apac.pac_apac_codigo
		LEFT JOIN cidade on cidade.cid_codigo_ibge=usuario.muni_cd_cod_ibge_resid
		
		LEFT JOIN unidade u0 ON u0.uni_codigo = apac.uni_pres_codigo
		LEFT JOIN apac_unidade u1 ON u1.uni_codigo = apac.uni_pres_apac_codigo
			
		WHERE apac_mes_competencia = '". $_GET["mes_ini"] ."'
		AND apac_ano_competencia = '". $_GET["ano_ini"] ."' ";

if( !empty($uni_codigo) )
{
	$sql .= "AND (CASE WHEN apac.med_sol_codigo IS NOT NULL THEN apac.med_sol_codigo
		WHEN apac.med_sol_apac_codigo IS NOT NULL THEN apac.med_sol_apac_codigo
		ELSE NULL END) = '".$uni_codigo."'";
}

$sql .= " GROUP BY prestador, cidade ORDER BY 2 ASC";

//print $sql;
//echo "<pre>$sql</pre>";

$query=pg_query($sql);
$qtd_apac = pg_num_rows($query);
if ( $qtd_apac == 0 )
{
    echo "NÃO FORAM ENCONTRADAS INFORMACOES COM ESTES PARÂMETROS<br><br>";
    echo "PRESTADOR	->".$Unidade."<br>";
    echo "COMPETÊNCIA	->".$_GET["mes_ini"]."/".$_GET["ano_ini"]."<br>";
}
else
{
	$competencia = "COMPETENCIA: ".$_GET["mes_ini"]."/".$_GET["ano_ini"];
        cabeca($titulo, $dt_final, $Unidade, '1', $competencia);
	cabeca($titulo, $dt_final, $Unidade, '0', $competencia);
	$last_unid = "";
        $last_cid = "";
        $qtd_apac = 0;
        $tot_cidade = 0;
	while($row=pg_fetch_row($query))
	{
            if( $last_cid != $row[1] && $last_cid != "" )
            {
                echo "<tr><td colspan='2' align='right'><b>Total da cidade: </b></td>
                <td align='center'><b>$tot_cidade</b></td></tr>";
                $tot_cidade = 0;
                echo "<tr><td colspan='3'><hr></td></tr>";
            }
            echo "<tr>\n";
            
            if( $last_unid != $row[2] )
            {
                echo "<td valign='top'>".$row[2]."</td>\n";
                $last_unid = $row_unidade;
            }
            else
            {
                echo "<td valign='top'>&nbsp;</td>\n";
            }
            if( $last_cid != $row[1] )
            {
                echo "<td valign='bottom'>".$row[1]."</td>\n";
                $last_cid = $row[1];
                $last_unid = "";
            }
            else
            {
                echo "<td valign='top'>&nbsp;</td>\n";
            }
            echo "<td valign='bottom' align='center'>".$row[0]."</td>\n";
            $tot_cidade += $row[0];
            $qtd_apac += $row[0];
            echo "</tr>\n";
	}
        echo "<tr><td colspan='2' align='right'><b>Total da cidade: </b></td>
                <td align='center'><b>$tot_cidade</b></td></tr>";
	echo "</table>";
	echo "<br /><p style=\"font-size:12px;font-family:Tahoma,Arial;\" align='center'><b> Total de apacs emitidas&nbsp;&nbsp;&nbsp;&nbsp;".$qtd_apac."</b></p>";
}
?>
