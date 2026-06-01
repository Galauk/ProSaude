<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	    
    $dtIni = "<b>".$_GET[data_ini]."</b>";
    $dtFin = "<b>".$_GET[data_fim]."</b>";
    
    $Tit = "Pacientes Atendidos por Período";
    
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
	
	if ($_GET[estocador] == -1) {
		$sql = "SELECT produto.pro_codigo,produto.pro_nome
				FROM produto $prod_stmt ORDER BY pro_nome ASC";
	} else {
		$sql = "select a.pro_codigo, a.pro_nome from produto a
				left join produto_setor b on a.pro_codigo = b.pro_codigo
				where b.set_codigo = $_GET[estocador]
				order by a.pro_nome asc";
	}
	
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

		echo "<table align='left'>";
			echo "<tr>";
			echo "<td colspan='2' align='left'>";
				echo "<b>Produto</b>";
			echo "</td>";
			echo "<td align='left'><b>";
				echo "Centro Estocador";
			echo "</b></td>";
            echo "<td align='left'><b>";
				echo "Qtd.";
			echo "</b></td>";
			echo "<td align='left'><b>";
				echo "Data";
			echo "</b></td>";
			echo "</tr>";

	while($row = pg_fetch_array($query))
	{
		$condicao = "AND d.pro_codigo = $row[pro_codigo]";

		//de acordo com a quantidade de dias digitado
		//é formatada a data para adicionar no SELECT
		$dt1 = explode("/",$_GET['data_ini']);
		$dt1 = $dt1[2].$dt1[1].$dt1[0];
		$dt2 = explode("/",$_GET['data_fim']);
		$dt2 = $dt2[2].$dt2[1].$dt2[0];
		
		if ($_GET['estocador'] == -1) {
            $sql_1_mes = "SELECT pro_nome, set_saida, SUM(ite_quantidade),set_nome, mov_data FROM (
                                SELECT d.pro_nome, b.set_saida, c.ite_quantidade, e.set_nome,
                                to_char(b.mov_data,'dd/mm/yyyy') as mov_data 
                                FROM usuario a, movimento b, itens_movimento c,
                                produto d, setor e 
                                WHERE a.usu_codigo = b.usu_codigo AND b.mov_codigo = c.mov_codigo
                                AND c.pro_codigo = d.pro_codigo 
                                AND b.set_saida = e.set_codigo
                                AND b.mov_data BETWEEN '$dt1' AND '$dt2' 
                                $condicao) AS tmp_resultado
                            GROUP BY pro_nome, set_saida, set_nome, mov_data
                            ORDER BY sum ASC";
		} else {
            $sql_1_mes = "SELECT pro_nome, set_saida, SUM(ite_quantidade),set_nome, mov_data FROM (
                                SELECT d.pro_nome, b.set_saida, c.ite_quantidade, e.set_nome,
                                to_char(b.mov_data,'dd/mm/yyyy') as mov_data 
                                FROM usuario a, movimento b, itens_movimento c,
                                produto d, setor e 
                                WHERE a.usu_codigo = b.usu_codigo AND b.mov_codigo = c.mov_codigo
                                AND c.pro_codigo = d.pro_codigo 
                                AND e.set_codigo = $_GET[estocador]
                                AND b.mov_data BETWEEN '$dt1' AND '$dt2' 
                                $condicao) AS tmp_resultado
                            GROUP BY pro_nome, set_saida, set_nome, mov_data
                            ORDER BY sum ASC";
		}
        
		$exec_sql_1_mes = db_query($sql_1_mes);
		$row_sql1 = pg_fetch_array($exec_sql_1_mes);
		$total_geral += round($row_sql1[sum]);
        
        echo "<tr>";
        echo "<td colspan='2' align='left'>";
            echo "$row[pro_nome]";
        echo "</td>";
        echo "<td align='left'>";
            echo $row_sql1[set_nome];
        echo "&nbsp;&nbsp;</td>";
        echo "<td align='left'>";
            echo round($row_sql1[sum]);
        echo "&nbsp;&nbsp;</td>";
        echo "<td align='left'>";
            echo $row_sql1[mov_data];
        echo "&nbsp;&nbsp;</td>";
        echo "</tr>";
	}
	echo "<tr><td colspan='4' align='right'><b>Total Geral:</b> ".$total_geral."</td></tr>";
echo "</table>";
?>