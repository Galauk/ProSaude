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
        </style>";
echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

// Descobre o nome do setor
if ($centro_estocador != -1) {
    $sql_ce = "SELECT set_nome FROM setor WHERE set_codigo = $centro_estocador";
    $query_ce = db_query($sql_ce);
    $row_ce = pg_fetch_array($query_ce);
    $nome_ce = $row_ce[set_nome];
} else {
    $nome_ce = "Todos";
}

$cod_pro = $_GET['cod_pro'];

//$titulo_rel = "PACIENTE POR FAIXA ET&Aacute;RIA / PRODUTO";
$Tit = "PACIENTES POR FAIXA ETARIA/PRODUTO";
$CE = $nome_ce;
$dtIni = $data_ini;
$dtFin = $data_fim;
include 'cabecalho.php';
?>
<head>
    <title><?=$Tit?></title>
<SCRIPT Language="Javascript">
function imprimir()
{
	window.print();
}
</script>
</head>
<body>
<?
  echo $cabecalho;
  
  // Inicio da Gambiarra
  list($d1,$m1,$a1) = explode("/",$data_ini);
  list($d2,$m2,$a2) = explode("/",$data_fim);
    $stmt = "";
    if($produto != -1) {
    	$stmt .= " AND produto.pro_codigo = $produto ";
    }
	
	if ($centro_estocador != -1) {
		$stmt .= " AND movimento.set_saida = $centro_estocador ";
	}
	
	if ($faixa == -1) {
		echo "<table>";
		$sql_pro = "SELECT pro_codigo, pro_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome,pro_nome,produto.pro_codigo
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						$stmt
						GROUP BY data_nasc,usu_nome,usu_sexo,pro_nome, produto.pro_codigo
						ORDER BY data_nasc
					) AS tmp_faixa
					WHERE pro_codigo IS NOT NULL
					GROUP BY pro_codigo, pro_nome
					ORDER BY pro_nome ASC";
		$query_pro = db_query($sql_pro);
	    while($row_pro = pg_fetch_array($query_pro))
	    {
			$sql = "SELECT COUNT(data_nasc) AS qtd, data_nasc, set_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, set_nome
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN setor AS s ON movimento.set_saida = s.set_codigo
						WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						AND produto.pro_codigo = $row_pro[pro_codigo]
						$stmt
						GROUP BY data_nasc,usu_nome,usu_sexo, set_nome
						ORDER BY data_nasc) AS tmp_faixa
					GROUP BY data_nasc, set_nome
					ORDER BY data_nasc ASC"; 
			$query = db_query($sql);
			$resultado = pg_num_rows($query);
			$idade = substr($faixa,0,1);
			
			if ($resultado != 0) {
				echo "<tr>
						<td><b>MEDICAMENTO:</b> $row_pro[pro_nome]</td>
					  </tr>
					  <tr>
						<td><b>FAIXA ETÁRIA</b></td>
						<td align='right'><b>QTD.</b></td>
					  </tr>";
			}
				  
			while($row = pg_fetch_array($query))
			{                
			    // Filtra as faixas
			    if ($row[data_nasc] >= 0 && $row[data_nasc] <= 1)
			    {
                    $faixa_1 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 1 && $row[data_nasc] <= 5)
			    {
                    $faixa_2 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 5 && $row[data_nasc] <= 12)
			    {
                    $faixa_3 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 12 && $row[data_nasc] <= 19)
			    {
                    $faixa_4 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 19 && $row[data_nasc] <= 25)
			    {
                    $faixa_5 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 25 && $row[data_nasc] <= 49)
			    {
                    $faixa_6 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 49 && $row[data_nasc] <= 65)
			    {
                    $faixa_7 += $row[qtd];
			    }
			    else if ($row[data_nasc] > 65)
			    {
                    $faixa_8 += $row[qtd];
			    }
			}
            
            // Inicializa as variaveis
            $faixa_1_geral = 0;
            $faixa_2_geral = 0;
            $faixa_3_geral = 0;
            $faixa_4_geral = 0;
            $faixa_5_geral = 0;
            $faixa_6_geral = 0;
            $faixa_7_geral = 0;
            $faixa_8_geral = 0;
            
            if ($faixa_1 != 0)
            {
                $faixa_1_geral+=$faixa_1;		    
                echo "<tr>
                    <td align='left'>0 a 1 ano</td>
                    <td align='right'>".$faixa_1."</td>
                    </tr>";
            }
            if ($faixa_2 != 0)
            {
                $faixa_2_geral+=$faixa_2;
                echo "<tr>
                    <td align='left'>1 a 5 anos</td>
                    <td align='right'>".$faixa_2."</td>
                    </tr>";
            }
                
            if ($faixa_3 != 0)
            {
                $faixa_3_geral+=$faixa_3;		    
                echo "<tr>
                    <td align='left'>5 a 12 anos</td>
                    <td align='right'>".$faixa_3."</td>
                    </tr>";
            }
            
            if ($faixa_4 != 0)
            {
                $faixa_4_geral+=$faixa_4;		    
                echo "<tr>
                    <td align='left'>12 a 19 anos</td>
                    <td align='right'>".$faixa_4."</td>
                    </tr>";
            }
            
            if ($faixa_5 != 0)
            {
                $faixa_5_geral+=$faixa_5;		    
                echo "<tr>
                    <td align='left'>19 a 25 anos</td>
                    <td align='right'>".$faixa_5."</td>
                    </tr>";
            }
            
            if ($faixa_6 != 0)
            {
                $faixa_6_geral+=$faixa_6;		    
                echo "<tr>
                    <td align='left'>25 a 49 anos</td>
                    <td align='right'>".$faixa_6."</td>
                    </tr>";
            }
            
            if ($faixa_7 != 0)
            {
                $faixa_7_geral+=$faixa_7;		    
                echo "<tr>
                    <td align='left'>49 a 65 anos</td>
                    <td align='right'>".$faixa_7."</td>
                    </tr>";
            }
            
            if ($faixa_8 != 0)
            {
                $faixa_8_geral+=$faixa_8;		    
                echo "<tr>
                    <td align='left'>Acima de 65 anos</td>
                    <td align='right'>".$faixa_8."</td>
                    </tr>";
            }
			
            echo "<tr>
                    <td colspan='3' align='right'><b>TOTAL:</b> ",$faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8,"</td>
            </tr>";
    
            $total_geral += ($faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8);
            $faixa_1 = 0;
            $faixa_2 = 0;
            $faixa_3 = 0;
            $faixa_4 = 0;
            $faixa_5 = 0;
            $faixa_6 = 0;
            $faixa_7 = 0;
            $faixa_8 = 0;
	    }
		
		if ($resultado != 0) {
			echo "<tr>
					<td align='right' colspan='3'><b>TOTAL GERAL:</b> $total_geral</td>
				  </tr>
				
				</tr>
				";				  				
			echo "</table>";
		}
		
	} else {
		// Verifica a condicao da faixa
		if ($faixa == "0_1") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 0 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 1 ";
		else if ($faixa == "1_5") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 1 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 5 ";
		else if ($faixa == "5_12") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 5 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 12 ";
		else if ($faixa == "12_19") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 12 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 19 ";
		else if ($faixa == "19_25") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 19 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 25 ";
		else if ($faixa == "25_49") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 25 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 49 ";
		else if ($faixa == "49_65") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 49 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) <= 65 ";
		else if ($faixa == "65") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) > 65 ";
		

		
		echo "<table>";
		$sql_pro = "SELECT pro_codigo, pro_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome,pro_nome,produto.pro_codigo
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						$stmt
						GROUP BY data_nasc,usu_nome,usu_sexo,pro_nome, produto.pro_codigo
						ORDER BY data_nasc
					) AS tmp_faixa
					WHERE pro_codigo IS NOT NULL
					GROUP BY pro_codigo, pro_nome
					ORDER BY pro_nome ASC";
		$query_pro = db_query($sql_pro);
	    while($row_pro = pg_fetch_array($query_pro))
	    {
			$sql = "SELECT COUNT(data_nasc) AS qtd, data_nasc, set_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, set_nome
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN setor AS s ON movimento.set_saida = s.set_codigo
						WHERE $condicao
						AND movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						AND produto.pro_codigo = $row_pro[pro_codigo]
						$stmt
						GROUP BY data_nasc,usu_nome,usu_sexo, set_nome
						ORDER BY data_nasc) AS tmp_faixa
					GROUP BY data_nasc, set_nome
					ORDER BY data_nasc ASC";

			$query = db_query($sql);
			$resultado = pg_num_rows($query);
			
			if ($resultado != 0) {
				echo "<tr>
						<td><b>MEDICAMENTO:</b> $row_pro[pro_nome]</td>
					  </tr>
					  <tr>
						<td><b>FAIXA ETÁRIA</b></td>					
						<td align='right'><b>QTD.</b></td>
					  </tr>";
			}
			
			// Zebragem
			$controle = 0;
			
			while($row = pg_fetch_array($query))
			{
					    // Filtra as faixas
			    if ($row[data_nasc] >= 0 && $row[data_nasc] <= 1) {
                    $faixa_1 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 1 && $row[data_nasc] <= 5) {
                    $faixa_2 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 5 && $row[data_nasc] <= 12) {
                    $faixa_3 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 12 && $row[data_nasc] <= 19) {
                    $faixa_4 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 19 && $row[data_nasc] <= 25) {
                    $faixa_5 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 25 && $row[data_nasc] <= 49) {
                    $faixa_6 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 49 && $row[data_nasc] <= 65) {
                    $faixa_7 += $row[qtd];
			    }
			    
			    if ($row[data_nasc] > 65)
			    {
                    $faixa_8 += $row[qtd];
			    }
			}
            
		if ($faixa_1 != 0 && $faixa=="0_1")
		{
		    echo "<tr>
				<td align='left'>0 a 1 ano</td>
				<td align='right'>".$faixa_1."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_1."</td>
			  </tr>";			    
		}
		else if ($faixa_2 != 0 && $faixa=="1_5")
		{
		    $faixa_1=0;
		    echo "<tr>
				<td align='left'>1 a 5 anos</td>
				<td align='right'>".$faixa_2."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_2."</td>
			  </tr>";			    
		}
		else if ($faixa_3 != 0 && $faixa=="5_12")
		{
		    $faixa_2=0;
		    echo "<tr>
				<td align='left'>5 a 12 anos</td>
				<td align='right'>".$faixa_3."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_3."</td>
			  </tr>";			    
		}
		else if ($faixa_4 != 0 && $faixa=="12_19")
		{
		    $faixa_3=0;
		    echo "<tr>
				<td align='left'>12 a 19 anos</td>
				<td align='right'>".$faixa_4."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_4."</td>
			  </tr>";			    
		}
		else if ($faixa_5 != 0 && $faixa=="19_25")
		{
		    $faixa_4=0;
		    echo "<tr>
				<td align='left'>19 a 25 anos</td>
				<td align='right'>".$faixa_5."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_5."</td>
			  </tr>";			    
		}
		else if ($faixa_6 != 0 && $faixa=="25_49")
		{
		    $faixa_5=0;
		    echo "<tr>		    
				<td align='left'>25 a 49 anos</td>
				<td align='right'>".$faixa_6."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_6."</td>
			  </tr>";			    
		}
		else if ($faixa_7 != 0 && $faixa=="49_65")
		{
		    $faixa_6=0;
		    echo "<tr>
				<td align='left'>49 a 65 anos</td>
				<td align='right'>".$faixa_7."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_7."</td>
			  </tr>";			    
		}
		else if ($faixa_8 != 0 && $faixa=="65")
		{
		    $faixa_7=0;
		    echo "<tr>
				<td align='left'>Acima de 65 anos</td>
				<td align='right'>".$faixa_8."</td>
			    </tr>";
		    echo "<tr>
			    <td colspan='3' align='right'><b>TOTAL:</b> ".$faixa_8."</td>
			  </tr>";			    
		}


		$total_geral += ($faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8);
		$faixa_1 = 0;
		$faixa_2 = 0;
		$faixa_3 = 0;
		$faixa_4 = 0;
		$faixa_5 = 0;
		$faixa_6 = 0;
		$faixa_7 = 0;
		$faixa_8 = 0;
	    }
		echo "<tr>
			<td align='right' colspan='3'><b>TOTAL GERAL:</b> $total_geral</td>
		  </tr>";
		echo "</table>";
	}
?>
</body>
