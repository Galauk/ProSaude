<?php
/**
 * Arquivo Iframe do Itens Exame
*/
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

if(empty($cad_exame)) {
exit;
}
if(empty($acao)) {
echo "<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
	 <td><a href=$PHP_SELF?acao=form_add&cad_exame=$cad_exame><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
	</tr>
	</table>";
echo "<table width=100% cellpadding=4 cellspacing=1 border=0 style='border-top:1px solid;border-right:1px solid;border-left:1px solid;border-bottom:1px solid;'>
	<tr>
	 <td width=10 style='background-color:#c9c9c9'>Cod.</td>
	 <td style='background-color:#c9c9c9'>Exames</td>
	 <td width=10 style='background-color:#c9c9c9'>Status</td>
	 <td colspan=2 style='background-color:#c9c9c9'>&nbsp;</td>
	</tr>";
$sql = pg_query("select *from itensdoexame where cad_exame = $cad_exame");
while($row=pg_fetch_array($sql)) {
  if(trim($row[itx_status])=="P") {
     $status = "<font color=red><b>PENDENTE</b></font>";
  }
  if(trim($row[itx_status])=="R") {
     $status = "<font color=green><b>REALIZADO</b></font>";
  }
  if(trim($row[itx_status])=="E") {
     $status = "<font color=blue><b>ENTREGUE</b></font>";
  }
$rr = pg_fetch_array(pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as proc_nome,*from procedimento where proc_codigo = $row[proc_codigo]"));
echo "<tr>
	 <td width=10 align=center>$row[itx_codigo]</td>
	 <td>$rr[proc_nome]</td>
	 <td width=10>$status</td>
	 <td width=10><a href=$PHP_SELF?cad_exame=$cad_exame&acao=form_edit&itx_codigo=$row[itx_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btnedit_on.png border=0></a></td>
	 <td width=10><a href=$PHP_SELF?cad_exame=$cad_exame&acao=del&itx_codigo=$row[itx_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel_on.png border=0></a></td>
	</tr>";
}
echo "</table><br>";
}

if(($acao=="form_add" OR $acao=="form_edit")) {
echo "<form method=post action=$PHP_SELF>";
 if($acao=="form_edit") {
    $row = pg_fetch_array(pg_query("select *from itensdoexame where itx_codigo = $itx_codigo"));
if($row[itx_urgente]=="S") {
   $it_1 = "selected";
   $it_2 = "";
} else {
   $it_1 = "";
   $it_2 = "selected";
}
if($row[itx_status]=="P") {
   $ts1="selected";
   $ts2="";
   $ts3="";
}
if($row[itx_status]=="R") {
   $ts1="";
   $ts2="selected";
   $ts3="";
}
if($row[itx_status]=="E") {
   $ts1="";
   $ts2="";
   $ts3="selected";
 }
echo "<input type=hidden name=acao value=edit>";
echo "<input type=hidden name=itx_codigo value=$itx_codigo>";
echo "<input type=hidden name=cad_exame value=$cad_exame>";
$btn = "editar";
} else {
echo "<input type=hidden name=acao value=add>";
echo "<input type=hidden name=cad_exame value=$cad_exame>";
$btn = "adicionar";
}
  echo "<table width=100% cellspacing=2 cellpadding=0 border=0>
	   <tr>
	    <td colspan=2>Exame:</td>
	   </tr>
	   <tr>
	    <td colspan=2><select name=proc_codigo class=box style='width:420px'>
	     <option>..:: Selecione o Exame ::..</option>";
  $sql = pg_query("select TRANSLATE(proc.proc_nome, 'ZZZ-', '') as proc_nome,proc.proc_codigo from tipodeexame as tp, procedimento as proc where tp.proc_codigo = proc.proc_codigo order by TRANSLATE(proc.proc_nome, 'ZZZ-', '')");
 while($rr = pg_fetch_array($sql)) {
  echo ($rr[proc_codigo]==$row[proc_codigo])?"<option value='$rr[proc_codigo]' selected>$rr[proc_nome]</option>":"<option value='$rr[proc_codigo]'>$rr[proc_nome]</option>";
 }
    echo "</select></td>
	   </tr>
           <tr>
             <td width=10>Urgente:</td>
             <td><select name=itx_urgente class=box>
		<option value='N' $it_1>NAO</option>
		<option value='S' $it_2>SIM</option>
	     </select></td>
           </tr>
           <tr>
             <td width=10>Status:</td>
             <td><select name=itx_status class=box>
		<option value='P' $ts1>PENDENTE</option>
		<option value='R' $ts2>REALIZADO</option>
		<option value='E' $ts3>ENTREGUE</option>
	     </select></td>
           </tr>
           <tr>
             <td colspan=2>Observacao: </td>
           </tr>
           <tr>
             <td colspan=2><textarea name='itx_observacao' class='box' cols='65' rows='3'>$row[itx_observacao]</textarea></td>
           </tr>
           <tr>
             <td colspan=2 valign=absmiddle><a href=javascript:history.go(-1)><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar.png border=0></a>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$btn."_on.jpg></td>
           </tr>
	  </table></form>";
}

if($acao=="add") {
   $sql = pg_query("insert into itensdoexame (cad_exame,proc_codigo,itx_observacao,itx_urgente,itx_status)
		    VALUES
		  ('$cad_exame','$proc_codigo','$itx_observacao','$itx_urgente','$itx_status')") or die(pg_last_error());

          echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>INCLUSO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_itensdoexame_iframe.php?id_login=$id_login&cad_exame=$cad_exame'\", 2000);
              </SCRIPT>";

}

if($acao=="edit") {
   $sql = pg_query("update itensdoexame SET 
		    proc_codigo='$proc_codigo',
		    itx_observacao='$itx_observacao',
		    itx_urgente='$itx_urgente',
		    itx_status='$itx_status'
		   where itx_codigo = '$itx_codigo'");

          echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>EDITADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_itensdoexame_iframe.php?id_login=$id_login&cad_exame=$cad_exame'\", 2000);
              </SCRIPT>";
}

if($acao=="del") {
   $sql = pg_query("delete from itensdoexame where itx_codigo = '$itx_codigo'");

          echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>EDITADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_itensdoexame_iframe.php?id_login=$id_login&cad_exame=$cad_exame'\", 2000);
              </SCRIPT>";
}
