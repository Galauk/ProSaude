<script>
	function imprimir(){
		window.print();
		//para limpar os campos do agendamento.
		window.opener.limpar();
	}
</script>
<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
#cabecario( $hotkey = true);

reglog($id_login,"Acessando Digitacao do Resultado");

echo "<body onload='imprimir();'>";
$i=0;
$sql = pg_query("select *from materialdeanalise as mlz left join itensdoexame as itx on itx.itx_codigo = mlz.itx_codigo left join procedimento as proc on proc.proc_codigo = itx.proc_codigo left join tipodeexame as tp on tp.proc_codigo = itx.proc_codigo where mlz.cad_exame = $cad_exame and proc.proc_codigo = $proc_codigo");

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td><font face=verdana size=2>";
$cadex = pg_fetch_array(pg_query("select med_codigo,usu_codigo,to_char(cad_datapedido,'DD/MM/YYYY') as cad_datapedido from cadastrodoexame where cad_exame = $cad_exame"));
$usu = pg_fetch_array(pg_query("select *from usuario where usu_codigo = $cadex[usu_codigo]"));
$med = pg_fetch_array(pg_query("select *from medico where med_codigo = $cadex[med_codigo]"));
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td width=130><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/logo_castro.jpg></td>
	 <td valign=top><font size=4>LABORATORIO MUNICIPAL DE CASTRO</font><br>
	<font size=2>PACIENTE: $usu[usu_nome]<br>
	<font size=2>DATA DO PEDIDO: $cadex[cad_datapedido]<br>
	<font size=2>MEDICO SOLICITANTE: $med[med_nome]
	</font>
	</td>
	</tr>
	</table>";

while($row=pg_fetch_array($sql)) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
       <tr bgcolor=FFFFFF>
        <td><b><font size=2>$row[proc_nome]</font></b></td>
       </tr>
      </table>";
$query = pg_query("select *from subexame where txa_codigo = '$row[txa_codigo]'");
$qq = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]'  order by vlr_codigo"));



#
# POSSUI SUBEXAME E NAO POSSUI O VALOR DE REFERENCIA
#
if((pg_num_rows($query)!="0" AND $qq[vlr_valordereferencia]=="''")) {
echo "
     <table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=2>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";
 $qq = pg_query("select *from resultadoexame where cad_exame = '$cad_exame' and proc_codigo = '$proc_codigo'") or die(pg_last_error());
   while($vlr=pg_fetch_array($qq)) {
 $vl = pg_fetch_array(pg_query("select *from valoresdereferencia where vlr_codigo = '$vlr[sex_codigo]'  order by vlr_codigo"));
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vl[ite_codigo]'"));
             echo "<tr bgcolor=FFFFFF>
                   <td width='23%'><font size=1>$ite[ite_itemdoexame]</td>
		   <td> <font size=1>:..........................: </td>
                   <td width='*'>&nbsp;&nbsp;<font size=1>$vlr[vlr_valor]</td>
                   <td align=right width='70%'><font size=1>$vlr[vlr_valordereferencia]</font></td>
                  </tr>";
}
        echo "</table>";
 $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1>Observacao:</td>
          <td><font size=1>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }
}

#
# NAO POSSUI SUBEXAME
#
if((pg_num_rows($query)=="0" AND $qq[vlr_valordereferencia]!="''")) {
echo "
     <table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=2>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";
 $ite = pg_fetch_array(pg_query("select *from tipodeexame where proc_codigo = '$proc_codigo'"));
 $vl = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo = '$ite[txa_codigo]' order by vlr_codigo"));
 $qq = pg_query("select *from resultadoexame where cad_exame = '$cad_exame' and proc_codigo = '$proc_codigo'") or die(pg_last_error());
   while($vlr=pg_fetch_array($qq)) {
     $count = (strlen($vl[vlr_valordereferencia])+5);
             echo "<tr bgcolor=FFFFFF>
                   <td width='".$count."%'><font size=1>$vl[vlr_valordereferencia]</td>
		   <td> <font size=1>:..........................: </td>
                   <td width='15%'>&nbsp;&nbsp;<font size=1>$vlr[vlr_valor]</td>
                   <td align=right width='70%'><font size=1>$vlr[vlr_valordereferencia]</font></td>
                  </tr>";
}
        echo "</table>";
 $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1>Observacao:</td>
          <td><font size=1>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }
}

#
# NAO POSSUI SUBEXAME E NAO POSSUI O VALOR DE REFERENCIA
#
if((pg_num_rows($query)=="0" AND $qq[vlr_valordereferencia]=="''")) {
echo "
     <table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=2>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";
 $qq = pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo");
   while($vlr=pg_fetch_array($qq)) {
$i++;
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
 $vl = pg_fetch_array(pg_query("select *from resultadoexame where sex_codigo = '$vlr[vlr_codigo]'"));
             echo "<tr bgcolor=FFFFFF>
                   <td width='23%'><font size=1>$ite[ite_itemdoexame]</td>
		   <td> <font size=1>:..........................: </td>
                   <td width='*'>&nbsp;&nbsp;<font size=1>$vl[vlr_valor]</td>
                   <td align=right width='70%'><font size=1>$vlr[vlr_valordereferencia]</font></td>
                  </tr>";
}
        echo "</table>";
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1>Observacao:</td>
          <td><font size=1>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }
}

#
# POSSUI SUBEXAME E POSSUI O VALOR DE REFERENCIA
#
if((pg_num_rows($query)!="0" AND $qq[vlr_valordereferencia]!="''")) {
while($sub=pg_fetch_array($query)) {
echo "<table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>
       <tr bgcolor=FFFFFF>
        <td><font size=1><b>".strtoupper($sub[sex_subexame])."</b></font></td>
       </tr>
      </table>
     <table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=2>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";

$qq = pg_query("select *from valoresdereferencia where sex_codigo = '$sub[sex_codigo]' order by vlr_codigo");
 while($vlr=pg_fetch_array($qq)) {

 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
 $vl = pg_fetch_array(pg_query("select *from resultadoexame where sex_codigo = '$vlr[vlr_codigo]'"));
             echo "<tr bgcolor=FFFFFF>
                   <td width='20%'><font size=1>$ite[ite_itemdoexame] $cacSize</td>
		   <td> <font size=1>:..........................:</td>
                   <td width='20%'>&nbsp;&nbsp;<font size=1>$vl[vlr_valor]</td>
		   </tr>
		   <tr>
		     <td>&nbsp;</td>
		     <td>&nbsp;</td>
	";
if($vlr[vlr_valordereferencia]=="") {
  echo "<td width='70%'>&nbsp;</td>";
} else {
  echo "<td width='70%'><font size=1>&nbsp;&nbsp;&nbsp;$vlr[vlr_valordereferencia]</font></td>";
}
echo "</tr>";

$teste .= $vl[res_observacao];
$teste2 .= $vl[res_conclusao];
}
 $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
echo "</table>";
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1>Observacao:</td>
          <td><font size=1>$obs[res_observacao]</td>
        </tr>
        </table><br>";
  }
 }

}
        echo "</td>
          </tr>
         </table>";
$txa_codigo = $row[txa_codigo];
$tpmed_codigo = $row[12];
 }
$conc = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_conclusoes!='' and proc_codigo = $proc_codigo"));
if($conc[res_conclusoes]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1 face=verdana>Conclusao:</td>
        </tr>
        <tr>
          <td><font size=1 face=verdana>$conc[res_conclusoes]</td>
        </tr>
        </table><br>";
}
$val = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo = $txa_codigo"));
$tp = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = '$val[man_codigo]'"));
$mat = pg_fetch_array(pg_query("select *from tipodeexame as tp left join tipodematerial as tma on tp.tma_codigo = tma.tma_codigo where proc_codigo = $proc_codigo"));
$bio = pg_fetch_array(pg_query("select *from medico where med_codigo = $tpmed_codigo"));
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1 face=verdana>$mat[tma_tipo]&nbsp;&nbsp;&nbsp;<b>Metodo:</b> $tp[tpm_metodo]</td>
        </tr>
        </table><br><br><br><br>";
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=1 face=verdana><b>EXAME REALIZADO POR:</b> $bio[med_nome] - CRF: $bio[med_crm]</td>
        </tr>
        </table>";

?>
