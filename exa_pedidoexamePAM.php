<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario( $hotkey = true);

reglog($id_login,"Acessando Fazer EXAME");

?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="g_script.js"></script>
<form method=post action="<?=$PHP_SELF?>">
<input type=hidden name=id_login value="<?=$id_login?>">
<?

if(($acao=="form_add" OR $acao=="form_edit")) {
if($acao=="form_add") {
echo "<input type=hidden name=acao value=add>";
} else {
echo "<input type=hidden name=acao value=edit>";
echo "<input type=hidden name=cad_exame value=$cad_exame>";
}
   $sel = pg_fetch_array(pg_query("SELECT cad_exame, to_char(cad_datapedido,'DD/MM/YYYY') as cad_datapedido, id_login, labm_codigo, uni_codigo, usu_codigo, to_char(cad_previsaoentrega,'DD/MM/YYYY') as cad_previsaoentrega, med_codigo, cad_retiradapedidoexame, cad_dadosclinicos FROM cadastrodoexame where cad_exame = '$cad_exame'"));
   $pac = pg_fetch_array(pg_query("select *from usuario where usu_codigo = '$sel[usu_codigo]'"));
                        echo "<fieldset>";
                                echo "<legend>Dados do Paciente</legend>";
                                echo "<table width=100% cellspacing=0 cellpadding=1 border=0>";
                                        echo "<tr>";
                                                echo "<td width=110>Numero do Paciente</td>";
                                                echo "<td width=40>";
                                                        echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxl size=10 readonly value='$pac[usu_codigo]'>";
                                                        echo "</td>";


                                                echo "<td width=40>Paciente</td>";
                                                echo "<td>";
                                                        echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxl size=60 onkeyasdfup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
                                                echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";



                                                echo divBuscaPaciente();
                                                        //echo "<input type=text name=pac_nome id=pac_nome class=boxl size=60 readonly><a href='#' OnClick='window.open(\"list_pacientes.php?id_login=$id_login&from=list\",null,\"height=460,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0 id='localizar'></a>";
                                                        /*echo "<input type=text name=pac_nome id=pac_nome class=boxl size=60 readonly><a href='#' OnClick='window.open(\"paciente.php?id_login=$id_login&controle=1\",null,\"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a>";*/
                                                        /*echo "<a href='#' OnClick='window.open(\"paciente.php?acao=form_add&id_login=$id_login&controle=1\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle border=0></a>";*/
                                                        echo "<a href='#' OnClick='window.open(\"paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle id=ficha border=0></a>";
                                                echo "</td>";
                                                echo "<td>Nascimento</td>";
                                                echo "<td width=230>";
                                                        //echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxl size=15 readonly>";
                                                        echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxl size=15 onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');return Ajusta_Data(this, event);\" maxlength=\"10\">";
                                                        echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data')\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
                                                echo "</td>";
                                        echo "</tr>";
                                //echo "</table>";
                                //echo "<table width=100% cellspacing=0 cellpadding=4 border=0>";
                                        echo "<tr>";
                                                echo "<td width=70 >Mãe</td>";
                                                echo "<td width=100 colspan=3>";
                                                        echo "<input type=text name=pac_mae value='$pac[usu_mae]' id=pac_mae class=boxl size=50 readonly>";
                                                echo "</td>";
                                                echo "<td width=20>Cidade</td>";
                                                echo "<td width=80>";
                                                        echo "<input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxl size=23 readonly>";
                                                echo "</td>";
                                        echo "</tr>";
                                echo "</table>";
                        echo "</fieldset>";

if($cad_retiradapedidoexame=="EN") {
   $vl1="selected";
   $vl2="";
} else {
   $vl1="";
   $vl2="selected";
}
?>


<fieldset>

        <legend>Pedido do Exame<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
        <table cellpadding='1'>
        <tr>
          <td align=right>Tipo:</td>
          <td><select name=cad_retiradapedidoexame class=box>
	  <option value='EN' $vl1>ENTRADA DO PEDIDO</option>
	  <option value='RE' $vl2>RETIRADA DO PEDIDO</option>
	  </select></td>
        </tr>
<?
 if($sel[cad_datapedido]=="") { $dt = date('d/m/Y'); } else { $dt = $sel[cad_datapedido]; }
 if($sel[cad_previsaoentrega]=="") { $dtp = date('d/m/Y'); } else { $dtp = $sel[cad_datapedido]; }
?>
        <tr>
	 <td align=right width=15%>Data do Pedido:</td>
	 <td><input type=text name=cad_datapedido value='<?=$dt?>' size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10></td>
	</tr>
        <tr>
	 <td align=right>Data Previsao Entrega:</td>
	 <td><input type=text name=cad_previsaoentrega value='<?=$dtp?>' size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10></td>
	</tr>
        <tr>
	 <td align=right>Laboratorio:</td>
	 <td><select name=labm_codigo class=box>"<?=$sel[labm_codigo]?>"
	 <option>..:: Seleciona o Laboratorio ::..</option>
	 <?
	$sql = pg_query("select *from medico where prestador_servico='S' and med_codigo = '2165'");
	 while($lab=pg_fetch_array($sql)) {
	  echo ($sel[labm_codigo]==$lab[labm_codigo])?"<option value=$lab[med_codigo] selected>$lab[med_nome]</option>":"<option value=$lab[med_codigo]>$lab[med_nome]</option>";
	 }
	 ?>
	 </select></td>
	</tr>
        <tr>
	 <td align=right>Unidade de Coleta:</td>
	 <td><select name=uni_codigo class=box>
	 <option>..:: Seleciona a Unidade de Coleta ::..</option>
	 <?
	$query = pg_query("select *from unidade order by uni_desc");
	 while($uni=pg_fetch_array($query)) {
	  echo ($sel[uni_codigo]==$uni[uni_codigo])?"<option value=$uni[uni_codigo] selected>$uni[uni_desc]</option>":"<option value=$uni[uni_codigo]>$uni[uni_desc]</option>";
	 }
	 ?>
	 </select></td>
	</tr>
        <tr>
	 <td align=right>Medico:</td>
	 <td><select name=med_codigo class=box>
	 <option>..:: Selecione o Medico ::..</option>
	 <?
	$select = pg_query("select *from medico where prestador_servico is null or prestador_servico = 'S' order by med_nome");
	 while($med=pg_fetch_array($select)) {
	  echo ($med[med_codigo]==$sel[med_codigo])?"<option value='$med[med_codigo]' selected>$med[med_nome]</option>":"<option value='$med[med_codigo]'>$med[med_nome]</option>";
	 }
	 ?>
	 </select></td>
	</tr>
        <tr>
          <td>&nbsp;</td>
          <td>Dados Clinicos: </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><textarea name='cad_dadosclinicos' class='box' cols='66' rows='3'><?=$sel[cad_dadosclinicos]?></textarea></td>
        </tr>
<?
if(($cad_exame=="" OR $acao=="form_edit")) {
echo "<tr>
       <td>&nbsp;</td>
       <td><a href=exa_listapedidoexamePAM.php><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg class=boxx></td>
      </tr>";
} else {
echo "<tr>
       <td>&nbsp;</td>
       <td><a href=exa_listapedidoexamePAM.php><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar.png border=0></a>&nbsp;<a href=$PHP_SELF?acao=finaliza><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_pedido_on.png border=0></a></td>
      </tr>";
}
?>
	</form>
	</table>
</fieldset>

<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td>
   <fieldset>
        <legend>Itens do Exame<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
        <table cellpadding='0'>
        <tr>
	 <td><iframe name=frameprincipal src='exa_itensdoexame_iframe.php?id_login=<?=$id_login?>&cad_exame=<?=$cad_exame?>' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=169></iframe></td>
	</tr>
	</table>
    </fieldset>
 </td>
 <td>
   <fieldset>
        <legend>Material de Analise<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
        <table cellpadding='0'>
        <tr>
	 <td><iframe name=frameprincipal src='exa_materialdeanalise_iframe.php?id_login=<?=$id_login?>&cad_exame=<?=$cad_exame?>&labm_codigo=<?=$labm_codigo?>' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=169></iframe></td>
	</tr>
	</table>
    </fieldset>
 </td>
</tr>
</table>
<?
}
if($acao=="add") {
// SQL INSERT

if(empty($pac_codigo)) {
         echo "<br><br><br><br><br><br><Br><Br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>Paciente nao selecionado</b></font></td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_pedidoexamePAM.php?acao=form_add&id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 0);
              </SCRIPT>";
exit;
}


 $id = pg_fetch_array( pg_query("select max(cad_exame) as cad_exame from cadastrodoexame"));
 $cad_exame = $id[cad_exame]+1;
 $stmt = "INSERT INTO cadastrodoexame ( 
	cad_exame, 
	cad_datapedido, 
	id_login, 
	labm_codigo, 
	uni_codigo, 
	usu_codigo, 
	cad_previsaoentrega, 
	med_codigo, 
	cad_retiradapedidoexame, 
	cad_dadosclinicos
	 ) VALUES ( 
	".intval($cad_exame).", 
	'".trim(strtoupper($cad_datapedido))."', 
	".intval($id_login).", 
	".intval($labm_codigo).", 
	".intval($uni_codigo).", 
	".intval($pac_codigo).", 
	'".trim(strtoupper($cad_previsaoentrega))."', 
	".intval($med_codigo).", 
	'$cad_retiradapedidoexame', 
	'".trim(strtoupper($cad_dadosclinicos))."' )";

$sql = pg_query($stmt);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_pedidoexamePAM.php?acao=form_add&id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 0);
              </SCRIPT>";
}

if($acao=="edit") {
 $stmt = "UPDATE cadastrodoexame SET 
	cad_datapedido = '".trim(strtoupper($cad_datapedido))."', 
	id_login = ".intval($id_login).", 
	labm_codigo = ".intval($labm_codigo).", 
	uni_codigo = ".intval($uni_codigo).", 
	cad_previsaoentrega = '".trim(strtoupper($cad_previsaoentrega))."', 
	med_codigo = ".intval($med_codigo).", 
	cad_retiradapedidoexame = '$cad_retiradapedidoexame', 
	cad_dadosclinicos = '".trim(strtoupper($cad_dadosclinicos))."'
	WHERE cad_exame = ".intval($cad_exame) ;
$sql = pg_query($stmt);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_pedidoexamePAM.php?acao=form_edit&id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 0);
              </SCRIPT>";

}


if($acao=="finaliza") {
         echo "<br><br><br><br><br><br><Br><Br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>PEDIDO com FINALIZADO</b></font></td>
                 </tr>
                </table><br>";

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_listapedidoexamePAM.php?id_login=$id_login'\", 2000);
              </SCRIPT>";
}

?>
