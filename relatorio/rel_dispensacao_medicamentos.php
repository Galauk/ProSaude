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

$cod_pac = $_GET['cod_pac'];
$data_ini = $_GET['data_ini'];
$data_fim = $_GET['data_fim'];
if ($cod_pac == "") $cod_pac = -1;

$titulo_rel = "Dispensa&ccedil;&atilde;o de Medicamentos por Paciente";
$cabecalho = "<table>
                <tr>
                    <td>SECRET&Aacute;RIA MUNICIPAL DE SA&Uacute;DE<br>
                        GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE<br>
                        PER&Iacute;ODO: <b>$data_ini</b> AT&Eacute; <b>$data_fim</b><br>
                        RELAT&Oacute;RIO DE DISPENSA&Ccedil;&Atilde;O DE MEDICAMENTOS POR PACIENTE
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
  // Zebragem
  $controle = 0;

  // Inicio da Gambiarra
  list($d1,$m1,$a1) = explode("/",$data_ini);
  list($d2,$m2,$a2) = explode("/",$data_fim);
  
  if ($cod_pac == -1) {
    echo "<table>";
    $sql = "SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, d.pro_nome, c.ite_quantidade
            FROM usuario a, movimento b, itens_movimento c, produto d
            WHERE a.usu_codigo = b.usu_codigo AND
            b.mov_codigo = c.mov_codigo AND
            c.pro_codigo = d.pro_codigo AND
            b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2'
            ORDER BY 1,2"; 
    $query = pg_query($sql) or die(pg_last_error());
    $resultado = pg_num_rows($query);
        if ($resultado != 0) {
            while($row = pg_fetch_array($query)) {
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
                if ($pac_controle == $row['usu_nome']) {
                    echo "<tr bgcolor='$cor'>
                                <td align='center'>".$data_form."</td>
                                <td align='center'>".$row['mov_codigo']."</td>
                                <td align='center'>".$row['mov_num_receita']."</td>
                                <td>".$row['pro_nome']."</td>
                                <td align='center'>".round($row['ite_quantidade'])."</td>
                            </tr>";
                } else {
                    echo "<tr><td colspan=5>&nbsp;</td></tr>
                    <tr><td colspan=5><b>PACIENTE:</b> ".$row['usu_nome']."</td></tr>
                            <tr>
                                <td><b>DATA</b></td>
                                <td><b>N&#186; MOV</b></td>
                                <td><b>N&#186; REC</b></td>
                                <td><b>MEDICAMENTO</b></td>
                                <td><b>QTDE</b></td>
                            </tr>
                            <tr bgcolor='$cor'>
                                <td align='center'>".$data_form."</td>
                                <td align='center'>".$row['mov_codigo']."</td>
                                <td align='center'>".$row['mov_num_receita']."</td>
                                <td>".$row['pro_nome']."</td>
                                <td align='center'>".round($row['ite_quantidade'])."</td>
                            </tr>";
                }
                // Variavel de controle pra gambiarra funcionar
                $pac_controle = $row['usu_nome'];
            }
        } else {
            echo "<tr>
                        <td colspan=4>Nenhum registro encontrado</td>
                  </tr>";
        }
    echo "</table>";
  } else {
    echo "<table>";
    $sql = "SELECT a.usu_nome, b.mov_data, b.mov_codigo, b.mov_num_receita, d.pro_nome, c.ite_quantidade
            FROM usuario a, movimento b, itens_movimento c, produto d
            WHERE a.usu_codigo = b.usu_codigo AND
            b.mov_codigo = c.mov_codigo AND
            c.pro_codigo = d.pro_codigo AND
            b.mov_data BETWEEN '$a1$m1$d1' AND '$a2$m2$d2' AND
            b.usu_codigo = $cod_pac
            ORDER BY 1,2"; 
    $query = pg_query($sql) or die(pg_last_error());
    $resultado = pg_num_rows($query);
        if ($resultado != 0) {
            while($row = pg_fetch_array($query)) {
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
                if ($pac_controle == $row['usu_nome']) {
                    echo "<tr bgcolor='$cor'>
                                <td align='center'>".$data_form."</td>
                                <td align='center'>".$row['mov_codigo']."</td>
                                <td align='center'>".$row['mov_num_receita']."</td>
                                <td>".$row['pro_nome']."</td>
                                <td align='center'>".round($row['ite_quantidade'])."</td>
                            </tr>";
                } else {
                    echo "<tr><td colspan=5>&nbsp;</td></tr>
                    <tr><td colspan=5><b>PACIENTE:</b> ".$row['usu_nome']."</td></tr>
                            <tr>
                                <td><b>DATA</b></td>
                                <td><b>N&#186; MOV</b></td>
                                <td><b>N&#186; REC</b></td>
                                <td><b>MEDICAMENTO</b></td>
                                <td><b>QTDE</b></td>
                            </tr>
                            <tr bgcolor='$cor'>
                                <td align='center'>".$data_form."</td>
                                <td align='center'>".$row['mov_codigo']."</td>
                                <td align='center'>".$row['mov_num_receita']."</td>
                                <td>".$row['pro_nome']."</td>
                                <td align='center'>".round($row['ite_quantidade'])."</td>
                            </tr>";
                }
                // Variavel de controle pra gambiarra funcionar
                $pac_controle = $row['usu_nome'];
            }
        } else {
            echo "<tr>
                        <td colspan=4>Nenhum registro encontrado</td>
                  </tr>";
        }
    echo "</table>";
  }
?>
</body>
