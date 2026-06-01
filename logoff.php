<?php 
session_start();
require_once $_SESSION[root].$_SESSION[comum].'library/php/db.inc.php';
require_once $_SESSION[root].$_SESSION[comum].'library/php/funcoes.inc.php';
	
$sql=pg_query("update logon set dt_atualizacao='1900-01-01 00:00:00' where id_login='$id_login'");
reglog($id_login,"Saindo do Sistema");

//destruindo sessao
session_start();
session_unset();
session_destroy(md5("id"));
setcookie ("PHPSESSID", "", time() - 1);
//fim

/*echo "<SCRIPT LANGUAGE=\"JavaScript\">
	setTimeout(\"location='index.php'\", 1000);
	window.close();
      </SCRIPT>";*/

echo "<SCRIPT LANGUAGE=\"JavaScript\">
window.location.href = 'index.php';
</SCRIPT>";
	  
?>
