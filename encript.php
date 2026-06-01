<?php

include_once 'db.inc.php';
echo"
<form method=post action=$_PHP_SEFT>
	<input type=hidden name='acao' value='add'>
	Codigo:<input type=text name='codigo'><br>
	Nome:<input type=text name='nome'>
	<input type=submit>
</form>";

if($_POST['acao'] == "add"){
	
	echo"<pre>".print_r($_POST,true)."</pre>";
	
	$senha = $_POST[codigo].md5($_POST[nome]);
	$insert ="insert into encript (enc_codigo,enc_nome)VALUES('$_POST[codigo]','$senha')";
	pg_query($insert);
	echo $insert;
}


?>