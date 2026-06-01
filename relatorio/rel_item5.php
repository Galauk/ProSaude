<?php
/**
 * @version Renato 12/7/2007 - 10:52
 * @author	Anderson - 16/05/2007 10:35
 * @brief	Adaptando a query para os novos filtros
 * @notes
 * adicionada a quantidade total por clinica
 */

/* Relatorio de Causas de internação por municipio e por clinica */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	
$Tit = html_entity_decode("Relat&Oacute;rio de Causa de Internacao por Clinica, por Mun&Iacute;cipio");
//$dtIni=$data_ini;
//$dtFin=$data_fim;
//$btprint=1;

// ---->parametros para o relatorio
if( $_GET["clinica"] != -1 )
{
	$cli = $_GET["clinica"];
}
$municipio = $_GET["municipio"];
//pegar nome para a verificacao do municipio da tabela aih_paciente
if( $municipio != -1 )
{
	$municipio_aih = db_get("SELECT cid_nome FROM cidade WHERE cid_codigo_ibge = $municipio", false);
}
$mes_comp = $_GET['mes_comp'];
$ano_comp = $_GET['ano_comp'];
$dados_compet = "COMPETENCIA: $mes_comp / $ano_comp";


include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

if($municipio != -1)
{
	$sql_stmt = "SELECT SUM(total), descricao, cidade FROM
		(
			(SELECT COUNT(aih.aih_codigo) AS total, clinica.cli_descricao AS descricao,
			UPPER(
			CASE
			WHEN usuario.usu_end_cidade is not null and length(usuario.usu_end_cidade) > 0 then usuario.usu_end_cidade
			WHEN aih_paciente.pac_end_cidade is not null and length(aih_paciente.pac_end_cidade) > 0 then aih_paciente.pac_end_cidade
			WHEN cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
			ELSE 'SEM CIDADE'
			END) as cidade
			FROM aih 
			LEFT JOIN clinica ON clinica.cli_codigo = aih.aih_clinica 
			LEFT JOIN aih_paciente ON aih_paciente.pac_codigo = aih.pac_aih_codigo
			LEFT JOIN usuario ON usuario.usu_codigo = aih.usu_codigo
			LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
			WHERE aih.aih_ativo = 'S' AND aih.aih_mes_compet = $mes_comp AND aih.aih_ano_compet = $ano_comp
			".( $municipio_aih ? " AND aih_paciente.pac_end_cidade = '$municipio_aih' " : "" )."
			".( ( $municipio != -1 ) ? " AND usuario.muni_cd_cod_ibge_resid = '$municipio' " : "" )."
			".( $cli ? " AND aih.aih_clinica = $cli " : "" )."
			GROUP BY cidade, clinica.cli_descricao
			)
			UNION
			(
			SELECT COUNT(aih.aih_codigo) AS total, clinica.cli_descricao AS descricao,
			cidade.cid_nome AS cidade
			FROM aih 
			LEFT JOIN clinica ON clinica.cli_codigo = aih.aih_clinica 
			LEFT JOIN usuario ON usuario.usu_codigo = aih.usu_codigo
			LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
			WHERE aih.aih_ativo = 'S' AND aih.aih_mes_compet = $mes_comp AND aih.aih_ano_compet = $ano_comp
			".( ( $municipio != -1 ) ? " AND usuario.muni_cd_cod_ibge_resid = '$municipio' " : "" )."
			".( $cli ? " AND aih.aih_clinica = $cli " : "" )."
			GROUP BY cidade, clinica.cli_descricao
			)
			ORDER BY 3, 2
		) AS workarround
		
		GROUP BY descricao, cidade
		
		ORDER BY cidade";
} else {
	$sql_stmt = "SELECT SUM(total), descricao, cidade FROM
	(
		(SELECT COUNT(aih.aih_codigo) AS total, clinica.cli_descricao AS descricao,
		UPPER(
		CASE
		WHEN usuario.usu_end_cidade is not null and length(usuario.usu_end_cidade) > 0 then usuario.usu_end_cidade
		WHEN aih_paciente.pac_end_cidade is not null and length(aih_paciente.pac_end_cidade) > 0 then aih_paciente.pac_end_cidade
		WHEN cidade.cid_nome  is not null and length(cidade.cid_nome) > 0 then cidade.cid_nome 
		ELSE 'SEM CIDADE'
		END) as cidade, usuario.muni_cd_cod_ibge_resid as cid
		FROM aih 
		LEFT JOIN clinica ON clinica.cli_codigo = aih.aih_clinica 
		LEFT JOIN aih_paciente ON aih_paciente.pac_codigo = aih.pac_aih_codigo
		LEFT JOIN usuario ON usuario.usu_codigo = aih.usu_codigo
		LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		WHERE aih.aih_ativo = 'S' AND aih.aih_mes_compet = $mes_comp AND aih.aih_ano_compet = $ano_comp
		".( $municipio_aih ? " AND aih_paciente.pac_end_cidade = '$municipio_aih' " : "" )."
		".( ( $municipio != -1 ) ? " AND usuario.muni_cd_cod_ibge_resid = '$municipio' " : "" )."
		".( $cli ? " AND aih.aih_clinica = $cli " : "" )."
		GROUP BY cidade, clinica.cli_descricao, cid
		)
		UNION
		(
		SELECT COUNT(aih.aih_codigo) AS total, clinica.cli_descricao AS descricao,
		cidade.cid_nome AS cidade, usuario.muni_cd_cod_ibge_resid as cid
		FROM aih 
		LEFT JOIN clinica ON clinica.cli_codigo = aih.aih_clinica 
		LEFT JOIN usuario ON usuario.usu_codigo = aih.usu_codigo
		LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		WHERE aih.aih_ativo = 'S' AND aih.aih_mes_compet = $mes_comp AND aih.aih_ano_compet = $ano_comp
		".( ( $municipio != -1 ) ? " AND usuario.muni_cd_cod_ibge_resid = '$municipio' " : "" )."
		".( $cli ? " AND aih.aih_clinica = $cli " : "" )."
		GROUP BY cidade, clinica.cli_descricao, cid
		)
		ORDER BY 3, 2
	) AS a,
	(
		SELECT usuario.muni_cd_cod_ibge_resid as cid
		FROM aih 
		LEFT JOIN clinica ON clinica.cli_codigo = aih.aih_clinica 
		LEFT JOIN aih_paciente ON aih_paciente.pac_codigo = aih.pac_aih_codigo
		LEFT JOIN usuario ON usuario.usu_codigo = aih.usu_codigo
		LEFT JOIN cidade ON cidade.cid_codigo_ibge = usuario.muni_cd_cod_ibge_resid
		WHERE aih.aih_ativo = 'S' AND aih.aih_mes_compet = $mes_comp AND aih.aih_ano_compet = $ano_comp
		".( $municipio_aih ? " AND aih_paciente.pac_end_cidade = '$municipio_aih' " : "" )."
		".( ( $municipio != -1 ) ? " AND usuario.muni_cd_cod_ibge_resid = '$municipio' " : "" )."
		".( $cli ? " AND aih.aih_clinica = $cli " : "" )."
		GROUP BY cid
	) AS b
	
	WHERE a.cid = b.cid
	
	GROUP BY descricao, cidade
	
	ORDER BY cidade";
}
	
	
	//echo "<pre>$sql_stmt</pre>";
	
/*
if( $cli != -1)
{
	$sql_stmt .= " aih.aih_clinica = ".$cli;
}
if( $municipio != -1 )
{
	$sql_stmt .= " aih.aih_cid_cod_princ = ".$municipio;
}
$sql_stmt .= " GROUP BY cidade, clinica.cli_descricao ORDER BY cidade, clinica.cli_descricao";
*/
$sql=db_query( $sql_stmt, $LOG = false );

if( pg_num_rows($sql) > 0 )
{
	echo "<table style=\"font-size:12px;font-family:Tahoma,Arial;\" width=100% align=center cellspacing=0 cellpadding=0 border=0 topmargin=1 leftmargin=0>
		<tr>
			<td width=200 style=\"font-weight:bold\">Munic&iacute;pio</td>
			<td width=350 style=\"font-weight:bold\">Cl&iacute;nica</td>
			<td width=80 style=\"font-weight:bold\">Quantidade</td>
		</tr>";
	$last_city = "";
	$qtd_city = 0;
	$qtd_total = 0;
	while( $row = pg_fetch_array($sql) )
	{
		
		if( $last_city != $row[2] && $last_city != "" )
		{
			echo "<tr><td colspan=2 align='right'><b>Total da cidade: &nbsp;</b></td>
				<td><b>$qtd_city</b></td></tr>";
			$qtd_city = 0;
			echo "<tr><td colspan=3><hr></td></tr>";
		}
		
		echo "<tr>
			<td width=200>".( $last_city != $row[2] ? $row[2] : "" )."</td>
			<td width=350>".$row[1]."</td>
			<td width=80>".$row[0]."</td>
		</tr>";
		$qtd_city += $row[0];
		$qtd_total += $row[0];
		$last_city = $row[2];
	}
	echo "<tr><td colspan=2 align='right'><b>Total da cidade: &nbsp;</b></td>
		<td><b>$qtd_city</b></td></tr>";
	echo "<tr><td colspan=3 align='center'><b>Total Geral: $qtd_total</b></td></tr>";
}
else
{	
	if( $cli != -1 && !empty($cli) )
	{
		$sql_cli = db_get('SELECT cli_descricao FROM clinica WHERE cli_codigo = '.$cli, false);
	}
	else
	{
		$sql_cli = "TODOS";
	}
	
	if( $municipio != -1 && !empty($municipio) )
	{
		$sql_muni = db_get('SELECT cid_nome FROM cidade WHERE cid_codigo_ibge = '.$municipio, false);
	}
	else
	{
		$sql_muni = "TODOS";
	}
	
	echo "NAO FORAM ENCONTRADAS INFORMACOES COM ESTES PARAMETROS<br><br>";
	echo "CLINICA		->".$sql_cli."<br>";
	echo "MUNICIPIO		->".$sql_muni."<br>";
	echo "COMPETÊNCIA	->".$mes_comp."/".$ano_comp."<br>";	
}
?>