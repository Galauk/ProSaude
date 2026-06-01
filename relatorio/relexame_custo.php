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
                echo "  <td width=200><font size=1 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>\n";
                echo "  <td width=10 align=right><font size=1 face=courier>".date("d/m/Y h:i:s")."</font></td>\n";
                echo " </tr>\n";
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
	$query= "select distinct(agx.proc_codigo),TRANSLATE(p.proc_nome, 'ZZZ-', '') as proc_nome,(select count(*) from agendamento_exame_lista where proc_codigo = agx.proc_codigo) as total,
	coalesce((select count(*) from agendamento_exame_lista where proc_codigo = agx.proc_codigo)*p.proc_valor) as total_vlr,p.proc_valor 
	from agendamento_exame as age,
	agendamento_exame_lista as agx,
	usuario as u,
	procedimento as p
	where age.agex_codigo = agx.agex_codigo 
	and age.usu_codigo = u.usu_codigo 
	and agx.usu_codigo = u.usu_codigo
	and agx.proc_codigo = p.proc_codigo
	".($med_codigo == "" ? "" :"and agx.med_codigo = $med_codigo")."
	".($proc_codigo == "" ? "" :"and agx.proc_codigo = $proc_codigo")."
	and agx.agexl_data between '$dt_inicial' and '$dt_final'
	group by p.proc_nome,agx.proc_codigo,p.proc_valor
	";
	//echo $query;exit;
  $sql = pg_query($query);
  echo "<table width=100% cellspacing=0 cellpadding=4 border=1>";
  echo "<tr>
	 <td><font face=verdana size=2><b>Procedimentos</b></font></td>
	 <td width=20%><font face=verdana size=2><b>Total Agendados</b></font></td>
	 <td width=20%><font face=verdana size=2><b>Custo Unitario</b></font></td>
	 <td width=20%><font face=verdana size=2><b>Total Calculado</b></font></td>
	</tr>";
while($row = pg_fetch_array($sql)) {
  echo "<tr>
	 <td><font face=verdana size=2>$row[proc_nome]</font></td>
	 <td align=center><font face=verdana size=3><b>$row[total]</b></font></td>
	 <td><font face=verdana size=2>$row[proc_valor]</font></td>
	 <td><font face=verdana size=2>$row[total_vlr]</font></td>
	</tr>";
 $total += $row[total];
 $total_vlr += $row[total_vlr];
}
  echo "<tr>
	 <td align=right><font face=verdana size=2><b>Total Agendados no Periodo:</b></font></td>
	 <td colspan=3 width=20%><font face=verdana size=4><b>$total</b></font></td>
	</tr>";
  echo "<tr>	
         <td align=right><font face=verdana size=2><b>Total Gasto em R$:</b></font></td>
         <td colspan=3 width=20%><font face=verdana size=4><b>$total_vlr</b></font></td>
        </tr>";
  echo "</table>";



?>
