<?php
/** 
 * Alterado tamanho da fonte e adicionado ao cabecalho a competencia escolhida no filtro.*
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

echo "
<style type=\"text/css\">
tr { font-size : 12px; }
.relatorio { width: 100%; }
.relatorio th { text-align:left; border-bottom: 1px solid #000; }
</style>
";


//
// pegando as vars...
//
$data_ini   			= @ $_GET['data_ini'];
$data_fin   			= @ $_GET['data_fin'];
$mes_compet 			= (int) @ $_GET['mes_compet'];
$ano_compet 			= (int) @ $_GET['ano_compet'];

$cid_codigo 			= (int) @ $_GET['cid_codigo'];
$med_codigo_solicitante	= (int) @ $_GET['med_codigo_solicitante'];
$proc_codigo			= (int) @ $_GET['proc_codigo'];
$med_autorizador		= (int) @ $_GET['med_autorizador'];

//
// iniciando vars...
//
$Tit = "Numeros de Laudos Solicitados";

// arrumando o sql das datas, periodos e afins
if( ! empty($data_ini) )
{
    $dados_compet = "PERIODO : ".$data_ini." A ".$data_fin;
    $where .= " AND aih_dataini BETWEEN '{$data_ini}' AND '{$data_fin}' ";
}
else
{
    $dados_compet = "COMPETENCIA ". mes($mes_compet) ." / ". $ano_compet;
    $where .= " AND aih_ano_compet = {$ano_compet} AND aih_mes_compet = {$mes_compet}";
}

// escolheu algum municipio? procedimento ? médico ? todos ?
if( $cid_codigo > 0 )
	$where .= " AND c.cid_codigo = {$cid_codigo} ";
	
if( $med_codigo_solicitante > 0 )
	$where .= " AND a.med_codigo_solicitante = {$med_codigo_solicitante} ";
	
if( $proc_codigo > 0 )
	$where .= " AND p.proc_codigo = {$proc_codigo} ";
//alterado o a.med_autorizador por a.med_solicitante (erro no SQL)	
if( $med_autorizador > 0 )
	$where .= " AND a.med_solicitante_proc = {$med_autorizador} ";	


include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

$stmt =
		"SELECT 
			COUNT(aih_codigo) AS total,
			prestador,
			medico,
			proc_codigo,
			proc_nome,
			cid_codigo,
			cid_nome
		FROM
		(
			( SELECT 
				a.aih_codigo,
				m1.med_nome as prestador,
				m2.med_nome as medico,
				p.proc_classificacao_sus as proc_codigo,
				p.proc_nome,
				c.cid_codigo,
				COALESCE( c.cid_nome || ' - ' || c.uf_sigla, '(SEM CIDADE)' ) AS cid_nome
				  
				FROM
		
					aih AS a
		
				NATURAL JOIN usuario AS u
		
				LEFT JOIN cidade AS c ON c.cid_codigo_ibge = u.muni_cd_cod_ibge_resid
					   
				INNER JOIN procedimento AS p ON p.proc_codigo = a.aih_desc_proc_soli
				INNER JOIN medico AS m1 ON m1.med_codigo = a.med_codigo_solicitante
				
				LEFT JOIN medico AS m2 ON
					(m2.med_codigo = a.med_solicitante_proc)
		
				WHERE aih_ativo = 'S' {$where}
			)
		
			UNION ALL
		
			( SELECT 
				a.aih_codigo,
				m1.med_nome as prestador,
				m2.med_nome as medico,
				p.proc_classificacao_sus as proc_codigo,
				p.proc_nome,
				c.cid_codigo,
				COALESCE( c.cid_nome || ' - ' || c.uf_sigla, '(SEM CIDADE)' ) AS cid_nome
				  
				FROM
		
					aih AS a
		
				INNER JOIN aih_paciente AS pac ON pac.pac_codigo = a.pac_aih_codigo
					
				LEFT JOIN cidade AS c ON c.cid_codigo_ibge = pac.pac_ibge_codigo
					   
				INNER JOIN procedimento AS p ON p.proc_codigo = a.aih_desc_proc_soli
				INNER JOIN medico AS m1 ON m1.med_codigo = a.med_codigo_solicitante
				
				LEFT JOIN medico AS m2 ON
					(m2.med_codigo = a.med_solicitante_proc )
		
				WHERE aih_ativo = 'S' {$where}
			)
		
		) AS mein
		
		GROUP BY cid_nome, prestador, medico, proc_codigo, proc_nome, cid_codigo
		
		ORDER BY cid_nome, prestador, medico, proc_codigo ASC";

//echo "<pre>$stmt</pre>";

$qry = db_query( $stmt );

$cid_nome_ant  	= null;
$total_prest	= array();
$total_cid		= 0;
$total_geral	= 0;
$cont			= 0;
$cidades		= 0;

while( $linha = pg_fetch_array($qry) )
{
	if( $cid_nome_ant != $linha['cid_nome'] )
	{
		if( $cont > 0 )
		{	
			echo "
				</table>
				<p> Total  de {$cid_nome_ant}: <strong>{$total_cid}</strong></p>
			";
		}
		
		echo "
			<table class='relatorio'>
			<tr>
				<th width='20%'>Prestador</th>
				<th width='20%'>M&eacute;dico</th>
				<th width='35%'>C&oacute;d. - Procedimento</td>
				<th width='20%'>Munic&iacute;pio</th>
				<th width='5%' style='text-align:center;'>TOTAL</td>
			</tr>
			";
			
		$total_cid = 0;
		$cidades++;
	}
	
	echo "
		<tr>
			<td>{$linha[prestador]}</td>
			<td>{$linha[medico]}</td>
			<td>{$linha['proc_codigo']} - {$linha['proc_nome']}</td>
			<td>{$linha[cid_nome]}</td>
			<td style='text-align:center; font-weight: bold;'>{$linha[total]}</td>
		</tr>
		";
	
	$total_cid		+= (int) $linha['total'];
	$total_geral	+= (int) $linha['total'];
	
	$total_prest[ $linha['prestador'] ] += (int) $linha['total'];
	   
	$cid_nome_ant = $linha['cid_nome'];
	$cont++;
}

// fechando o laço
echo "
	</table>
	<p>Total de {$cid_nome_ant}: <strong>{$total_cid}</strong></p>
	<hr />
	";

// total por prestador de serviço
echo "
	<p>Total por prestador</p>
	<table class='relatorio'>
		<tr>
			<th>Prestador</th>
			<th width='5%' style='text-align:center;'>TOTAL</td>
		</tr>
	";
	
foreach( $total_prest as $p_nome => $p_total )
{
	echo "
		<tr>
			<td>{$p_nome}</td>
			<td style='text-align:center; font-weight: bold;'>{$p_total}</td>
		</tr>";
}

echo "</table>";
	
// total geral	
echo	
	"<hr>
	<p> Total geral: <strong>{$total_geral}</strong></p>
	";
