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
         <td><a href=$PHP_SELF?acao=form_add&cad_exame=$cad_exame&labm_codigo=$labm_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
        </tr>
        </table>";
echo "<table width=100% cellpadding=4 cellspacing=1 border=0 style='border-top:1px solid;border-right:1px solid;border-left:1px solid;border-bottom:1px solid;'>
        <tr>
         <td width=10 style='background-color:#c9c9c9'>Cod.</td>
         <td width=40% style='background-color:#c9c9c9'>Exame</td>
         <td style='background-color:#c9c9c9'>Tipo</td>
         <td width=10 style='background-color:#c9c9c9'>Dt.Coleta</td>
         <td colspan=2 style='background-color:#c9c9c9'>&nbsp;</td>
        </tr>";
$sql = pg_query("SELECT mlz_codigo, itx_codigo, tma_codigo, mlz_coletado, to_char(mlz_datadacoleta,'DD/MM/YYYY') as mlz_datadacoleta, mlz_quantidade, id_login, labm_codigo, mlz_conservacao, mlz_observacao, mlz_motivo FROM materialdeanalise where cad_exame = $cad_exame");
while($row=pg_fetch_array($sql)) {
$tma = pg_fetch_array(pg_query("select *from tipodematerial where tma_codigo = $row[tma_codigo]"));
$itx = pg_fetch_array(pg_query("select *from itensdoexame where itx_codigo = $row[itx_codigo]"));
$proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = $itx[proc_codigo]"));
if(trim($row[mlz_coletado])=="S") {
	$tpcoleta = "<font color=green><b>Coletado</b></font>";
} else {
	$tpcoleta = "<font color=red><b>Nao Coletado</b></font>";
}
echo "<tr>
         <td width=10 align=center>$row[mlz_codigo]</td>
         <td width=50%>$proc[proc_nome]</td>
         <td width=50%>$tpcoleta</td>
         <td>$row[mlz_datadacoleta]</td>
         <td width=10><a href=$PHP_SELF?acao=form_edit&mlz_codigo=$row[mlz_codigo]&cad_exame=$cad_exame&labm_codigo=$labm_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btnedit_on.png border=0></a></td>
         <td width=10><a href=$PHP_SELF?acao=del&mlz_codigo=$row[mlz_codigo]&labm_codigo=$labm_codigo&cad_exame=$cad_exame><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel_on.png border=0></a></td>
        </tr>";
}
echo "</table><br>";
}

if(($acao=="form_add" OR $acao=="form_edit")) {
  $verifica = pg_query("select i.itx_codigo,i.cad_exame,i.proc_codigo,i.itx_observacao,i.itx_urgente,i.itx_status,p.proc_nome from itensdoexame as i left join procedimento as p on p.proc_codigo = i.proc_codigo where i.cad_exame = $cad_exame");
  if(pg_num_rows($verifica)==0) {
         echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:red;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>ERRO<br>Nao e possivel cadastrar pois nao foram selecionados os ITENS DE EXAME</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 4000);
              </SCRIPT>";
exit;
}

echo "<form method=post action=$PHP_SELF>";
 if($acao=="form_edit") {
    $row = pg_fetch_array(pg_query("SELECT med_codigo, mlz_codigo, itx_codigo, tma_codigo, mlz_coletado, to_char(mlz_datadacoleta,'DD/MM/YYYY') as mlz_datadacoleta, mlz_quantidade, id_login, labm_codigo, mlz_conservacao, mlz_observacao, mlz_motivo FROM materialdeanalise WHERE mlz_codigo = $mlz_codigo"));
if($row[mlz_coletado]=="S") {
   $it_1 = "selected";
   $it_2 = "";
} else {
   $it_1 = "";
   $it_2 = "selected";
}
echo "<input type=hidden name=acao value=edit>";
echo "<input type=hidden name=mlz_codigo value=$mlz_codigo>";
echo "<input type=hidden name=cad_exame value=$cad_exame>";
echo "<input type=hidden name=labm_codigo value=$labm_codigo>";
$btn = "editar";
} else {
echo "<input type=hidden name=acao value=add>";
echo "<input type=hidden name=cad_exame value=$cad_exame>";
echo "<input type=hidden name=labm_codigo value=$labm_codigo>";
$btn = "adicionar";
}
  echo "<table width=100% cellspacing=2 cellpadding=0 border=0>
    	<tr>
         <td align=right width=20%>Data da Coleta:</td>
         <td><input type=text name=mlz_datadacoleta size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10 value='$row[mlz_datadacoleta]'></td>
        </tr>
	   <tr>
	    <td align=right width=100>Quantidade:</td>
	    <td><input type=text name=mlz_quantidade class=box size=2 value='$row[mlz_quantidade]'></td>
	   </tr>
           <tr>
             <td width=10 align=right>Coletado:</td>
             <td><select name=mlz_coletado class=box>
                <option value='N' $it_1>NAO</option>
                <option value='S' $it_2>SIM</option>
             </select></td>
           </tr>
           <tr>
            <td colspan=2>Bioquimico:</td>
	   </tr>
	   <tr>
            <td colspan=2><select name=med_codigo class=box style='width:420px'>
             <option value=0>..:: Selecione o Bioquimico ::..</option>";
  $sql = pg_query("select *from medico_especialidade as esp left join medico as m on esp.med_codigo=m.med_codigo where esp_codigo=9");
 while($rr = pg_fetch_array($sql)) {
  echo ($rr[med_codigo]==$row[med_codigo])?"<option value='$rr[med_codigo]' selected>$rr[med_nome]</option>":"<option value='$rr[med_codigo]'>$rr[med_nome]</option>";
 }
    echo "</select></td>
           </tr>
           <tr>
            <td colspan=2>Item do Pedido:</td>
	   </tr>
	   <tr>
            <td colspan=2><select name=itx_codigo class=box style='width:420px'>
             <option value=0>..:: Selecione o Itens do Pedido ::..</option>";
  $sql = pg_query("select i.itx_codigo,i.cad_exame,i.proc_codigo,i.itx_observacao,i.itx_urgente,i.itx_status,p.proc_nome from itensdoexame as i left join procedimento as p on p.proc_codigo = i.proc_codigo where i.cad_exame = $cad_exame");
 while($rr = pg_fetch_array($sql)) {
  echo ($rr[itx_codigo]==$row[itx_codigo])?"<option value='$rr[itx_codigo]' selected>$rr[proc_nome]</option>":"<option value='$rr[itx_codigo]'>$rr[proc_nome]</option>";
 }
    echo "</select></td>
           </tr>";
/*
    echo "<tr>
            <td colspan=2>Tipo de Material:</td>
	   </tr>
	   <tr>
            <td colspan=2><select name=tma_codigo class=box style='width:420px'>
             <option>..:: Selecione o Tipo de Material ::..</option>";
  $sql = pg_query("select *from tipodematerial order by tma_tipo");
 while($rr = pg_fetch_array($sql)) {
  echo ($rr[tma_codigo]==$row[tma_codigo])?"<option value='$rr[tma_codigo]' selected>$rr[tma_tipo]</option>":"<option value='$rr[tma_codigo]'>$rr[tma_tipo]</option>";
 }
    echo "</select></td>
           </tr>";
*/
    echo " <tr>
             <td colspan=2>Conservacao: </td>
           </tr>
           <tr>
             <td colspan=2><textarea name='mlz_conservacao' class='box' cols='65' rows='3'>$row[mlz_conservacao]</textarea></td>
           </tr>
           <tr>
             <td colspan=2>Observacoes: </td>
           </tr>
           <tr>
             <td colspan=2><textarea name='mlz_observacao' class='box' cols='65' rows='3'>$row[mlz_observacao]</textarea></td>
           </tr>
           <tr>
             <td colspan=2>Motivo da nao coleta do material: </td>
           </tr>
           <tr>
             <td colspan=2><textarea name='mlz_motivo' class='box' cols='65' rows='3'>$row[mlz_motivo]</textarea></td>
           </tr>
           <tr>
             <td colspan=2 valign=absmiddle><a href=javascript:history.go(-1)><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar.png border=0></a>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$btn."_on.jpg></td>
           </tr>
          </table></form>";
}

if($acao=="add") {
   $ver = pg_query("select *from materialdeanalise where itx_codigo = $itx_codigo");
  if(pg_num_rows($ver)>="1") { 
         echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>Exame Duplicado</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
exit;
}
if($itx_codigo=="0") {
         echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=red><b>Selecione o Item</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
exit;
}

// SQL INSERT
 $stmt = "INSERT INTO materialdeanalise ( 
	med_codigo,
	cad_exame,
	mlz_datadacoleta,
	itx_codigo, 
	tma_codigo, 
	mlz_coletado, 
	mlz_quantidade, 
	id_login, 
	labm_codigo, 
	mlz_conservacao, 
	mlz_observacao, 
	mlz_motivo
	 ) VALUES ( 
	".intval($med_codigo).", 
	".intval($cad_exame).", 
	'".trim(strtoupper($mlz_datadacoleta))."', 
	".intval($itx_codigo).", 
	".intval($tma_codigo).", 
	'$mlz_coletado', 
	".intval($mlz_quantidade).", 
	".intval($id_login).", 
	".intval($labm_codigo).", 
	'".trim(strtoupper($mlz_conservacao))."', 
	'".trim(strtoupper($mlz_observacao))."', 
	'".trim(strtoupper($mlz_motivo))."' )";
 $sql = pg_query($stmt);
         echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>INCLUSO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
}

if($acao=="edit") {
 $stmt = "UPDATE materialdeanalise SET 
	med_codigo = ".intval($med_codigo).", 
	itx_codigo = ".intval($itx_codigo).", 
	tma_codigo = ".intval($tma_codigo).", 
	mlz_coletado = '$mlz_coletado', 
	mlz_datadacoleta = '".trim(strtoupper($mlz_datadacoleta))."', 
	mlz_quantidade = ".intval($mlz_quantidade).", 
	id_login = ".intval($id_login).", 
	labm_codigo = ".intval($labm_codigo).", 
	mlz_conservacao = '".trim(strtoupper($mlz_conservacao))."', 
	mlz_observacao = '".trim(strtoupper($mlz_observacao))."', 
	mlz_motivo = '".trim(strtoupper($mlz_motivo))."'
	WHERE mlz_codigo = ".intval($mlz_codigo) ;

$sql = pg_query($stmt);
          echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>EDITADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 2000);
              </SCRIPT>";
}

if($acao=="del") {
$stmt = "DELETE FROM materialdeanalise WHERE mlz_codigo = ".intval($mlz_codigo);
$sql = pg_query($stmt);

          echo "<br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>APAGADO com Sucesso</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_materialdeanalise_iframe.php?id_login=$id_login&cad_exame=$cad_exame&labm_codigo'\", 2000);
              </SCRIPT>";
}

