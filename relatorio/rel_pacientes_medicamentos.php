<?php
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

$cod_pac = $_GET['cod_pac'];
$cod_pro = $_GET['cod_pro'];
$data_ini = $_GET['data_ini'];
$data_fim = $_GET['data_fim'];
$centro_estocador = $_GET['centro_estocador'];

if($cod_pac == "") $cod_pac = -1;

$titulo_rel = "Pacientes por Medicamento";
$Tit = "RELATÓRIO DE PACIENTES POR MEDICAMENTO";
$dtIni = $data_ini;
$dtFin = $data_fim;
include 'cabecalho.php';

// Pega o nome do Centro Estocador
if ($centro_estocador != -1) {
  $sql_ce = "SELECT set_nome FROM setor WHERE set_codigo = $centro_estocador";
  $query_ce = db_query($sql_ce);
  $row_ce = pg_fetch_array($query_ce);
  $ce_nome = $row_ce[set_nome];
  
  $campos = "b.set_saida, e.set_nome,";
  $tabela = ", setor e";
  $condicao = "AND b.set_saida = e.set_codigo AND b.set_saida = $centro_estocador";
  
  echo "<b>CENTRO ESTOCADOR:</b> ".$ce_nome;
} else {
  $campos = "b.set_saida, e.set_nome,";
  $tabela = ", setor e";
  $condicao = "AND b.set_saida = e.set_codigo";
  
  echo "<b>CENTRO ESTOCADOR:</b> Todos";
}

?>
<SCRIPT Language="Javascript">
function imprimir()
{
	window.print() ;
}
</script>
<head>
    <title><?=$titulo_rel?></title>
</head>

<body>
<?
echo $cabecalho;

// Inicio da Gambiarra
  list($d1,$m1,$a1) = explode("/",$data_ini);
  list($d2,$m2,$a2) = explode("/",$data_fim);
  
  // Zebragem
  $controle = 0;

  if ($cod_pac == -1) {
    // Lista todos os pacientes e todos os produtos
    if ($cod_pro == -1) {
        echo "<table width=100%>";
        $sql = "SELECT a.usu_nome, b.mov_data, b.mov_codigo, $campos b.mov_num_receita, d.pro_nome, c.ite_quantidade
                FROM usuario a, movimento b, itens_movimento c, produto d $tabela
                WHERE a.usu_codigo = b.usu_codigo
                AND b.mov_codigo = c.mov_codigo
                AND c.pro_codigo = d.pro_codigo
                $condicao
                AND b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
                ORDER BY 2,5"; 
        $query = pg_query($sql) or die(pg_last_error());
        $resultado = pg_num_rows($query);
            if ($resultado != 0) {
                $verif_total = 0;
		while($row = pg_fetch_array($query))
		{
			$total_geral += round($row[ite_quantidade]);
			
			$c1 = "";
			$c2 = "#A6A6A6";
			
			if ($controle == 0) {
			  $cor = $c1;
			  $controle++;
			} else {
			  $cor = $c2;
			  $controle = 0;
			}
			
			list($ano,$mes,$dia) = explode("-",$row['mov_data']);
			$data_form = $dia."/".$mes."/".$ano;
			if ($pro_controle == $row['pro_nome'])
			{
				echo "<tr bgcolor='$cor'>
					<td align='left'>".$data_form."</td>
					<td align='left'>".$row['mov_codigo']."</td>
					<td align='left'>".$row['mov_num_receita']."</td>
					<td>".$row['usu_nome']."</td>
					<td align='right'>".round($row['ite_quantidade'])."</td>
					</tr>";
				$total_pac++;
			}
			else
			{
				if( $verif_total > 0 )
				{
					echo "<tr><td colspan=5><b>Total de Pacientes: $total_pac</b></td></tr>";
				}
				echo "<tr><td colspan=5>&nbsp;</td></tr>
				<tr><td colspan=5><b>MEDICAMENTO:</b> ".$row['pro_nome']."</td></tr>
                <tr><td colspan=5><b>CENTRO ESTOCADOR:</b> ".$row['set_nome']."</td></tr>
					<tr>
					<td><b>DATA</b></td>
					<td><b>N&#186; MOV</b></td>
					<td><b>N&#186; REC</b></td>
					<td><b>PACIENTE</b></td>
					<td align='right'><b>QTDE</b></td>
					</tr>
					<tr bgcolor='$cor'>
					<td align='left'>".$data_form."</td>
					<td align='left'>".$row['mov_codigo']."</td>
					<td align='left'>".$row['mov_num_receita']."</td>
					<td>".$row['usu_nome']."</td>
					<td align='right'>".round($row['ite_quantidade'])."</td>
					</tr>";
				$verif_total++;
				$total_pac = 1;
			}
			// Variavel de controle pra gambiarra funcionar
			$pro_controle = $row['pro_nome'];
		}
		echo "<tr><td colspan=5><b>Total de Pacientes: $total_pac</b></td></tr>";
            } else {
                echo "<tr>
                            <td colspan=5>Nenhum registro encontrado</td>
                      </tr>";
            }
        echo "<tr><td align='right' colspan='5'><b>Total:</b> $total_geral</td></tr></table>";
    // Lista todos os pacientes de um determinado produto
    } else {
        echo "<table width=100%>";
        $sql = "SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, $campos d.pro_nome, c.ite_quantidade 
                        FROM usuario a, movimento b, itens_movimento c, produto d $tabela
                        WHERE a.usu_codigo = b.usu_codigo 
                        AND b.mov_codigo = c.mov_codigo 
                        AND c.pro_codigo = d.pro_codigo 
                        $condicao
                        AND b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2' 
                        AND c.pro_codigo = $cod_pro 
                        ORDER BY 2,5"; 

        $query = pg_query($sql) or die(pg_last_error());
        $resultado = pg_num_rows($query);
            if ($resultado != 0) {
                while($row = pg_fetch_array($query)) {
				  
					$total_pac++;
				  
					$total_geral += round($row[ite_quantidade]);
					$c1 = "";
					$c2 = "#F2F2F2";
					
					if ($controle == 0) {
					  $cor = $c1;
					  $controle++;
					} else {
					  $cor = $c2;
					  $controle = 0;
					}
					
                    list($ano,$mes,$dia) = explode("-",$row['mov_data']);
                    $data_form = $dia."/".$mes."/".$ano;
                    if ($pro_controle == $row['pro_nome']) {
                        echo "<tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['usu_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    } else {
                        echo "<tr><td colspan=5>&nbsp;</td></tr>
                        <tr><td colspan=5><b>MEDICAMENTO:</b> ".$row['pro_nome']."</td></tr>
                                <tr>
                                    <td><b>DATA</b></td>
                                    <td><b>N&#186; MOV</b></td>
                                    <td><b>N&#186; REC</b></td>
                                    <td><b>PACIENTE</b></td>
                                    <td><b>CENTRO ESTOCADOR</b></td>
                                    <td align='right'><b>QTDE</b></td>
                                </tr>
                                <tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['usu_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    }
                    // Variavel de controle pra gambiarra funcionar
                    $pro_controle = $row['pro_nome'];
                }
				echo "<tr><td colspan=5><b>Total de Pacientes: $total_pac</b></td></tr>";
            } else {
                echo "<tr>
                            <td colspan=4>Nenhum registro encontrado</td>
                      </tr>";
            }
        echo "<tr><td align='right' colspan='6'><b>Total:</b> $total_geral</td></tr></table>";
    }
  // Lista um paciente 
  } else {
    // Lista um paciente para todos os produtos
    if ($cod_pro == -1) {
        echo "<table width=100%>";
        $sql = "(SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, $campos d.pro_nome, c.ite_quantidade 
                        FROM usuario a, movimento b, itens_movimento c, produto d $tabela
                        WHERE a.usu_codigo = b.usu_codigo 
                        AND b.mov_codigo = c.mov_codigo 
                        AND c.pro_codigo = d.pro_codigo 
                        $condicao
                        AND b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2' 
                        AND b.usu_codigo = $cod_pac 
                        ORDER BY 2,5)
						UNION
						(SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, $campos d.pro_nome, c.ite_quantidade 
                        FROM usuario a, movimento_bkp b, itens_movimento_bkp c, produto_bkp d $tabela
                        WHERE a.usu_codigo = b.usu_codigo 
                        AND b.mov_codigo = c.mov_codigo 
                        AND c.pro_codigo = d.pro_codigo 
                        $condicao
                        AND b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2' 
                        AND b.usu_codigo = $cod_pac 
                        ORDER BY 2,5)
						
						
						
						"; 

        $query = pg_query($sql) or die(pg_last_error());
        $resultado = pg_num_rows($query);
            if ($resultado != 0) {
                while($row = pg_fetch_array($query)) {
				  
					$total_geral += round($row[ite_quantidade]);
					$c1 = "";
					$c2 = "#F2F2F2";
					
					if ($controle == 0) {
					  $cor = $c1;
					  $controle++;
					} else {
					  $cor = $c2;
					  $controle = 0;
					}
					
                    list($ano,$mes,$dia) = explode("-",$row['mov_data']);
                    $data_form = $dia."/".$mes."/".$ano;
/*
                    if ($pro_controle == $row['pro_nome']) {
                        echo "<tr>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['usu_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    } else {
                        echo "<tr><td colspan=5>&nbsp;</td></tr>
                        <tr><td colspan=5><b>MEDICAMENTO:</b> ".$row['pro_nome']."</td></tr>
                                <tr>
                                    <td><b>DATA</b></td>
                                    <td><b>N&#186; MOV</b></td>
                                    <td><b>N&#186; REC</b></td>
                                    <td><b>PACIENTE</b></td>
                                    <td align='right'><b>QTDE</b></td>
                                </tr>
                                <tr>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['usu_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    }
*/
                    if ($usu_controle == $row['usu_nome']) {
                        echo "<tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['pro_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    } else {
                        echo "<tr><td colspan=5>&nbsp;</td></tr>
                        <tr><td colspan=5><b>PACIENTE:</b> ".$row['usu_nome']."</td></tr>
                                <tr>
                                    <td><b>DATA</b></td>
                                    <td><b>N&#186; MOV</b></td>
                                    <td><b>N&#186; REC</b></td>
                                    <td><b>MEDICAMENTO</b></td>
                                    <td><b>CENTRO ESTOCADOR</b></td>
                                    <td align='right'><b>QTDE</b></td>
                                </tr>
                                <tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['pro_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    }
                    // Variavel de controle pra gambiarra funcionar
                    $usu_controle = $row['usu_nome'];
                }
            } else {
                echo "<tr>
                            <td colspan=4>Nenhum registro encontrado</td>
                      </tr>";
            }
        echo "<tr><td align='right' colspan='6'><b>Total:</b> $total_geral</td></tr></table>";
    } else {
        // Lista de um paciente e de um produto
        echo "<table width=100%>";
        $sql = "SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, $campos d.pro_nome, c.ite_quantidade 
                    FROM usuario a, movimento b, itens_movimento c, produto d $tabela
                    WHERE a.usu_codigo = b.usu_codigo 
                    AND b.mov_codigo = c.mov_codigo 
                    AND c.pro_codigo = d.pro_codigo 
                    $condicao
                    AND b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2' 
                    AND b.usu_codigo = $cod_pac 
                    AND c.pro_codigo = $cod_pro 
                    ORDER BY 2,5";
       
        $query = pg_query($sql) or die(pg_last_error());
        $resultado = pg_num_rows($query);
            if ($resultado != 0) {
                while($row = pg_fetch_array($query)) {
				  
					$total_geral += $row[ite_quantidade];
					$c1 = "";
					$c2 = "#F2F2F2";
					
					if ($controle == 0) {
					  $cor = $c1;
					  $controle++;
					} else {
					  $cor = $c2;
					  $controle = 0;
					}
					
                    list($ano,$mes,$dia) = explode("-",$row['mov_data']);
                    $data_form = $dia."/".$mes."/".$ano;
                    if ($usu_controle == $row['usu_nome']) {
                        echo "<tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['pro_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    } else {
                        echo "<tr><td colspan=5>&nbsp;</td></tr>
                        <tr><td colspan=5><b>PACIENTE:</b> ".$row['usu_nome']."</td></tr>
                                <tr>
                                    <td><b>DATA</b></td>
                                    <td><b>N&#186; MOV</b></td>
                                    <td><b>N&#186; REC</b></td>
                                    <td><b>MEDICAMENTO</b></td>
                                    <td><b>CENTRO ESTOCADOR</b></td>
                                    <td align='right'><b>QTDE</b></td>
                                </tr>
                                <tr bgcolor='$cor'>
                                    <td align='left'>".$data_form."</td>
                                    <td align='left'>".$row['mov_codigo']."</td>
                                    <td align='left'>".$row['mov_num_receita']."</td>
                                    <td>".$row['pro_nome']."</td>
                                    <td>".$row['set_nome']."</td>
                                    <td align='right'>".round($row['ite_quantidade'])."</td>
                                </tr>";
                    }
                    // Variavel de controle pra gambiarra funcionar
                    $usu_controle = $row['usu_nome'];
                }
            } else {
                echo "<tr>
                            <td colspan=4>Nenhum registro encontrado</td>
                      </tr>";
            }
        echo "<tr><td align='right' colspan='6'><b>Total:</b> $total_geral</td></tr></table>";
    }
  }
//print $sql;
 //die($sql);
?>
</body>
