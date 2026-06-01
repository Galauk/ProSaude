<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>
<?
    session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    
    echo "<style type='text/css'>
                .quebra_pagina{
                    page-break-before:always;
                }
                tr{
                    font-size:12px;
                }
                th {
                    text-align: left;
                }
            </style>";
    echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
    
    //$sql = pg_fetch_array(pg_query("select to_char((current_date - interval '1 year'), 'dd/mm/yyyy') as dias"));
    
	/*echo "<pre>";
		print_r($_REQUEST);
	echo "</pre>";*/
	
	$Tit = html_entity_decode("RELAT&Oacute;RIO DE QUANTIDADE DE PACIENTES ATENDIDOS POR PROGRAMA");
	
    $dtIni = $_GET["data_ini"];
    $dtFin = $_GET["data_fim"];
    
    include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
    
	$setor = "select set_codigo, set_nome
			  from setor
			  where set_farmacia = 'S'
			  and set_estoque = 'S'";
	$setor .= (empty($_GET[set_codigo]) ? "" : " and set_codigo = $_GET[set_codigo] " );
	$setor .= "order by set_nome";
	
	/*echo "<pre>";
		echo $setor;
	echo "</pre>";*/
	
	$setores = pg_query($setor);
	
	while($set = pg_fetch_array($setores))
	{
	
		$select = "select * from programa_atendimento";
		$select .= (empty($_GET[prg_codigo]) ? "" : " where prg_codigo = $_GET[prg_codigo] ");
		$select .= " order by prg_nome ";
		
		$programa = pg_query($select);
	
	
		/*echo "<pre>";
			print_r($_REQUEST);
		echo "</pre>";*/
	
		echo "<table>";
			echo "<tr>";
				echo "<th colspan='2'>";
					echo $set[set_nome];
				echo "</th>";
			echo "</tr>";
			echo "<tr>";
				echo "<th width='350px'>";
					echo "Programa";
				echo "</th>";
				echo "<th>";
					echo "Quantidade";
				echo "</th>";
			echo "</tr>";
	
		while($row = pg_fetch_array($programa))
		{
			$sql = "select count(distinct a.usu_codigo) as qtde
					from movimento a, itens_movimento b, usuario c
					where a.mov_codigo = b.mov_codigo
					and a.usu_codigo = c.usu_codigo
					and b.pro_codigo in (
					select pro_codigo
					from programa_produto
					where prg_codigo = $row[prg_codigo])
					and a.set_saida = $set[set_codigo]
					and a.usu_codigo is not null
					and a.mov_data between '$dtIni' and '$dtFin'
					";
						
			$exec_sql = pg_query($sql);
			
			//echo "<pre>$sql</pre>";
			
			while($linha = pg_fetch_array($exec_sql))
			{
				$qtde += $linha['qtde'];
				echo "
				<tr>
					<td>
						$row[prg_nome]
					</td>
					<td>
						$linha[qtde]
					</td>
				</tr>";	
			}
		}
			echo "
				<tr>
					<th colspan='2'>
						Total $qtde
					</th>
				</tr>
				<tr>
					<td colspan='2'>
						<hr />
					</td>
				</tr>";
			$total_geral += $qtde;
			$qtde = 0;
		echo "</table>";
	}
	echo "
	<table>
		<tr>
			<th colspan='2'>
				Total Geral $total_geral
			</th>
		</tr>
	</table>";
?>