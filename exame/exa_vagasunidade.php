<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

?>
</script>

        <style>
                .borda {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #cccccc;
                }
                .borda2 {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                }
                .bordaN {
                        border-bottom: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                        background: #f9f9f9;
                        text-align: right;
                }
        </style>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?
if(empty($acao)) {

echo "<h3>$acao</h3>";

//
//-> Botoes
 echo "<fieldset><legend>Laboratorio CENTRAL/ Vagas por Unidade </legend>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>
        <tr bgcolor='#000000'>
         <td><font color='#FFFFFF'>Laboratorios</font></td>
         <td><font color='#FFFFFF'>Periodo</font></td>
         <td><font color='#FFFFFF'>Qtd. Proc.</font></td>
         <td><font color='#FFFFFF'>Qtd. Vagas</font></td>
         <td width=420 colspan=3>&nbsp;</td>
        </tr>";
$sql = pg_query("select to_char(grm.gex_periodo,'DD/MM/YYYY') as gex_periodo2,* from grade_exame_mensal as grm left join medico as m on m.med_codigo = grm.med_codigo
 where grm.med_codigo = '2165' order by to_date(gex_periodo,'YYYY') desc,to_date(gex_periodo,'MM') desc,to_date(gex_periodo,'DD') desc") or die(pg_last_error());
  while($rr = pg_fetch_array($sql)) {
   $query = pg_query("select *from grade_exame where med_codigo = $rr[med_codigo] and graex_data = '$rr[gex_periodo]'");
   $num = pg_num_rows($query);
   $row = pg_fetch_array($query);
   $total = pg_fetch_array(pg_query("select sum(graex_qtde) as total from grade_exame where med_codigo = $rr[med_codigo] and graex_data = '$rr[gex_periodo]'"));
if($num!=0) {
 echo "<tr bgcolor='#f1f1f1'>
         <td>$rr[med_nome]</td>
         <td align=center>$rr[gex_periodo2]</td>
         <td align=center><font size=2><b>$num</b></td>
         <td align=center><font size=2><b>$total[total]</b></td>
         <td width=120><a href=$PHP_SELF?med_codigo=$rr[med_codigo]&acao=form_add&id_login=$id_login&gex_codigo=$rr[gex_codigo]&gex_periodo=$rr[gex_periodo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
        </tr>";
}
}
 echo "</table>";
 echo "</fieldset>";

}

if($acao=="newstatus") {
   $sql = pg_query("update grade_exame set graex_status='$status' where graex_codigo = '$graex_codigo'");
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$med_codigo&gex_periodo=$gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="form_add") {
 echo "<fieldset><legend>Digite a Cota Diaria para Cada Procedimentos</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
        <input type=hidden name=acao value=finalizar_upd>
        <input type=hidden name=med_codigo value=$med_codigo>
        <input type=hidden name=gex_periodo value=$gex_periodo>
        <input type=hidden name=gex_codigo value=$gex_codigo>
        <table width=100% cellspacing=1 cellpadding=5 border=0>
        <tr bgcolor='#000000'>
         <td><font color='#FFFFFF'>Status</font></td>
         <td><font color='#FFFFFF'>Procedimento</font></td>
         <td><font color='#FFFFFF'>Preco do Procedimento</font></td>
        </tr>";
$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,grm.graex_qtde as graex_qtde,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.gex_codigo = '$gex_codigo' and grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-','')");
  while($row = pg_fetch_array($sql)) {
  if($row[graex_status]=="S") {
     $img = "<a href=$PHP_SELF?status=N&graex_codigo=$row[graex_codigo]&acao=newstatus&med_codigo=$med_codigo&gex_periodo=$gex_periodo&gex_codigo=$gex_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/on.png border=0></a>";
} else {
     $img = "<a href=$PHP_SELF?status=S&graex_codigo=$row[graex_codigo]&acao=newstatus&med_codigo=$med_codigo&gex_periodo=$gex_periodo&gex_codigo=$gex_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/off.png border=0></a>";
}
  echo "
        <input type=hidden name=graex_codigo[] value=$row[graex_codigo]>
        <tr bgcolor='#f1f1f1'>
         <td width=22>$img</td>
         <td>$row[newprocnome]</td>
         <td><input type=text name=graex_qtde[] value='$row[graex_qtde]' class=box size=4 maxsize=4></td>
        </tr>";
}
 echo "</table>";
 echo "</fieldset>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr>
        <td align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png></td>
        </tr>";
 echo "</table>";
}


