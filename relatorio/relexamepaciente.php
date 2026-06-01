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
	
   $med = pg_fetch_array(pg_query("select *from medico where med_codigo = $med_codigo"));
            echo "<table  width=100% cellspacing=0 cellpadding=0 border=0>\n";
                echo " <tr>\n";
                echo "  <td colspan=2><font size=1 face=courier>".strtoupper($Tit)."</font></td>\n";
                echo " </tr>\n";
                echo " <tr>\n";
                echo "  <td colspan=2><font size=1 face=courier>Laboratorio:  $med[med_nome] </font></td>\n";
                echo " </tr>\n";
                echo " <tr>\n";
                echo "  <td colspan=2><font size=1 face=courier>Periodo: $dt_inicial a $dt_final</font></td>\n";
                echo "</tr>\n";
                echo "</table>\n";
                echo "<br>\n";


$query = "SELECT sum(cit.coni_valor) as valor,proc_nome,count(cit.proc_codigo) as total
                                                                FROM agenda as age
                                                                JOIN agenda_itens as agt
                                                                  ON agt.age_codigo = age.age_codigo
                                                                JOIN convenio_itens as cit
                                                                  ON cit.coni_codigo = agt.coni_codigo
                                                                JOIN convenio as cnv
                                                                  ON cnv.conv_codigo = cit.conv_codigo
                                                                JOIN procedimento as proc
                                                                  ON proc.proc_codigo = cit.proc_codigo
																JOIN coleta col
																  ON col.agei_codigo=agt.agei_codigo
                                                               WHERE agei_data >= '$dt_inicial'
                                                                 AND agei_data <= '$dt_final'
 		".($med_codigo == "" ? "" :"and cnv.med_codigo = $med_codigo")."
 		".($proc_codigo == "" ? "" :"and proc.proc_codigo = $proc_codigo")."
                                                                GROUP BY cit.coni_valor,proc_nome,cit.proc_codigo";
//echo $query;exit;
$sql = pg_query($query);


  echo "<table width=100% cellspacing=0 cellpadding=4 border=1>";
  echo "<tr>
	 <td width=80%><font face=verdana size=2><b>Procedimento</b></font></td>
	 <td><font face=verdana size=2><b>Quantidade</b></font></td>
	 <td><font face=verdana size=2><b>Valor</b></font></td>
	</tr>";
$i=0;
while($row = pg_fetch_array($sql)) {
$i++;
  echo "<tr>
	 <td><font face=verdana size=3><b>$row[proc_nome]</b></font></td>
	 <td><font face=verdana size=2>$row[total]</font></td>
	 <td><font face=verdana size=2>".formata_valor($row[valor])."</font></td>
	</tr>";
 $j 	= $i;
  $tot += $row[total];
  $vlt += $row[valor];
}
  echo "<tr>
	 <td align=right><font face=verdana size=2><b>Total Agendados no Periodo:</b></font></td>
	 <td width=20% align=center><font face=verdana size=4><b>$tot</b></font></td>
	 <td width=20% align=center><font face=verdana size=4 color=red><b>$vlt</b></font></td>
	</tr>";
  echo "</table>";



?>
