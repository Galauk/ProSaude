<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

/* o estilo abaixo define o negrito da coluna e o tamanho da fonte para o relatório */
echo "<style type=\"text/css\">
tr{
	font-size	:12px;
	font-family	:verdana;
}
.nome_coluna{
	font-weight	:bold;
}
</style>";


// --- > parametros para o relatorio
$data_ini	= $_GET["periodo_ini"];
$data_fim	= $_GET["periodo_fim"];
$mes_comp	= $_GET["mes"];
$ano_comp 	= $_GET["ano"];
$proced		= $_GET["procedimento"];
$municipio	= $_GET["municipio"];
$acao		= $_GET["acao"];
// ---> fim parametros

$Tit="Internacoes por Procedimento, municipio, periodo ou competencia";
$dtIni=$data_ini;
$dtFin=$data_fim;
$btprint=0;

$total_por_cidade=0;
$total_geral=0;
$separador="<tr><td colspan='3'><hr></td></tr>";

// o codigo abaixo verifica se recebeu os parametros mes de competencia e ano de competencia e os imprime
// caso o parametro não for passado, não será impresso nada
if (strlen($data_ini)==0) {
	$dados_compet="COMPETENCIA : ".$mes_comp." / ".$ano_comp;
}

include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

// ----> transforma data de dd/mm/yyyy para yyyy-mm-dd
	list($dia,$mes,$ano)=split("/",$data_ini);
	$data_ini=array($ano,$mes,$dia);
	$data_ini=implode("-",$data_ini);
	list($dia,$mes,$ano)=split("/",$data_fim);
	$data_fim=array($ano,$mes,$dia);
	$data_fim=implode("-",$data_fim);
//---->fim 
$colunas="<table width='100%'>
	<tr class='nome_coluna'><td width='30%'>Municipio</td><td width='50%'>Procedimento</td><td width='10%'>Qtde</td></tr>
	</table>";
	

					
//----> RELATORIO POR PERIODO ************************************					
//----> bloco 1
if ($acao=="periodo")
{
	//--> inicio bloco 1.1
	if (isset($proced) and ($proced==-1) and (isset($municipio) and ($municipio==-1)))
	{ // todos os procedimentos de todos os municipios
		$sql=db_query("select 
					tbcidade.cid_nome as nome_cidade,
					tbproced.proc_nome as nome_proced,
					count(tbaih.aih_codigo) as qtde,
					tbcidade.cid_codigo_ibge as codigo_cidade,
					tbproced.proc_classificacao_sus as codigo_procedimento
				from aih as tbaih
					inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
					inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
					inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
				where ((tbaih.aih_dataini between '$data_ini' and '$data_fim') and (tbaih.aih_ativo='S'))
				group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
				order by nome_cidade,tbcidade.cid_codigo_ibge");
		echo $colunas;
		echo "<table width='100%'>";
		while ($reg=pg_fetch_array($sql))
		{
			$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
			$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
			if ($cidade)
			{
				echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";
				echo $separador;
				echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
				$total_geral += $total_por_cidade;
				$total_por_cidade=0;
			}
			else
			{
				if ($cidade=$procedimento)
				{
					echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
				}		
			}
			$cidade=$reg["codigo_cidade"];
			$procedimento=$reg["codigo_procedimento"];
			$total_por_cidade+=$reg[qtde];
		}
		$total_geral += $total_por_cidade;
		echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";
	}
	else
	{
		//----> inicio bloco 1.1.1
		if (isset($proced) and ($proced==-1) and (isset($municipio) and ($municipio!=-1)))
		{ // todos os procedimentos de um unico municipio
			$sql=db_query("select 
						tbcidade.cid_nome as nome_cidade,
						tbproced.proc_nome as nome_proced,
						count(tbaih.aih_codigo) as qtde,
						tbcidade.cid_codigo_ibge as codigo_cidade,
						tbproced.proc_classificacao_sus as codigo_procedimento
					from aih as tbaih
						inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
						inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
						inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
					where ((tbaih.aih_dataini between '$data_ini' and '$data_fim') and (tbaih.aih_ativo='S' and tbcidade.cid_codigo_ibge=$municipio))
					group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
					order by nome_cidade,tbcidade.cid_codigo_ibge");
			echo $colunas;
			echo "<table width='100%'>";
			while ($reg=pg_fetch_array($sql))
			{
				$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
				$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
				if ($cidade){
					echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";							
					echo $separador;
					echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$erg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
					$total_geral += $total_por_cidade;
					$total_por_cidade=0;
				}
				else
				{
					if ($cidade=$procedimento){
						echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
					}			
				}
				$cidade=$reg["codigo_cidade"];
				$procedimento=$reg["codigo_procedimento"];
				$total_por_cidade+=$reg[qtde];
			}
			$total_geral += $total_por_cidade;
			echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";					
		}
		else
		{
			// ---> inicio bloco 1.1.2
			if (isset($proced) and ($proced!=-1) and (isset($municipio) and ($municipio==-1)))
			{ // um procedimento em todos os municipios
				$sql=db_query("select 
							tbcidade.cid_nome as nome_cidade,
							tbproced.proc_nome as nome_proced,
							count(tbaih.aih_codigo) as qtde,
							tbcidade.cid_codigo_ibge as codigo_cidade,
							tbproced.proc_classificacao_sus as codigo_procedimento
						from aih as tbaih
							inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
							inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
							inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
						where ((tbaih.aih_dataini between '$data_ini' and '$data_fim') and (tbaih.aih_ativo='S' and tbproced.proc_codigo=$proced))
						group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
						order by nome_cidade,tbcidade.cid_codigo_ibge");
				echo $colunas;
				echo "<table width='100%'>";
				while ($reg=pg_fetch_array($sql))
				{
					$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
					$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
					if ($cidade)
					{
						echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";									
						echo $separador;
						echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
						$total_geral += $total_por_cidade;
						$total_por_cidade=0;
					}
					else
					{
						if ($cidade=$procedimento)
						{
							echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
						}			
					}
					$cidade=$reg["codigo_cidade"];
					$procedimento=$reg["codigo_procedimento"];
					$total_por_cidade+=$reg[qtde];
				}
				$total_geral += $total_por_cidade;
				echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";							
			}
			else
			{
				// -----> inicio bloco 1.1.3
				if (isset($proced) and ($proced!=-1) and (isset($municipio) and ($municipio!=-1)))
				{ // um procedimento em um municipio
					$sql=db_query("select 
								tbcidade.cid_nome as nome_cidade,
								tbproced.proc_nome as nome_proced,
								count(tbaih.aih_codigo) as qtde,
								tbcidade.cid_codigo_ibge as codigo_cidade,
								tbproced.proc_classificacao_sus as codigo_procedimento
							from aih as tbaih
								inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
								inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
								inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
							where ((tbaih.aih_dataini between '$data_ini' and '$data_fim') and (tbaih.aih_ativo='S' and tbproced.proc_codigo=$proced and tbcidade.cid_codigo_ibge=$municipio))
							group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
							order by nome_cidade,tbcidade.cid_codigo_ibge");
					echo $colunas;
					echo "<table width='100%'>";
					while ($reg=pg_fetch_array($sql)){
						$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
						$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
						if ($cidade)
						{
							echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";											
							echo $separador;
							echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
							$total_geral += $total_por_cidade;
							$total_por_cidade=0;
						}else
						{
							if ($cidade=$procedimento)
							{
								echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
							}			
						}
						$cidade=$reg["codigo_cidade"];
						$procedimento=$reg["codigo_procedimento"];
						$total_por_cidade+=$reg[qtde];
					}
					$total_geral += $total_por_cidade;
					echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";
				} // --->fim bloco 1.1.3
			} //---> fim bloco 1.1.2
		}// ---> fim bloco 1.1
	}// ---> fim bloco 1
	echo "<tr><td colspan='3'>&nbsp</td></tr>";
	echo "<tr><td align='center' colspan='3'><b>Total geral = ".$total_geral."</b></td></tr>";
//---> FIM RELATORIO POR PERIODO*************************************************
}
else
{
	// ---> inicio bloco 2
	if ($acao=="competencia")
	{
	// -- inicio bloco 2.1
		if (isset($proced) and ($proced==-1) and (isset($municipio) and ($municipio==-1)))
		{ // todos os procedimentos de todos os municipios
			$sql=db_query("select 
						tbcidade.cid_nome as nome_cidade,
						tbproced.proc_nome as nome_proced,
						count(tbaih.aih_codigo) as qtde,
						tbcidade.cid_codigo_ibge as codigo_cidade,
						tbproced.proc_classificacao_sus as codigo_procedimento
					from aih as tbaih
						inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
						inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
						inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
					where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp and tbaih.aih_ativo='S')
						group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
						order by nome_cidade,tbcidade.cid_codigo_ibge");
			echo $colunas;
			echo "<table width='100%'>";
			while ($reg=pg_fetch_array($sql))
			{
				$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
				$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
				if ($cidade)
				{
					echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";					
					echo $separador;
					echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
					$total_geral += $total_por_cidade;
					$total_por_cidade=0;
				}
				else
				{
					if ($cidade=$procedimento)
					{
						echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
					}			
				}
				$cidade=$reg["codigo_cidade"];
				$procedimento=$reg["codigo_procedimento"];
				$total_por_cidade+=$reg[qtde];				
			}
			$total_geral += $total_por_cidade;
			echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";										
		}
		else
		{
			//----> inicio bloco 2.1.1
			if (isset($proced) and ($proced==-1) and (isset($municipio) and ($municipio!=-1)))
			{ // todos os procedimentos de um unico municipio
				$sql=db_query("select 
							tbcidade.cid_nome as nome_cidade,
							tbproced.proc_nome as nome_proced,
							count(tbaih.aih_codigo) as qtde,
							tbcidade.cid_codigo_ibge as codigo_cidade,
							tbproced.proc_classificacao_sus as codigo_procedimento
						from aih as tbaih
							inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
							inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
							inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
						where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp) and (tbaih.aih_ativo='S' and tbcidade.cid_codigo_ibge=$municipio)
						group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
						order by nome_cidade,tbcidade.cid_codigo_ibge");
				echo $colunas;
				echo "<table width='100%'>";
				while ($reg=pg_fetch_array($sql))
				{
					$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
					$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
					if ($cidade)
					{
						echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";								
						echo $separador;
						echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
						$total_geral += $total_por_cidade;
						$total_por_cidade=0;
					}
					else
					{
						if ($cidade=$procedimento)
						{
							echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
						}			
					}
					$cidade=$reg["codigo_cidade"];
					$procedimento=$reg["codigo_procedimento"];
					$total_por_cidade+=$reg[qtde];							
				}
				$total_geral += $total_por_cidade;
				echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";
			}
			else
			{
				// ---> inicio bloco 2.1.2
				if (isset($proced) and ($proced!=-1) and (isset($municipio) and ($municipio==-1)))
				{ // um procedimento em todos os municipios
					$sql=db_query("select 
								tbcidade.cid_nome as nome_cidade,
								tbproced.proc_nome as nome_proced,
								count(tbaih.aih_codigo) as qtde,
								tbcidade.cid_codigo_ibge as codigo_cidade,
								tbproced.proc_classificacao_sus as codigo_procedimento
							from aih as tbaih
								inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
								inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
								inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
							where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp) and (tbaih.aih_ativo='S' and tbproced.proc_codigo=$proced)
								group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
								order by nome_cidade,tbcidade.cid_codigo_ibge");
					echo $colunas;
					echo "<table width='100%'>";
					while ($reg=pg_fetch_array($sql))
					{
						$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
						$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
						if ($cidade)
						{
							echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";											
							echo $separador;
							echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
							$total_geral += $total_por_cidade;
							$total_por_cidade=0;
						}
						else
						{
							if ($cidade=$procedimento)
							{
								echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
							}			
						}
						$cidade=$reg["codigo_cidade"];
						$procedimento=$reg["codigo_procedimento"];
						$total_por_cidade+=$reg[qtde];										
					}
					$total_geral += $total_por_cidade;
					echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";
				}
				else
				{
				// -----> inicio bloco 2.1.3
					if (isset($proced) and ($proced!=-1) and (isset($municipio) and ($municipio!=-1)))
					{ // um procedimento em um municipio
						$sql=db_query("select 
									tbcidade.cid_nome as nome_cidade,
									tbproced.proc_nome as nome_proced,
									count(tbaih.aih_codigo) as qtde,
									tbcidade.cid_codigo_ibge as codigo_cidade,
									tbproced.proc_classificacao_sus as codigo_procedimento
								from aih as tbaih
									inner join usuario as tbusuario on tbusuario.usu_codigo=tbaih.usu_codigo
									inner join cidade as tbcidade on tbusuario.muni_cd_cod_ibge_resid=tbcidade.cid_codigo_ibge
									inner join procedimento as tbproced on tbaih.aih_desc_proc_soli=tbproced.proc_codigo
								where (tbaih.aih_mes_compet=$mes_comp and tbaih.aih_ano_compet=$ano_comp) and (tbaih.aih_ativo='S' and tbproced.proc_codigo=$proced and tbcidade.cid_codigo_ibge=$municipio)
									group by nome_cidade,nome_proced,codigo_cidade,codigo_procedimento
									order by nome_cidade,tbcidade.cid_codigo_ibge");
						echo $colunas;
						echo "<table width='100%'>";
						while ($reg=pg_fetch_array($sql))
						{
							$cidade=($reg["codigo_cidade"]==$cidade) ? false : true ;
							$procedimento=($reg["codigo_cidade"]==$procedimento) ? false : true ;	
							if ($cidade)
							{
								echo ($total_por_cidade==0) ? '' : "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";														
								echo $separador;
								echo "<tr><td width='30%'><b>$reg[nome_cidade]</b></td><td width='50%'>$reg[codigo_procedimento]-$reg[nome_proced]</td><td width='10%'>$reg[qtde]</td></tr>";
								$total_geral += $total_por_cidade;
								$total_por_cidade=0;
							}
							else
							{
								if ($cidade=$procedimento)
								{
									echo "<tr><td></td><td>$reg[codigo_procedimento]-$reg[nome_proced]</td><td>$reg[qtde]</td></tr>";	
								}			
							}
							$cidade=$reg["codigo_cidade"];
							$procedimento=$reg["codigo_procedimento"];
							$total_por_cidade+=$reg[qtde];													
						}
						$total_geral += $total_por_cidade;
						echo "<tr><td><b>Total da cidade = ".$total_por_cidade."</b></td></tr>";																			
					} // --->fim bloco 2.1.3
				} //---> fim bloco 2.1.2
			}// ---> fim bloco 2.1
		}// ---> fim bloco 2
	}
	echo "<tr><td colspan='3'>&nbsp</td></tr>";
	echo "<tr><td align='center' colspan='3'><b>Total geral = ".$total_geral."</b></td></tr>";
}
//---> FIM RELATORIO POR COMPETENCIA*************************************************
pg_close($db);
?>
