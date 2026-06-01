<script language=javascript>

function imprimir()
{
    window.print();
}
</script>

<!--<body onload='imprimir()'>-->

<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

            if($uni_codigo){
              $unidade = pg_fetch_array(pg_query("select uni_desc from unidade where uni_codigo = $uni_codigo"));
            }
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0>\n";
                    echo " <tr>\n";
                echo "  <td width=200><font size=1 face=courier>GEST�O P�BLICA DE SA�DE</font></td>\n";
                echo "  <td width=10 align=right><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
                echo " </tr>\n";
                echo " <tr>\n";
                echo "  <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>\n";
                echo " </tr>\n";
                if($uni_codigo){ 
                  echo " <tr>\n";
                  echo "  <td colspan=2><font size=1 face=courier>Laboratorio:  $unidade[uni_desc] </font></td>\n";
                  echo " </tr>\n";
                }
                echo " <tr>\n";
                echo "  <td colspan=2><font size=1 face=courier>Periodo: $dt_inicial a $dt_final</font></td>\n";
                echo "</tr>\n";
                echo "</table>\n";
                echo "<br>\n";

          $andUnidade = !empty($uni_codigo) ? " AND agt.uni_codigo_coleta = '$uni_codigo'" : '';

					$sql = "SELECT proc_nome, agt.agei_valor as agei_valor, count(cit.proc_codigo) as total
								FROM agenda as age 
								JOIN agenda_itens as agt 
								ON agt.age_codigo = age.age_codigo 
								JOIN convenio_itens as cit 
								ON cit.coni_codigo = agt.coni_codigo 
								JOIN convenio as cnv 
								ON cnv.conv_codigo = cit.conv_codigo 
								JOIN procedimento as proc 
								ON proc.proc_codigo = cit.proc_codigo
                LEFT JOIN unidade as uni
                ON agt.uni_codigo_coleta = uni.uni_codigo
								WHERE agei_data >= '$dt_inicial' 
								AND agei_data <= '$dt_final' 
                $andUnidade
								GROUP BY proc_nome,cit.proc_codigo, agt.agei_valor";
                  $query = pg_query($sql);
                  
  echo "<table width=100% cellspacing=0 cellpadding=4 border=1>";
  echo "<tr>
	 <td width=60%><font face=verdana size=2><b>Procedimentos</b></font></td>
	 <td width=20%><font face=verdana size=2><b>Total Agendados</b></font></td>
   <td width=10%><font face=verdana size=2><b>Valor Unit&aacute;rio</b></font></td>
   <td width=10%><font face=verdana size=2><b>Valor Total</b></font></td>
	</tr>";
  //die(var_dump($sql));
  $totalValor = 0;
while($row = pg_fetch_assoc($query)) {
  //die(var_dump($row[proc_nome]));
  $medCalc = $row[agei_valor]*$row[total];
  $totalValor = $totalValor + $medCalc;
  echo "<tr>
	 <td><font face=verdana size=2>$row[proc_nome]</font></td>
	 <td align=center><font face=verdana size=2><b>$row[total]</b></font></td>
   <td align=center><font face=verdana size=2><b>$row[agei_valor]</b></font></td>
   <td align=center><font face=verdana size=2><b>$medCalc</b></font></td>
	</tr>";
 $total += $row[total];
}
  echo "<tr>
	 <td align=right><font face=verdana size=2><b>Total Agendados no Periodo:</b></font></td>
	 <td colspan=2 width=20% align=center><font face=verdana size=4><b>$total</b></font></td>
	</tr>";
  echo "<tr>
   <td align=right><font face=verdana size=2><b>Valor Total Por Per&iacute;odo:</b></font></td>
   <td colspan=2 width=20% align=center><font face=verdana size=4><b>$totalValor</b></font></td>
  </tr>";
  echo "</table>";



?>
