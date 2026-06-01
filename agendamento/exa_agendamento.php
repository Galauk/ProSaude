<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

?>

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
        </style>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?
 echo "<table width=100% cellspacing=0 cellpadding=5 border=0>
	<tr>
	 <td class=borda2>Procedimento</td>
	 <td class=borda2>Data</td>
	 <td class=borda2>Procedimento</td>
	</tr>
	</table>";

if(empty($acao)) {
$uri = $_SERVER['REQUEST_URI'];
$exp = explode("?",$uri);
  echo "<a href=$PHP_SELF?acao=sel_exa&$exp[1]>Selecionar Procedimento</a>";

}

if($acao=="sel_exa") {
$uri = $_SERVER['REQUEST_URI'];
$exp = explode("?",$uri);
$rep = str_replace("acao=sel_exa","",$exp[1]);
  echo "<table width=100% cellspacing=0 cellpadding=3 border=0 class=borda>
         <tr>
          <td><b>Selecione o Procedimento:</b></td>
         </tr><form method=post action=$PHP_SELF?$rep>";
  echo "<tr>
          <td><select name=proc_codigo class='box' style='width:550px !important;'>";
 $sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,*from procedimento where proc_exame = 'S' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($proc = pg_fetch_array($sql)) {
  echo "<option value=$proc[proc_codigo]>$proc[newprocnome]</option>";
}
  echo "</select></td>
        </tr>
         <tr>
          <td><b><font color=blue>Datas Disponiveis:</font></b></td>
         </tr>";
  echo "<tr>
          <td><select name=proc_codigo class='box'>";
  echo "<option value=>11/11/2011</option>";
  echo "</select></td>
        </tr>
        <tr>
         <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg style='border: 0;'></td>
        </tr></form>
       </table>";
}
?>