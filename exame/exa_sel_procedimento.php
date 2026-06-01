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
<?


  echo "<table width=100% cellspacing=0 cellpadding=3 border=0 class=borda>
	 <tr>
	  <td><b>Selecione o Procedimento:</b></td>
	 </tr>";
  echo "<tr>
	  <td><select name=proc_codigo class='box' style='width:600px !important;'>";
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
	 <td><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.jpg></td>
	</tr>
       </table>";


?>
