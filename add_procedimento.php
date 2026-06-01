<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once "authlib.inc.php";
	verauth($id_login);

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
// -> Inclusao funcao lucio
//------------------------------------------------------------------>

reglog($id_login,"Adicionando Procedimento");
if($act=="addproc") {
   $ins = pg_query("insert into procedimento_atendimento (ate_codigo,proc_codigo) values ('$ate_codigo','$procedimento')");
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='add_procedimento.php?id_login=$id_login&ate_codigo=$ate_codigo'\", 0);
              </SCRIPT>";
}
if($act=="") {
$sel = pg_query("select *from procedimento_atendimento where ate_codigo='$ate_codigo'");
while($row=pg_fetch_array($sel)) {
$proc = pg_fetch_array(pg_query("select *from procedimento where proc_codigo = '$row[proc_codigo]' and proc_exame!='S'"));
if($proc[proc_nome]=="") { exit; }
  echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	 <tr bgcolor=ffffff>
	  <td width=20><a href='$PHP_SELF?acao=del&id_login=$id_login&pat_codigo=$row[pat_codigo]&ate_codigo=$ate_codigo' onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/delpront_on.jpg border=0></a></td>
	  <td>$proc[proc_nome]</td>
	 </tr>
	</table>";
 }
}
if($acao=="del") {
   $ins = pg_query("delete from procedimento_atendimento where pat_codigo='$pat_codigo'");
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='add_procedimento.php?id_login=$id_login&ate_codigo=$ate_codigo'\", 0);
              </SCRIPT>";
}
?>
