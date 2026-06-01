<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
if (isset($op) && $op == "del") {
	session_start();
   
	$cont = 0;
	while (strlen($chb[$cont]) > 0){
		pg_query("DELETE FROM logon WHERE id = $chb[$cont]");
		//unset();
		session_destroy(md5("id"));
		$cont++;
	}
	header("Location: auth.php"); //como solicitado na OS 394
}
?>
<!DOCTYPE head PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GPS - Software de Gest&atilde;o P&uacute;blica</title>
<link href="estilo.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgsBotoes/mini_logo_elotech.png"> 
<script type="text/javascript">
function validaBotao( max )
{
	if( max == 0 ) return;

	var achou = false;
    
	for( var i = 1; i <= max && ! achou ; i ++ )
	{
		var o = document.getElementById( "chb_"+i );
		achou = o.checked;
	}
    
	if( ! achou )
		alert( "Escolha ao menos 1 !");

	return achou;
}

function desconectarTodos( max )
{
	if( max == 0 ) return;

	for( var i = 1; i <= max ; i ++ )
	{
		var o = document.getElementById( "chb_"+i );
		achou = o.checked = true;
	}
    
	return true;
}

</script>
</head>
<body bgcolor=#d0e0f0 style="margin-top:100px;">
<?
	$sql = "SELECT logon.id AS id, 
				   usuarios.usr_login AS login
			  FROM logon
			 INNER JOIN usuarios
				ON logon.id_login = usuarios.usr_codigo
			 ORDER BY usuarios.usr_login";
	$query = pg_query($sql);
	$resultado = pg_num_rows($query);
	if ($resultado != 0) {
		$j = 1;
		echo "
		<div align=center>
			<fieldset style=\"width:300px;right:50px;padding:5px;\">
				<legend align='center'>Escolha um usu&aacute;rio para ser derrubado</legend>
				<form method='post' action='$PHP_SELF' onsubmit='return validaBotao($resultado);'>
					<table align=center>";
					while($row = pg_fetch_array($query)) {
						echo "
						<tr>
							<td width='2%'>".$j."</td>
							<td><input type='checkbox' id='chb_$j' name='chb[]' value='$row[id]'> <label for='chb_$j'>$row[login]</label></td>
							<input type='hidden' value='del' name='op'>
						</tr>";
						$j++;
					}
					echo "
					<td colspan=2>
						<input type='submit' name='sub1' value='Desconectar'>
						<input type='submit' name='sub2' value='Desconectar TODOS' onclick='desconectarTodos($resultado)'>
					</td>";
				echo "
				</form>";
			echo "
			</fieldset>
		</div>";
	} else {
		echo "<p align='center'><strong><h2>Nenhum usu&aacute;rio logado!</h2></strong</p>";
		echo "<script type='text/javascript'>
		setTimeout(\"document.location.href='auth.php'\",3000);
		</script>";
	}
?>
</body>
</html>