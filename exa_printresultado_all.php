<link href="estilo_exame.css" rel="stylesheet" type="text/css">
<script>
        function imprimir()
        {
                window.print();
                //para limpar os campos do agendamento.
                window.opener.limpar();
                //
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

echo "<table  cellspacing=0 cellpadding=0 border='0'>
         <tr>
          <td><font face=verdana size=2>";
$cadex = pg_fetch_array(pg_query("select med_codigo,usu_codigo,to_char(cad_datapedido,'DD/MM/YYYY') as cad_datapedido,cad_medico_externo,cad_exame from cadastrodoexame where cad_exame = $cad_exame"));
$usu = pg_fetch_array(pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') usu_data,* from usuario where usu_codigo = $cadex[usu_codigo]"));
if($cadex[cad_medico_externo]=='E'){
	$med = pg_fetch_array(pg_query("select * from medico where med_codigo = $cadex[med_codigo]"));
	$nomeMedico = $med[med_nome];
}else{
	$med = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = $cadex[med_codigo]"));
	$nomeMedico = $med[usr_nome];
}
echo "<table width=90% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td rowspan=6 width=20><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/brasao.png'> </td>
	</tr>
	<tr>
	 <td> </td>
	 <td valign=top><font size=5>LABORAT&Oacute;RIO MUNICIPAL DE NOVA ESPERAN&Ccedil;A</font></td>
	</tr>
	<tr>
	 <td> </td>
	 <td valign=top><font size=4>PACIENTE: $usu[usu_nome]</td>
	</tr>
	<tr>
	 <td> </td>
	 <td valign=top><font size=4>DATA NASCIMENTO: $usu[usu_data]</td>
	</tr>
	<tr>
	 <td> </td>
	 <td valignt=top><font size=4>DATA DO PEDIDO: $cadex[cad_datapedido]</td>
	</tr>
	<tr>
	 <td> </td>
	 <td valign=top><font size=4>MEDICO SOLICITANTE: $nomeMedico
	</tr>
     </table>";

for($y=0; $y <= (count($DoPrint)-1); $y++) {
    $proc_codigo = $DoPrint[$y];
$sql = pg_query("select proc.proc_nome,*from materialdeanalise as mlz left join itensdoexame as itx on itx.itx_codigo = mlz.itx_codigo left join procedimento as proc on proc.proc_codigo = itx.proc_codigo left join tipodeexame as tp on tp.proc_codigo = itx.proc_codigo where mlz.cad_exame = $cad_exame and proc.proc_codigo = $proc_codigo");
while($row=pg_fetch_array($sql)) {
$proc_nome = $row[proc_nome];
$query = pg_query("select *from subexame where txa_codigo = '$row[txa_codigo]'");
$valorR = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo"));

//
//-> Seria pra funcionar so quando nao tem nda

if((pg_num_rows($query)!="0" AND $valorR[vlr_valordereferencia]!="")) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
       <tr bgcolor=FFFFFF>
        <td><b><font size=6>$row[procnome]</font></b></td>
       </tr>
      </table>";
}
$val = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo = $row[txa_codigo]"));
$tp = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = '$val[man_codigo]'"));
$mat = pg_fetch_array(pg_query("select *from tipodeexame as tp left join tipodematerial as tma on tp.tma_codigo = tma.tma_codigo where proc_codigo = $proc_codigo"));
$bio = pg_fetch_array(pg_query("select *from medico where med_codigo = $tpmed_codigo"));
echo "<br/> <br/>";
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
         <td><font size=3 face=verdana> <b>Tipo de Metodo:</b> $tp[tpm_metodo]</td>
        </tr>
        <tr>
          <td width=8%><font size=3 face=verdana><b> Tipo de material:</b> $mat[tma_tipo]
        </tr>
        </table>";
        echo "<br/><br/><br/><br/>";



#
# POSSUI SUBEXAME E NAO POSSUI O VALOR DE REFERENCIA
#
if((pg_num_rows($query)!="0" AND $valorR[vlr_valordereferencia]=="")) {
echo "
     <table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=6>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";
 $qq = pg_query("select *from resultadoexame where cad_exame = '$cad_exame' and proc_codigo = '$proc_codigo'") or die(pg_last_error());
   while($vlr=pg_fetch_array($qq)) {

 $vl = pg_fetch_array(pg_query("select *from valoresdereferencia where sex_codigo = '$vlr[sex_codigo]' order by vlr_codigo"));
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vl[ite_codigo]'"));
             echo "<tr bgcolor=FFFFFF>
                  <td width='23%'><font size=5>$ite[ite_itemdoexame]</td>
		   <td> <font size=2>:.......................................: </td>
                   <td width='*'>&nbsp;&nbsp;<font size=5>$vlr[vlr_valor]</td>
                   <td align=right width='70%'><font size=5>$vlr[vlr_valordereferencia]</font></td>
                  </tr>";
}
        echo "</table>";
/* $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=5>Observacao:</td>
          <td><font size=5>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }*/
}

#
# NAO POSSUI SUBEXAME
#
if((pg_num_rows($query)=="0" AND $valorR[vlr_valordereferencia]!="")) {
echo "<table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
	<tr>
		<td colspan=4 align=right>
			<b>V.R</b>
		</td>
	</tr>";
 $ite = pg_fetch_array(pg_query("select *from tipodeexame where proc_codigo = '$proc_codigo'"));
 $vl = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo = '$ite[txa_codigo]' order by vlr_codigo"));
 $qq = pg_query("select *from resultadoexame where cad_exame = '$cad_exame' and proc_codigo = '$proc_codigo'") or die(pg_last_error());


 $ta = pg_query("select *from valoresdereferencia where txa_codigo = '$ite[txa_codigo]' order by vlr_codigo");
 echo "<tr bgcolor=FFFFFF>
       	 <td colspan=4 align=left><font size=5 face=verdana color=#010976>&nbsp;<b>$proc_nome</td>
	</tr>";

while($tt=pg_fetch_array($ta)) {
	$ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vl[ite_codigo]'"));
 	$count = ($count=="")?"17":(strlen($vl[vlr_valordereferencia])+24);
	$vlr=pg_fetch_array($qq);
		echo "<tr>
			 <td align=right width=500>$ite[ite_itemdoexame] </td>
		  	 <td align=center> <font size=2>:.................................: </td>
			 <td align=right>&nbsp;&nbsp;<font size=5>$vlr[vlr_valor]</font></td>
                  	 <td align=right>&nbsp;&nbsp;<font size=5>$tt[vlr_valordereferencia]</td>
                  </tr>";
             echo "<!--<tr bgcolor=FFFFFF>
                   <td ><font size=5>&nbsp;</td>
		   <td> <font size=5>&nbsp;</td>";

   echo "</tr>-->";
}
        echo "</table>";
 $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
/*if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=5>Observacao:</td>
          <td><font size=5>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }*/
}

#
# NAO POSSUI SUBEXAME E NAO POSSUI O VALOR DE REFERENCIA
if((pg_num_rows($query)=="0" AND $valorR[vlr_valordereferencia]=="")) {
echo "
     <table width='100%' align='center' cellspacing='1' cellpadding='0' border='0' class='lista'>
	      <tr>
		 <td colspan=4><font size=5 face=verdana color='#010976'><b>$proc_nome </td>
	      </tr>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=6>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'><b><font size=2>V.R</td>
              </tr>";
	

#

 $qq = pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo");
   while($vlr=pg_fetch_array($qq)) {


#
# TESTE
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
 $vl = pg_fetch_array(pg_query("select *from resultadoexame where sex_codigo = $vlr[vlr_codigo] and cad_exame = '$cadex[cad_exame]'"));
// $vl = pg_query("select *from resultadoexame where sex_codigo = '$vlr[vlr_codigo]'");
             echo "<tr bgcolor=FFFFFF>
                   <td width='23%'><font size=5>$ite[ite_itemdoexame]</td>
		   <td> <font size=2>:.................................: </td>";
             echo "<td width='*'>&nbsp;&nbsp;<font size=5>$vl[vlr_valor]</td>";
             echo "<td align=right width='70%'><font size=5>$vl[vlr_valordereferencia]</font></td>
                  </tr>";
}
        echo "</table>";
/* $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
if($obs[res_observacao]!="") {
echo "<br><table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=5>Observacao:</td>
          <td><font size=5>$obs[res_observacao]</td>
        </tr>
        </table><br>";
 }*/
}

#
# POSSUI SUBEXAME E POSSUI O VALOR DE REFERENCIA
#
if((pg_num_rows($query)!="0" AND $valorR[vlr_valordereferencia]!="")) {
//ake
echo "<table width='100% align='center' cellspacing='0' cellpadding='0' border='0'>
	<tr>
		<td>
			<font size=5 face=verdana color='#010976'><b>$proc_nome</b></font>
		</td>
	</tr>
	<tr>
        <td align=right>
          <font size='4'><b>V.R
        </td>
     </tr>

     </table>
<br/> <br/>";
while($sub=pg_fetch_array($query)) {
echo "<table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>
       <tr bgcolor=FFFFFF>
        <td><font size=5><b>".strtoupper($sub[sex_subexame])."</b></font></td>
       </tr>
</table>
     <table width='100%' align='center' cellspacing='0' cellpadding='0' border='0'>
              <tr bgcolor=FFFFFF>
                   <td width='*'><b><font size=6>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td width='*'>&nbsp;</td>
                   <td align=right width='*'>&nbsp;</td>
                  </tr>";
$qq = pg_query("select *from valoresdereferencia where sex_codigo = '$sub[sex_codigo]' order by vlr_codigo");
 while($vlr=pg_fetch_array($qq)) {
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
 $vl = pg_fetch_array(pg_query("select *from resultadoexame where sex_codigo = $vlr[vlr_codigo] and cad_exame = '$cadex[cad_exame]'"));
             echo "<tr bgcolor=FFFFFF>
                   <td width=23%><font size=5>$ite[ite_itemdoexame]</td>
		   <td> <font size=2>:..........................:</td>
                   <td  >&nbsp;&nbsp;<font size=5>$vl[vlr_valor]</td>
                   <td width=70% align=right><font size=5>$vlr[vlr_valordereferencia]</font></td>
                  </tr>";
$teste .= $vl[res_observacao];
$teste2 .= $vl[res_conclusao];
}
/* $obs = pg_fetch_array(pg_query("select *from resultadoexame where cad_exame = $cad_exame and res_observacao!='' and proc_codigo = $proc_codigo"));
echo "</table>";
if($obs[res_observacao]!="") {
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=5>Observacao:</td>
          <td><font size=5>$obs[res_observacao]</td>
        </tr>
        </table><br>";
  }*/
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
echo "<br/><br/><br/>";
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
          <td width=8%><font size=4 face=verdana><b>Observacao:</td>
        </tr>
        <tr>
          <td><font size=5 face=verdana>$conc[res_conclusoes]</td>
        </tr>
        </table><br>";
}

/*$val = pg_fetch_array(pg_query("select *from valoresdereferencia where txa_codigo = $txa_codigo"));
$tp = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = '$val[man_codigo]'"));
$mat = pg_fetch_array(pg_query("select *from tipodeexame as tp left join tipodematerial as tma on tp.tma_codigo = tma.tma_codigo where proc_codigo = $proc_codigo"));
$bio = pg_fetch_array(pg_query("select *from medico where med_codigo = $tpmed_codigo"));
echo "<br/> <br/>";
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
        <tr>
         <td><font size=3 face=verdana> <b>Tipo de Metodo:</b> $tp[tpm_metodo]</td>
        </tr>
        <tr>
          <td width=8%><font size=3 face=verdana><b> Tipo de material:</b> $mat[tma_tipo]
	</tr>
        </table>";
	echo "<br/><br/><br/><br/>";*/
}
echo "<table width=80 cellspacing=0 cellpadding=0 border=0>
        <tr>";
                $selectBioquimicos = "SELECT * FROM usuarios WHERE usr_tipo_medico = 'F'";
                $queryBioquimicos  = pg_query($selectBioquimicos);
                while($regBioquimicos = pg_fetch_array($queryBioquimicos)){
                        echo "<td align='center'>
                              &nbsp; ___________________________&nbsp;<br/>
                                <b>$regBioquimicos[usr_nome]<br/>
                                <b><i>$regBioquimicos[usr_num_conselho]
                             </td>";
                }
        echo"
        </tr>
        </table>";
?>
