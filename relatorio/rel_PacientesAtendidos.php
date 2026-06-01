<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    
    $sql = pg_fetch_array(pg_query("select (current_date - interval '1 year') as dias"));
    
    $dtIni = date("d/m/Y");
    $dtFin = $sql[0];
    
    $Tit = "Pacientes Atendidos por Medicamento";
    
    include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";
    
    if ($pro_cod != -1)
    {
        $prod_stmt = "WHERE produto.pro_codigo = $pro_cod ";
        //$condicao = "and b.pro_codigo = $pro_cod";
    }
    else
    {
    	$prod_stmt = "";
    }
	$sql = "SELECT produto.pro_codigo,produto.pro_nome
	FROM produto $prod_stmt ORDER BY pro_nome ASC";
	$query = pg_query($sql) or die(pg_last_error());

echo "<style type='text/css'>
			.quebra_pagina{
			page-break-before:always;
			}
			td{
			font-size:11px;
			}
			</style>";
		echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

		echo "<table align='left' style='font-size:bold'>";
			echo "<tr>";
			echo "<th colspan='2' align='left'>";
				echo "<b>Medicamento</b>";
			echo "</th>";
			echo "<th align='left'><b>";
				echo date("d/m/Y", mktime( 0, 0, 0, date("m"), (date("d")-$qtde_dia), date("Y") ));
			echo "</b></th>";
			echo "<th align='left'><b>";
				echo date("d/m/Y", mktime( 0, 0, 0, (date("m")-1), date("d"), date("Y") ));
			echo "</b></th>";
			echo "<th align='left'><b>";
				echo date("d/m/Y", mktime( 0, 0, 0, (date("m")-3), date("d"), date("Y") ));
			echo "</th>";
			echo "<th align='left'><b>";
				echo date("d/m/Y", mktime( 0, 0, 0, (date("m")-4), date("d"), date("Y") ));
			echo "</b></th>";
			echo "<th align='left'><b>";
				echo date("d/m/Y", mktime( 0, 0, 0, date("m"), date("d"), (date("Y")-1) ));
			echo "</b></th>";
			echo "</tr>";


	while($row = pg_fetch_array($query))
	{
		$condicao = "AND d.pro_codigo = $row[0]";	

		//de acordo com a quantidade de dias digitado
		//é formatada a data para adicionar no SELECT
		$dt1 = date("Ymd", mktime( 0, 0, 0, date("m"), (date("d")-$qtde_dia), date("Y") ));
		$dt2 = date("Ymd");

		$sql_dia = "SELECT d.pro_nome
			FROM usuario a, movimento b, itens_movimento c, produto d
			WHERE a.usu_codigo = b.usu_codigo AND
			b.mov_codigo = c.mov_codigo AND
			c.pro_codigo = d.pro_codigo AND
			b.mov_data BETWEEN '$dt1' AND '$dt2'
			$condicao ";
				
		$exec_sql_dia = pg_query($sql_dia);
		
		$qtde_dia = pg_num_rows($exec_sql_dia);
		
		$dt1 = date("Ymd", mktime( 0, 0, 0, (date("m")-1), date("d"), date("Y") ));

		$sql_1_mes = "SELECT d.pro_nome
			FROM usuario a, movimento b, itens_movimento c, produto d
			WHERE a.usu_codigo = b.usu_codigo AND
			b.mov_codigo = c.mov_codigo AND
			c.pro_codigo = d.pro_codigo AND
			b.mov_data BETWEEN '$dt1' AND '$dt2'
			$condicao ";
				
		$exec_sql_1_mes = pg_query($sql_1_mes);
		
		$qtde_1_mes = pg_num_rows($exec_sql_1_mes);
		
		$dt1 = date("Ymd", mktime( 0, 0, 0, (date("m")-3), date("d"), date("Y") ));

		$sql_3_mes = "SELECT d.pro_nome
			FROM usuario a, movimento b, itens_movimento c, produto d
			WHERE a.usu_codigo = b.usu_codigo AND
			b.mov_codigo = c.mov_codigo AND
			c.pro_codigo = d.pro_codigo AND
			b.mov_data BETWEEN '$dt1' AND '$dt2'
			$condicao ";
		
		$exec_sql_3_mes = pg_query($sql_3_mes);
		
		$qtde_3_mes = pg_num_rows($exec_sql_3_mes);
		
		$dt1 = date("Ymd", mktime( 0, 0, 0, (date("m")-4), date("d"), date("Y") ));

		$sql_4_mes = "SELECT d.pro_nome
			FROM usuario a, movimento b, itens_movimento c, produto d
			WHERE a.usu_codigo = b.usu_codigo AND
			b.mov_codigo = c.mov_codigo AND
			c.pro_codigo = d.pro_codigo AND
			b.mov_data BETWEEN '$dt1' AND '$dt2'
			$condicao ";
		
		$exec_sql_4_mes = pg_query($sql_4_mes);
		
		$qtde_4_mes = pg_num_rows($exec_sql_4_mes);
		
		$dt1 = date("Ymd", mktime( 0, 0, 0, date("m"), date("d"), (date("Y")-1) ));

		$sql_1_ano = "SELECT d.pro_nome
			FROM usuario a, movimento b, itens_movimento c, produto d
			WHERE a.usu_codigo = b.usu_codigo AND
			b.mov_codigo = c.mov_codigo AND
			c.pro_codigo = d.pro_codigo AND
			b.mov_data BETWEEN '$dt1' AND '$dt2'
			$condicao ";
				
		$exec_sql_1_ano = pg_query($sql_1_ano);
		
		$qtde_1_ano = pg_num_rows($exec_sql_1_ano);
	
    
		echo "<tr>";
		echo "<td colspan='2' align='left'>";
			echo "$row[1]";
		echo "</td>";
		echo "<td align='right'>";
			echo $qtde_dia;
		echo "&nbsp;</td>";
		echo "<td align='right'>";
			echo $qtde_1_mes;
		echo "&nbsp;</td>";
		echo "<td align='right'>";
			echo $qtde_3_mes;
		echo "&nbsp;</td>";
		echo "<td align='right'>";
			echo $qtde_4_mes;
		echo "&nbsp;</td>";
		echo "<td align='right'>";
			echo $qtde_1_ano;
		echo "&nbsp;</td>";
		echo "</tr>";
	}
echo "</table>";
?>