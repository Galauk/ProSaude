<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
?>
<style>
	.bold
	{
		font-weight: bold;
	}
	table
	{
		font-size: 12px;
	}
</style>
<?

// --- > parametros para o relatorio
$data_ini = $_GET["periodo_ini"];
$data_fim = $_GET["periodo_fim"];
$mes_comp = $_GET["mes"];
$ano_comp = $_GET["ano"];
$cid10 = $_GET["cid10"];
/*if($_GET["municipio"] != -1)
{
	$sql = "select cid_codigo_ibge from cidade where cid_codigo = $_GET[municipio]";
	$municipio = db_get($sql);
} else {*/
	$municipio = $_GET["municipio"];
//}
$acao = $_GET["acao"];
// ---> fim parametros

// ---> inicio parametros para cabecalho
$Tit = "Internacoes por CID10, municipio, periodo ou competencia";
if($acao == "periodo")
{
	$dtIni = $periodo_ini;
	$dtFin = $periodo_fim;
} else {
	$dados_compet = "COMPET&Ecirc;NCIA: $mes_comp/$ano_comp";
}
//$btprint = 1;
$colunas = "<tr><td>C&oacute;d do Cid e Descri&ccedil;&atilde;o</td><td>Municipio</td><td>Qtde</td></tr>";
// ----> fim 


// ----> transforma data de dd/mm/yyyy para yyyy-mm-dd
	list($dia,$mes,$ano)=split("/",$data_ini);
	$data_ini=array($ano,$mes,$dia);
	$data_ini=implode("-",$data_ini);
	list($dia,$mes,$ano)=split("/",$data_fim);
	$data_fim=array($ano,$mes,$dia);
	$data_fim=implode("-",$data_fim);
//---->fim 


include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/

if ($acao=="periodo")
{
	if (isset($cid10) and ($cid10==-1) and (isset($municipio) and ($municipio==-1)))
	{
		//echo "if 1 periodo";
		$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
						tbaih.pac_aih_codigo,count(*) as qtde
						from aih as tbaih
						inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
						inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
						inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
						where tbaih.aih_dataini between '$data_ini' and '$data_fim'
						group by tbcidade.cid_nome,tbcid10.cd10_descricao,cast(tbaih.aih_cid_cod_princ as integer),tbaih.pac_aih_codigo
						order by cid_nome");
		
		echo "<table width='100%' border=0>";							
		echo $colunas;
		$x = 0;
		while ($reg = pg_fetch_array($sql))
		{
			$x++;
			
			$total += $reg["qtde"];
			
			if($reg["cid_nome"] != $last_cidade && $x > 1)
			{
				echo "
				<tr class='bold'>
					<td colspan='3'>
						Total Por Munic&iacute;pio = $aux
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<hr/>
					</td>
				</tr>";
				$aux = $reg["qtde"];
			} else {
				$aux += $reg["qtde"];
			}
			echo "
			<tr>
				<td>$reg[cd10_descricao]</td>
				<td>$reg[cid_nome]</td>
				<td>$reg[qtde]</td>
			</tr>";
			
			$last_cidade = $reg["cid_nome"];
			
		}
			echo "
			<tr class='bold'>
				<td colspan='3'>
					Total Por Munic&iacute;pio = $aux
				</td>
			</tr>
			<tr>
				<td colspan='3'>
					<hr/>
				</td>
			</tr>
			<tr class='bold'>
				<td colspan='3'>
					Total Geral = $total
				</td>
			</tr>";
			
		echo "</table>";
	} else {
		if (isset($cid10) and ($cid10==-1) and (isset($municipio) and ($municipio!=-1)))
		{
			//echo "if 2 periodo";					
			$sql = db_query("select tbcidade.cid_nome,tbcid10.cd10_descricao,cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),tbaih.pac_aih_codigo,count(*) as qtde
							from aih as tbaih
							inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
							inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
							inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
							where (tbaih.aih_dataini between '$data_ini' and '$data_fim' and tbcidade.cid_codigo_ibge=$municipio)
							group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
							tbaih.pac_aih_codigo
							order by cid_nome");
			echo "<table width='100%' border=0>";							
			echo $colunas;

			while ($reg = pg_fetch_array($sql))
			{
				
				$total += $reg["qtde"];
				
				echo "
				<tr>
					<td>$reg[cd10_descricao]</td>
					<td>$reg[cid_nome]</td>
					<td>$reg[qtde]</td>
				</tr>";
				
			}
				echo "
				<tr>
					<td colspan='3'>
						<hr/>
					</td>
				</tr>
				<tr class='bold'>
					<td colspan='3'>
						Total Geral = $total
					</td>
				</tr>";
				
			echo "</table>";
		} else {	
			if (isset($cid10) and ($cid10!=-1) and (isset($municipio) and ($municipio==-1)))
			{
				//echo "if 3 periodo";
				$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),
								tbaih.pac_aih_codigo,count(*) as qtde
								from aih as tbaih
								inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
								inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
								inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
								where tbaih.aih_dataini between '$data_ini' and '$data_fim'
								and cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)=$cid10
								group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
								tbaih.pac_aih_codigo
								order by cid_nome");
				echo "<table width='100%' border=0>";							
				echo $colunas;
				$x = 0;
				while ($reg = pg_fetch_array($sql))
				{
					$x++;
					
					$total += $reg["qtde"];
					
					if($reg["cid_nome"] != $last_cidade && $x > 1)
					{
						echo "
						<tr class='bold'>
							<td colspan='3'>
								Total Por Munic&iacute;pio = $aux
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								<hr/>
							</td>
						</tr>";
						$aux = $reg["qtde"];
					} else {
						$aux += $reg["qtde"];
					}
					echo "
					<tr>
						<td>$reg[cd10_descricao]</td>
						<td>$reg[cid_nome]</td>
						<td>$reg[qtde]</td>
					</tr>";
					
					$last_cidade = $reg["cid_nome"];
					
				}
					echo "
					<tr class='bold'>
						<td colspan='3'>
							Total Por Munic&iacute;pio = $aux
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<hr/>
						</td>
					</tr>
					<tr class='bold'>
						<td colspan='3'>
							Total Geral = $total
						</td>
					</tr>";
					
				echo "</table>";
			} else {	
				if (isset($cid10) and ($cid10!=-1) and (isset($municipio) and ($municipio!=-1)))
				{
					//echo "if 4 periodo";
					$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),
									tbaih.pac_aih_codigo,count(*) as qtde
									from aih as tbaih
									inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
									inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
									inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
									where (tbaih.aih_dataini between '$data_ini' and '$data_fim' and tbcidade.cid_codigo_ibge=$municipio and cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)=$cid10)
									group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
									tbaih.pac_aih_codigo
									order by cid_nome");
					echo "<table width='100%' border=0>";							
					echo $colunas;
					while ($reg = pg_fetch_array($sql))
					{
						
						$total += $reg["qtde"];
						
						echo "
						<tr>
							<td>$reg[cd10_descricao]</td>
							<td>$reg[cid_nome]</td>
							<td>$reg[qtde]</td>
						</tr>";
						
					}
						echo "
						<tr>
							<td colspan='3'>
								<hr/>
							</td>
						</tr>
						<tr class='bold'>
							<td colspan='3'>
								Total Geral = $total
							</td>
						</tr>";
						
					echo "</table>";
				}
			}
		}
	}
} else { // por competencia
	if (isset($cid10) and ($cid10==-1) and (isset($municipio) and ($municipio==-1)))
	{
		//echo "if 1 competencia";
		$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),
						tbaih.pac_aih_codigo,count(*) as qtde
						from aih as tbaih
						inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
						inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
						inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
						where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp)
						group by tbcidade.cid_nome,tbcid10.cd10_descricao,cast(tbaih.aih_cid_cod_princ as integer),tbaih.pac_aih_codigo
						order by cid_nome");
		echo "<table width='100%' border=0>";							
		echo $colunas;
		
		$x = 0;
		while ($reg = pg_fetch_array($sql))
		{
			$x++;
			
			$total += $reg["qtde"];
			
			if($reg["cid_nome"] != $last_cidade && $x > 1)
			{
				echo "
				<tr class='bold'>
					<td colspan='3'>
						Total Por Munic&iacute;pio = $aux
					</td>
				</tr>
				<tr>
					<td colspan='3'>
						<hr/>
					</td>
				</tr>";
				$aux = $reg["qtde"];
			} else {
				$aux += $reg["qtde"];
			}
			echo "
			<tr>
				<td>$reg[cd10_descricao]</td>
				<td>$reg[cid_nome]</td>
				<td>$reg[qtde]</td>
			</tr>";
			
			$last_cidade = $reg["cid_nome"];
			
		}
			echo "
			<tr class='bold'>
				<td colspan='3'>
					Total Por Munic&iacute;pio = $aux
				</td>
			</tr>
			<tr>
				<td colspan='3'>
					<hr/>
				</td>
			</tr>
			<tr class='bold'>
				<td colspan='3'>
					Total Geral = $total
				</td>
			</tr>";
			
		echo "</table>";
	} else {
		if(isset($cid10) and ($cid10==-1) and (isset($municipio) and ($municipio!=-1)))
		{
			//echo "if 2 competencia";
			$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),
							tbaih.pac_aih_codigo,count(*) as qtde
							from aih as tbaih
							inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
							inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
							inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
							where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp)
							and (tbcidade.cid_codigo_ibge=$municipio)
							group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
							tbaih.pac_aih_codigo
							order by cid_nome");
			echo "<table width='100%' border=0>";							
			echo $colunas;
			while ($reg = pg_fetch_array($sql))
			{
				
				$total += $reg["qtde"];
				
				echo "
				<tr>
					<td>$reg[cd10_descricao]</td>
					<td>$reg[cid_nome]</td>
					<td>$reg[qtde]</td>
				</tr>";
				
			}
				echo "
				<tr>
					<td colspan='3'>
						<hr/>
					</td>
				</tr>
				<tr class='bold'>
					<td colspan='3'>
						Total Geral = $total
					</td>
				</tr>";
				
			echo "</table>";
		} else {	
			if(isset($cid10) and ($cid10!=-1) and (isset($municipio) and ($municipio==-1)))
			{
				//echo "if 3 competencia";
				$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(cast(tbaih.aih_cid_cod_princ as integer) as integer),
								tbaih.pac_aih_codigo,count(*) as qtde
								from aih as tbaih
								inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
								inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
								inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)
								where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp)
								and cast(cast(tbaih.aih_cid_cod_princ as integer) as integer)=$cid10
								group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
								tbaih.pac_aih_codigo
								order by cid_nome");
				echo "<table width='100%' border=0>";							
				echo $colunas;
				$x = 0;
				while ($reg = pg_fetch_array($sql))
				{
					$x++;
					
					$total += $reg["qtde"];
					
					if($reg["cid_nome"] != $last_cidade && $x > 1)
					{
						echo "
						<tr class='bold'>
							<td colspan='3'>
								Total Por Munic&iacute;pio = $aux
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								<hr/>
							</td>
						</tr>";
						$aux = $reg["qtde"];
						//echo "<h1>igualando....</h1>";
					} else {
						$aux += $reg["qtde"];
						//echo "<h1>somando....</h1>";
					}
					echo "
					<tr>
						<td>$reg[cd10_descricao]</td>
						<td>$reg[cid_nome]</td>
						<td>$reg[qtde]</td>
					</tr>";
					
					$last_cidade = $reg["cid_nome"];
					
				}
					echo "
					<tr class='bold'>
						<td colspan='3'>
							Total Por Munic&iacute;pio = $aux
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<hr/>
						</td>
					</tr>
					<tr class='bold'>
						<td colspan='3'>
							Total Geral = $total
						</td>
					</tr>";
					
				echo "</table>";
			} else {	
				if(isset($cid10) and ($cid10!=-1) and (isset($municipio) and ($municipio!=-1)))
				{
					//echo "if 4 competencia";
					$sql = db_query("select tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),tbaih.pac_aih_codigo,count(*) as qtde
									from aih as tbaih
									inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo 
									inner join cidade as tbcidade on tbcidade.cid_codigo_ibge=tbusuario.muni_cd_cod_ibge_resid
									inner join cid10 as tbcid10 on tbcid10.cd10_codigo=cast(tbaih.aih_cid_cod_princ as integer)
									where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp)
									and (tbcidade.cid_codigo_ibge=$municipio and cast(tbaih.aih_cid_cod_princ as integer)=$cid10)
									group by tbcidade.cid_nome, tbcid10.cd10_descricao, cast(tbaih.aih_cid_cod_princ as integer),
									tbaih.pac_aih_codigo
									order by cid_nome");
					echo "<table width='100%' border=0>";							
					echo $colunas;
					while ($reg = pg_fetch_array($sql))
					{
						
						$total += $reg["qtde"];
						
						echo "
						<tr>
							<td>$reg[cd10_descricao]</td>
							<td>$reg[cid_nome]</td>
							<td>$reg[qtde]</td>
						</tr>";
						
					}
						echo "
						<tr>
							<td colspan='3'>
								<hr/>
							</td>
						</tr>
						<tr class='bold'>
							<td colspan='3'>
								Total Geral = $total
							</td>
						</tr>";
						
					echo "</table>";
				}
			}
		}
	}
}

?>