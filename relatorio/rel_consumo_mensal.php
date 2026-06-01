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

$titulo_rel = "Consumo Mensal";
$cabecalho = "<table>
                <tr>
                    <td>AUTARQUIA MUNICIPAL DE SA&Uacute;DE<br>
                        GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE<br>
                        PER&Iacute;ODO: <b>$data_mes/$data_ano</b><br>
                        RELAT&Oacute;RIO DE CONSUMO MENSAL
                    <td valign='bottom'>DATA ".date('d/m/Y')."<br>HORA ".date('H:i')."
                </tr>
             </table>";
?>
<head>
    <title><?=$titulo_rel?></title>
</head>

<script language="javascript">
    window.print();
</script>

<body>
<?
echo $cabecalho;
$data = $data_mes."/".$data_ano;
$condicao_1 = "";
$condicao_2 = "";

// Condição da SQL
if ($setor != "-1") {
    $condicao_1 .= "AND b.set_codigo = $setor ";
}

if ($tipo_saida != "-1") {
    $condicao_2 .= "AND mov_tipo = '$tipo_saida' ";
}

if ($grupo != "-1") {
    $condicao_2 .= "AND gru.gru_codigo = $grupo ";
}

$sql = "SELECT b.set_nome, a.set_saida,
        count(a.set_saida) AS total
        FROM movimento a, setor b
        WHERE a.set_saida = b.set_codigo 
        AND a.mov_data IN (SELECT mov_data FROM movimento 
            WHERE TO_CHAR(mov_data,'mm/yyyy') = '$data')
        $condicao_1
        GROUP BY b.set_nome, a.set_saida ORDER BY total DESC";
$query = db_query($sql);
$resultado = pg_num_rows($query);
$total_geral = 0;

    if ($resultado != 0) {
        echo "<table width='100%' border='0'>";
        while($row = pg_fetch_array($query)) {
            $c1 = "";
            $c2 = "#A6A6A6";
            
            if ($controle == 0) {
              $cor = $c1;
              $controle++;
            } else {
              $cor = $c2;
              $controle = 0;
            }
                
            $sql_2 = "SELECT pro_nome, ( 
                            CASE 
                                WHEN mov_tipo = 'S' THEN 'SAIDA' 
                                WHEN mov_tipo = 'E' THEN 'ENTRADA' 
                                ELSE 'NENHUM' 
                            END 
                            ) AS mov_tipo, gru_nome, COUNT(set_saida) AS qtd FROM itens_movimento AS ite
                            LEFT JOIN movimento AS mov ON ite.mov_codigo = mov.mov_codigo
                            LEFT JOIN produto AS pro ON ite.pro_codigo = pro.pro_codigo
                            LEFT JOIN grupo AS gru ON pro.gru_codigo = gru.gru_codigo
                            WHERE mov_data IN (SELECT mov_data FROM movimento WHERE TO_CHAR(mov_data,'mm/yyyy') = '$data') 
                            AND set_saida = $row[set_saida]
                            $condicao_2
                            GROUP BY pro_nome, mov_tipo, gru_nome
                            ORDER BY pro_nome ASC";
            $query_2 = db_query($sql_2);
            $resultado_2 = pg_num_rows($query_2);
            
                if ($resultado_2 != 0) {
                    echo "<tr>
                                <td><b>SETOR:</b> $row[set_nome]</td>
                            </tr>";
                
                    echo "<table width='100%'>
                            <tr>
                                <td width='50%'><b>PRODUTO</b></td>
                                <td><b>TIPO DE SA&Iacute;DA</b></td>
                                <td width='30%'><b>GRUPO</b></td>
                                <td align='right'><b>QTD.</b></td>
                            </tr>";
                            
                    while($row_2 = pg_fetch_array($query_2)) {
                        echo "<tr>
                                    <td>$row_2[pro_nome]</td>
                                    <td>$row_2[mov_tipo]</td>
                                    <td>$row_2[gru_nome]</td>
                                    <td align='right'>$row_2[qtd]</td>
                                </tr>";
                        $total_geral += $row_2[qtd];
                    }
                    echo "</table><br><hr><br>";
                }
        }
        echo "</table>";
        echo "<table width='100%'>
                <tr>
                    <td align='right'><b>TOTAL GERAL:</b> $total_geral</td>
                </tr>
              </table>";
    } else {
        echo "Nenhum resultado encontrado para o per&iacute;odo $data";
    }
?>
</body>