<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";


echo "<form method=post action=$PHP_SELF>
  <input type=hidden name=acao value=ok>
  numero do usuario: <input type=text name=num>&nbsp; <input type=submit value='Permite'>
 </form>";

if($acao=="ok") {
   $sql = pg_query("update usuarios_permissoes set perm_set='S',nivel_i='S',nivel_a='S',nivel_d='S',nivel_l='S',nivel_b='S' where usr_codigo='$num'");
if($sql) { echo "<br>Alterado com Sucesso"; } else { echo "Erro Usuario nao existe"; }
}
?>
