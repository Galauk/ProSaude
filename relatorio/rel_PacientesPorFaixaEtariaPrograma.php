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

$titulo_rel = "PACIENTE POR FAIXA ET&Aacute;RIA / PROGRAMA";
$Tit = "RELATÓRIO DE PACIENTE POR FAIXA ETÁRIA / PROGRAMA";
$CE = $nome_ce;
$dtIni = $data_ini;
$dtFin = $data_fim;
include 'cabecalho.php';
?>
<head>
    <title><?=$titulo_rel?></title>
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
	if ($centro_estocador != -1) {
		$stmt .= " AND movimento.set_saida = $centro_estocador ";
	}
	
	if($programa != -1) {
        $stmt .= " AND pp.prg_codigo = $programa ";
    }
	
	
	if ($faixa == -1) {
		echo "<table>";
		$sql_pro = "SELECT prg_codigo, prg_nome 
								FROM ( SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, pp.prg_codigo, pa.prg_nome
									FROM usuario LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo 
									LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo 
									LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo 
									LEFT JOIN programa_produto AS pp ON pp.pro_codigo = itens_movimento.pro_codigo 
									LEFT JOIN programa_atendimento AS pa ON pp.prg_codigo = pa.prg_codigo
									WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
									$stmt
									GROUP BY data_nasc,usu_nome,usu_sexo, pp.prg_codigo, pa.prg_nome
									ORDER BY data_nasc ) AS tmp_faixa 
						WHERE prg_codigo IS NOT NULL 
						GROUP BY prg_codigo, prg_nome";
		$query_pro = db_query($sql_pro);
		while($row_pro = pg_fetch_array($query_pro)) {
			$sql = "SELECT COUNT(data_nasc) AS qtd, data_nasc, set_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, set_nome
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN programa_produto AS pp ON pp.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN programa_atendimento AS pa ON pp.prg_codigo = pa.prg_codigo
						LEFT JOIN setor AS s ON movimento.set_saida = s.set_codigo
						WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						AND pp.prg_codigo = $row_pro[prg_codigo]
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
						<td><b>PROGRAMA:</b> $row_pro[prg_nome]</td>
					  </tr>
					  <tr>
						<td><b>FAIXA ETÁRIA</b></td>
						<td align='right'><b>QTD.</b></td>
					  </tr>";
			}
				  
			while($row = pg_fetch_array($query)) {                
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
                
                if ($row[data_nasc] > 65) {
                    $faixa_8 += $row[qtd];
                }
			}
            
            if ($faixa_1 != 0) {
                echo "<tr>
                            <td align='left'>0 a 1 ano</td>
                            <td align='right'>".$faixa_1."</td>
                        </tr>";
            }
            
            if ($faixa_2 != 0) {
                echo "<tr>
                            <td align='left'>1 a 5 anos</td>
                            <td align='right'>".$faixa_2."</td>
                        </tr>";
            }
                    
            if ($faixa_3 != 0) {
                echo "<tr>
                            <td align='left'>5 a 12 anos</td>
                            <td align='right'>".$faixa_3."</td>
                        </tr>";
            }
            
            if ($faixa_4 != 0) {
                echo "<tr>
                            <td align='left'>12 a 19 anos</td>
                            <td align='right'>".$faixa_4."</td>
                        </tr>";
            }
            
            if ($faixa_5 != 0) {
                echo "<tr>
                            <td align='left'>19 a 25 anos</td>
                            <td align='right'>".$faixa_5."</td>
                        </tr>";
            }
            
            if ($faixa_6 != 0) {
                echo "<tr>
                            <td align='left'>25 a 49 anos</td>
                            <td align='right'>".$faixa_6."</td>
                        </tr>";
            }
            
            if ($faixa_7 != 0) {                    
                echo "<tr>
                            <td align='left'>49 a 65 anos</td>
                            <td align='right'>".$faixa_7."</td>
                        </tr>";
            }
            
            if ($faixa_8 != 0) {
                echo "<tr>
                            <td align='left'>Acima de 65 anos</td>
                            <td align='right'>".$faixa_8."</td>
                        </tr>";
            }
                    
			echo "<tr>
					<td colspan='3' align='right'><b>TOTAL:</b> ",$faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8,"</td>
				  </tr>";

			$total_geral += ($faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8);
			
			$total_faixa_1 += $faixa_1;
			$total_faixa_2 += $faixa_2;
			$total_faixa_3 += $faixa_3;
			$total_faixa_4 += $faixa_4;
			$total_faixa_5 += $faixa_5;
			$total_faixa_6 += $faixa_6;
			$total_faixa_7 += $faixa_7;
			$total_faixa_8 += $faixa_8;
			
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
			echo "
				<tr>
					<td colspan='3'><hr /></td>
				</tr>
				<tr>
					<td colspan='3'>
						<b>TOTAL POR FAIXA ETÁRIA</b>
					</td>
				</tr>";
			
			if ($total_faixa_1 != 0) {
                echo "
				<tr>
					<td align='left'>0 a 1 ano</td>
					<td align='right'>".$total_faixa_1."</td>
				</tr>";
            }
            
            if ($total_faixa_2 != 0) {
                echo "
				<tr>
					<td align='left'>1 a 5 anos</td>
					<td align='right'>".$total_faixa_2."</td>
				</tr>";
            }
                    
            if ($total_faixa_3 != 0) {
                echo "
				<tr>
					<td align='left'>5 a 12 anos</td>
					<td align='right'>".$total_faixa_3."</td>
				</tr>";
            }
            
            if ($total_faixa_4 != 0) {
                echo "
				<tr>
					<td align='left'>12 a 19 anos</td>
					<td align='right'>".$total_faixa_4."</td>
				</tr>";
            }
            
            if ($total_faixa_5 != 0) {
                echo "
				<tr>
					<td align='left'>19 a 25 anos</td>
					<td align='right'>".$total_faixa_5."</td>
				</tr>";
            }
            
            if ($total_faixa_6 != 0) {
                echo "
				<tr>
					<td align='left'>25 a 49 anos</td>
					<td align='right'>".$total_faixa_6."</td>
				</tr>";
            }
            
            if ($total_faixa_7 != 0) {                    
                echo "
				<tr>
					<td align='left'>49 a 65 anos</td>
					<td align='right'>".$total_faixa_7."</td>
				</tr>";
            }
            
            if ($total_faixa_8 != 0) {
                echo "
				<tr>
					<td align='left'>Acima de 65 anos</td>
					<td align='right'>".$total_faixa_8."</td>
				</tr>";
            }
				
			echo "
				<tr>
					<td align='right' colspan='3'><b>TOTAL GERAL:</b> $total_geral</td>
				</tr>
			</table>";
		}
		
	} else {
		// Verifica a condicao da faixa
		if ($faixa == "0_1") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 0 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 1 ";
		else if ($faixa == "1_5") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 1 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 5 ";
		else if ($faixa == "5_12") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 5 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 12 ";
		else if ($faixa == "12_19") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 12 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 19 ";
		else if ($faixa == "19_25") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 19 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 25 ";
		else if ($faixa == "25_49") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 25 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 49 ";
		else if ($faixa == "49_65") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 49 AND EXTRACT(YEAR FROM age(now(),usu_datanasc)) < 65 ";
		else if ($faixa == "65") $condicao = " EXTRACT(YEAR FROM age(now(),usu_datanasc)) >= 65 ";
		
		echo "<table>";
		$sql_pro = "SELECT prg_codigo, prg_nome 
							FROM ( SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, pp.prg_codigo, pa.prg_nome
								FROM usuario LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo 
								LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo 
								LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo 
								LEFT JOIN programa_produto AS pp ON pp.pro_codigo = itens_movimento.pro_codigo 
								LEFT JOIN programa_atendimento AS pa ON pp.prg_codigo = pa.prg_codigo
								WHERE movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
								$stmt
								GROUP BY data_nasc,usu_nome,usu_sexo, pp.prg_codigo, pa.prg_nome
								ORDER BY data_nasc ) AS tmp_faixa 
						WHERE prg_codigo IS NOT NULL 
						GROUP BY prg_codigo, prg_nome";
		$query_pro = db_query($sql_pro);
		while($row_pro = pg_fetch_array($query_pro)) {
			$sql = "SELECT COUNT(data_nasc) AS qtd, data_nasc, set_nome FROM (
						SELECT EXTRACT(YEAR FROM age(now(),usu_datanasc)) AS data_nasc,usu_sexo,usu_nome, set_nome
						FROM usuario
						LEFT JOIN movimento ON movimento.usu_codigo = usuario.usu_codigo
						LEFT JOIN itens_movimento ON itens_movimento.mov_codigo = movimento.mov_codigo
						LEFT JOIN produto ON produto.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN programa_produto AS pp ON pp.pro_codigo = itens_movimento.pro_codigo
						LEFT JOIN programa_atendimento AS pa ON pp.prg_codigo = pa.prg_codigo
						LEFT JOIN setor AS s ON movimento.set_saida = s.set_codigo
						WHERE $condicao
						AND movimento.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
						AND pp.prg_codigo = $row_pro[prg_codigo]
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
			
			while($row = pg_fetch_array($query)) {
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
                
                if ($row[data_nasc] > 65) {
                    $faixa_8 += $row[qtd];
                }
			}
            
            if ($faixa_1 != 0) {
                echo "<tr>
                            <td align='left'>0 a 1 ano</td>
                            <td align='right'>".$faixa_1."</td>
                        </tr>";
            }
            
            if ($faixa_2 != 0) {
                echo "<tr>
                            <td align='left'>1 a 5 anos</td>
                            <td align='right'>".$faixa_2."</td>
                        </tr>";
            }
                    
            if ($faixa_3 != 0) {
                echo "<tr>
                            <td align='left'>5 a 12 anos</td>
                            <td align='right'>".$faixa_3."</td>
                        </tr>";
            }
            
            if ($faixa_4 != 0) {
                echo "<tr>
                            <td align='left'>12 a 19 anos</td>
                            <td align='right'>".$faixa_4."</td>
                        </tr>";
            }
            
            if ($faixa_5 != 0) {
                echo "<tr>
                            <td align='left'>19 a 25 anos</td>
                            <td align='right'>".$faixa_5."</td>
                        </tr>";
            }
            
            if ($faixa_6 != 0) {
                echo "<tr>
                            <td align='left'>25 a 49 anos</td>
                            <td align='right'>".$faixa_6."</td>
                        </tr>";
            }
            
            if ($faixa_7 != 0) {                    
                echo "<tr>
                            <td align='left'>49 a 65 anos</td>
                            <td align='right'>".$faixa_7."</td>
                        </tr>";
            }
            
            if ($faixa_8 != 0) {
                echo "<tr>
                            <td align='left'>Acima de 65 anos</td>
                            <td align='right'>".$faixa_8."</td>
                        </tr>";
            }
            
            if (($faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8) != 0) {
                echo "<tr>
                        <td colspan='3' align='right'><b>TOTAL:</b> ",$faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8,"</td>
                      </tr>";
            }

			$total_geral += ($faixa_1+$faixa_2+$faixa_3+$faixa_4+$faixa_5+$faixa_6+$faixa_7+$faixa_8);
			
			$total_faixa_1 += $faixa_1;
			$total_faixa_2 += $faixa_2;
			$total_faixa_3 += $faixa_3;
			$total_faixa_4 += $faixa_4;
			$total_faixa_5 += $faixa_5;
			$total_faixa_6 += $faixa_6;
			$total_faixa_7 += $faixa_7;
			$total_faixa_8 += $faixa_8;
			
			$faixa_1 = 0;
            $faixa_2 = 0;
            $faixa_3 = 0;
            $faixa_4 = 0;
            $faixa_5 = 0;
            $faixa_6 = 0;
            $faixa_7 = 0;
            $faixa_8 = 0;
		}
		echo "
			<tr>
				<td colspan='3'><hr /></td>
			</tr>
			<tr>
				<td colspan='3'>
					<b>TOTAL POR FAIXA ETÁRIA</b>
				</td>
			</tr>";
			
			if ($total_faixa_1 != 0) {
                echo "
				<tr>
					<td align='left'>0 a 1 ano</td>
					<td align='right'>".$total_faixa_1."</td>
				</tr>";
            }
            
            if ($total_faixa_2 != 0) {
                echo "
				<tr>
					<td align='left'>1 a 5 anos</td>
					<td align='right'>".$total_faixa_2."</td>
				</tr>";
            }
                    
            if ($total_faixa_3 != 0) {
                echo "
				<tr>
					<td align='left'>5 a 12 anos</td>
					<td align='right'>".$total_faixa_3."</td>
				</tr>";
            }
            
            if ($total_faixa_4 != 0) {
                echo "
				<tr>
					<td align='left'>12 a 19 anos</td>
					<td align='right'>".$total_faixa_4."</td>
				</tr>";
            }
            
            if ($total_faixa_5 != 0) {
                echo "
				<tr>
					<td align='left'>19 a 25 anos</td>
					<td align='right'>".$total_faixa_5."</td>
				</tr>";
            }
            
            if ($total_faixa_6 != 0) {
                echo "
				<tr>
					<td align='left'>25 a 49 anos</td>
					<td align='right'>".$total_faixa_6."</td>
				</tr>";
            }
            
            if ($total_faixa_7 != 0) {                    
                echo "
				<tr>
					<td align='left'>49 a 65 anos</td>
					<td align='right'>".$total_faixa_7."</td>
				</tr>";
            }
            
            if ($total_faixa_8 != 0) {
                echo "
				<tr>
					<td align='left'>Acima de 65 anos</td>
					<td align='right'>".$total_faixa_8."</td>
				</tr>";
            }
		echo "
			<tr>
				<td align='right' colspan='3'><b>TOTAL GERAL:</b> $total_geral</td>
			</tr>";
		echo "</table>";
	}
?>
</body>
