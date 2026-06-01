<script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//           verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=ate_codigo value=$ate_codigo>
	<input type=hidden name=acao value=add>
	<table width=90% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	 <td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>It.Age.</font></td>
	</tr>";
echo "<tr bgcolor=FFFFFF>";
 echo "<td width=10 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>";
$sel_proc = pg_query("select *from procedimento where proc_exame = 'S' order by proc_nome");
echo "<td width=300 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:cccccc'><select name='proc_codigo' class='box' style='width:680px !important;'>";
echo "<option value=''>----- Selecione um Procedimento -----</option>";
while($ra=pg_fetch_array($sel_proc)) {
  echo "<option value='$ra[proc_codigo]'>$ra[proc_nome]</option>";

}
 echo "</select></td>";
echo "</form></tr>";
   echo "</table><br><br>";

$sql_requisicao = pg_query("select *from requisicao_exames where ate_codigo = '$ate_codigo' and req_finalizada = 'N'");
  echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=CCCCCC>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Exame Requisitado</font></td>
	 <td>&nbsp;</td>
	</tr>";
while($rv=pg_fetch_array($sql_requisicao)) {
 $proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = '$rv[proc_codigo]'"));
 echo "<tr bgcolor=ffffff>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>$proc[proc_nome]</font></td>
	 <td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=10><a href=$PHP_SELF?acao=del&req_codigo=$rv[req_codigo]&ate_codigo=$ate_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_on.jpg border=0></a></td>
	</tr>";

 }
 echo "</table>";


if($acao=="add") {
   $usu = pg_fetch_array(pg_query("select *from atendimento where ate_codigo='$ate_codigo'"));
   $sql = pg_query("insert into requisicao_exames (usu_codigo,ate_codigo,proc_codigo,req_finalizada,dt_requisicao) values ('$usu[usu_codigo]','$ate_codigo','$proc_codigo','N',NOW())");
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
             setTimeout(\"location='$PHP_SELF?id_login=$id_login&id_login=$id_login&ate_codigo=$ate_codigo&acao='\", 0);
           </SCRIPT>";

 }

if($acao=="del") {
   $sql = pg_query("delete from requisicao_exames where req_codigo='$req_codigo'");
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
             setTimeout(\"location='$PHP_SELF?id_login=$id_login&id_login=$id_login&ate_codigo=$ate_codigo&acao='\", 0);
           </SCRIPT>";

 }

