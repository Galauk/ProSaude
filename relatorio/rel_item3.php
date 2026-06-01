<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
?>
<style>
	.conteudo
	{
		font-size:13px;font-family:Tahoma,Arial;
	}
</style>
<?

$Tit="Internacao por municipio, N&#186; Absoluto por Competencia";
$dtIni=$data_ini;
$dtFin=$data_fim;
//$btprint=1;

if(!empty($mes))
{
	$dados_compet = "COMPET&Ecirc;CIA: $mes/$ano ";
} else {
	$dados_compet = "COMPET&Ecirc;CIA: TODAS ";
}

include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

$municipio = $_GET["municipio"];

/*
select usu_codigo,pac_aih_codigo,count(pac_aih_codigo) as total,count(usu_codigo) as totusu
from aih as parte1
group by usu_codigo,pac_aih_codigo
*/
	
	/*echo "<pre>";
		print_r($_REQUEST);
	echo "</pre>";*/
	
	$and_cid = (!empty($municipio) ? " AND c.muni_cd_cod_ibge_resid = $municipio " : "");
	$and_comp = (!empty($mes) ? " AND b.aih_mes_compet = $mes AND b.aih_ano_compet = $ano " : "");
	
	
	$sql_stmt = "SELECT a.cid_nome, b.aih_mes_compet,
			b.aih_ano_compet, COUNT(b.aih_mes_compet) AS qtd
			FROM cidade a, aih b, usuario c
			WHERE b.usu_codigo = c.usu_codigo
			AND c.muni_cd_cod_ibge_resid = a.cid_codigo_ibge
			AND b.aih_ativo = 'S'
			$and_cid
			$and_comp
			GROUP BY a.cid_nome, b.aih_mes_compet,
			b.aih_ano_compet
			ORDER BY a.cid_nome,b.aih_mes_compet ASC";
	
	$sql = db_query( $sql_stmt, $LOG = false );
	
	while ($reg = pg_fetch_array($sql))
	{
		
		if ($reg[cid_nome] != $aux)
		{
			if ($total != "")
			{
				echo "
				<table cellspacing=0 cellpadding=0 border=0 width=100% class='conteudo'>
					<tr>
						<td>
							<table cellspacing=0 cellpadding=0 border=0 width=30% align=center>
								<tr>
									<td width=43% align=center><b>Total</b></td>
									<td align=center>$total</td>
								</tr>						
							</table>
						</td>
					</tr>
				</table>";
				$total = 0;
			}
			
			echo "
			<table cellspacing=0 cellpadding=0 border=0 width=100% class='conteudo'>
				<tr>
					<td width=15%><b>Mun&iacute;cipio:</b> </td>
					<td>$reg[cid_nome]</td>
				<tr>
			</table>";
					
			echo "
			<table cellspacing=0 cellpadding=0 border=0 width=30% align=center class='conteudo'>
				<tr>
					<td align=center><b>Compet&ecirc;ncia</b> </td>
					<td align=center><b>N&#186; de Interna&ccedil;&otilde;es</td>
				<tr>
			</table>";
		}				
				
		echo "
		<table cellspacing=0 cellpadding=0 border=0 width=100% align=center class='conteudo'>
			<tr>
				<td>		
					<table cellspacing=0 cellpadding=0 border=0 width=30% align=center class='conteudo'>
						<tr>
							<td width=43% align=center>$reg[aih_mes_compet] / $reg[aih_ano_compet]</td>
							<td align=center>$reg[qtd]</td>
						</tr>						
					</table>
				</td>
			</tr>
		</table>";
		
		// Variaveis de controle
		$aux = $reg[cid_nome];
		$total += $reg[qtd];
		$total_geral += $reg[qtd];
		$total_compet[$reg[aih_mes_compet]] += $reg[qtd];
		$ano_compet = $reg[aih_ano_compet];
	}
	echo "
	<table cellspacing=0 cellpadding=0 border=0 width=100% class='conteudo'>
		<tr>
			<td>
				<table cellspacing=0 cellpadding=0 border=0 width=30% align=center class='conteudo'>
					<tr>
						<td width=43% align=center><b>Total</b></td>
						<td align=center>$total</td>
					</tr>						
				</table>
			</td>
		</tr>
	</table>";
	if( empty($municipio) )
	{
		echo "
		<table cellspacing=0 cellpadding=0 border=0 width='200' class='conteudo'>
			<tr>
				<td><b>Total Geral </b></td>
			</tr>";
			if( empty($mes) )
			{
				for($i=0;$i<=12;$i++)
				{
					if( !empty($total_compet[$i]) )
					{
						echo "<tr>
							<td width='200'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;Comp $i/$ano_compet&nbsp; $total_compet[$i]
							</td>
						</tr>";
					}
				}
			}
			echo"<tr>
				<td align='right'><b>$total_geral</b></td>
			</tr>
		</table>";
	}

	pg_close($db);
?>