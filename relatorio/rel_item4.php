<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
echo "<style type=\"text/css\">
	tr{
	font-size	:12px;
	}
	</style>";

$Tit = "Internacoes por Municipio, n&#186; Absoluto por carater de internacao por competencia";
$dados_compet = "COMPETENCIA ENTRE $mes_ini / $ano_ini E $mes_fin / $ano_fin";
include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

$municipio	=$_GET["municipio"];
$ci		=$_GET["ci"];

$data_ini = sprintf( "01-%02d-%04d", $mes_ini, $ano_ini );
$data_fim = sprintf( "01-%02d-%04d", $mes_fin, $ano_fin );

if ($_GET[prestador] == -1) {
	$condicao_prestador = "";
} else {
	$condicao_prestador = "AND med_codigo_solicitante = $_GET[prestador]";

}

$stmt = "SELECT c.cid_nome
				 ,c.cid_codigo_ibge
				, COUNT(aih_ci) AS total, med.med_nome
				,aih_ci, ci_descricao, ci_cod
			  FROM
			  aih AS a
			  
			  LEFT JOIN usuario AS p0 ON p0.usu_codigo = a.usu_codigo
			  LEFT JOIN aih_paciente AS p1 ON p1.pac_codigo = a.pac_aih_codigo
			  
			  LEFT JOIN cidade AS c ON c.cid_codigo_ibge = (
			   ( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.muni_cd_cod_ibge_resid 
				  WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_ibge_codigo 
				  ELSE null END )
			  )
			  
			  LEFT JOIN ci AS ci ON ci.ci_codigo = aih_ci
			  LEFT JOIN medico AS med ON med.med_codigo = med_codigo_solicitante
			  
			  WHERE
				  ( ( '01-' || aih_mes_compet || '-' || aih_ano_compet )::date BETWEEN '{$data_ini}' AND '{$data_fim}' ) 
				  AND aih_ativo = 'S' ".
			  ( $municipio != - 1 ?
				  "AND
					( CASE WHEN a.usu_codigo IS NOT NULL THEN p0.muni_cd_cod_ibge_resid = {$municipio} 
					  WHEN a.pac_aih_codigo IS NOT NULL THEN p1.pac_ibge_codigo = {$municipio}
					  ELSE false END ) " : " " ).
			  ( $ci != -1 ? " AND aih_ci = {$ci} " : " " ).
			  "
			  $condicao_prestador	
			  GROUP BY c.cid_nome, c.cid_codigo_ibge, aih_ci, ci_descricao, aih_ci, ci_descricao, med_nome, ci_cod
			  ORDER BY cid_nome ";
	
	//print "<pre>$stmt</pre>";
	$qry = db_query($stmt);
		
	echo "<table width='100%' border=0 cellspacing=0 cellpadding=5>";
	$i = 1;
	
	$x = 0;
	
	while($row = pg_fetch_array($qry)) {
		
		$x++;
		
		$cor = ($x % 2 == 0) ? "#DCDCDC" : "";
		
		if ($row[cid_nome] != $aux) {
			$i = 1;
			
			if ($aux != "") {
				echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width=80% align=right><b>Total</b></td>
							<td align=center>$total</td>
						</tr>
						<tr><td colspan=2><hr></td></tr>
					</table>";
			}
			
			echo "<table width='100%' border=0 cellspacing=0 cellpadding=0>
					<tr><td width=15%><b>Municipio</b></td><td> $row[cid_nome]</td></tr></table>";
			echo "<table width='100%' border=0 cellspacing=0 cellpadding=0>
				<tr class='nome_coluna'>
					<td width='20%'><b>Car&aacute;ter de interna&ccedil;&atilde;o</b></td>
					<td width='40%'>&nbsp;</td>
					<td width='20%'><b>Prestador</b></td>
					<td width='20%' align='center'><b>Numero de interna&ccedil;&otilde;es</b></td>
				</tr>
			";
			echo "
			<tr class='nome_coluna' style=\"background:$cor\">
				<td width='20%'>$row[ci_cod]</td>
				<td width='40%'>{$row['ci_descricao']}</td>
				<td width='20%'>{$row['med_nome']}</td>
				<td width='20%' align='center'>{$row['total']}</td>
			</tr>";
			$total = 0;
		} else {
			echo "
			<tr class='nome_coluna' style=\"background:$cor\">
				<td width='20%'>$row[ci_cod]</td>
				<td width='40%'>{$row['ci_descricao']}</td>
				<td width='20%'>{$row['med_nome']}</td>
				<td width='20%' align='center'>{$row['total']}</td>
			</tr>";
		}
		$total += $row[total];
		$total_geral += $row[total];
		$aux = $row[cid_nome];
	}
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width=80% align=right><b>Total</b></td>
							<td align=center>$total</td>
						</tr>
						<tr><td colspan=2><hr></td></tr>
					</table>";
	echo "</table>";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width=80% align=right><b>Total Geral</b></td>
							<td align=center>$total_geral</td>
						</tr>
						<tr><td colspan=2><hr></td></tr>
					</table>";
	echo "</table>";

/**
if ($municipio==-1 && $ci ==-1){
	
echo $colunas;
echo "<table width='100%' border=0 cellspacing=0 cellpadding=5>";			
		
while ($reg=pg_fetch_array($sql)){
	echo "
			<tr><td width='70%'>$reg[nome]</td><td width='10%'>$reg[mcompetencia] / $reg[acompetencia]</td><td width='10%'>$reg[qtde_usu]</td></tr>
	";
}

}else{
$sql=pg_query("select 
							parte3.cid_nome as nome ,
							tab_aih.usu_codigo as codigo,
							tab_aih.pac_aih_codigo as aih_codigo,
							tab_aih.aih_mes_compet as mcompetencia,
							tab_aih.aih_ano_compet as acompetencia,
							count(tab_aih.pac_aih_codigo) as qtde_aih,
							count(tab_aih.usu_codigo) as qtde_usu
						from aih as tab_aih
							inner join usuario as parte2 on tab_aih.usu_codigo=parte2.usu_codigo 
							inner join cidade as parte3 on parte2.muni_cd_cod_ibge_resid=parte3.cid_codigo_ibge
						where tab_aih.aih_ativo='S' and parte3.cid_codigo_ibge=$municipio
						group by tab_aih.usu_codigo,tab_aih.pac_aih_codigo,mcompetencia,acompetencia,parte3.cid_nome") or die(pg_last_error());

echo $colunas;
echo "<table width='100%' border=0 cellspacing=0 cellpadding=5>";			
		
while ($reg=pg_fetch_array($sql)){
	echo "
			<tr><td width='70%'>$reg[nome]</td><td width='10%'>$reg[mcompetencia] / $reg[acompetencia]</td><td width='10%'>$reg[qtde_usu]</td></tr>
	";
	}
}
echo "</table>";
pg_close($db);	
**/
	
?>
