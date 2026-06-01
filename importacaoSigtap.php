<link href="estilo.css" rel="stylesheet" type="text/css" />
<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";
?>


<script language="JavaScript">
<!--
function teste(){
if (document.upload.arquivo.value=="") {
alert("Arquivo para upload não informado!")
document.upload.arquivo.focus()
return false
}
}
//-->
</script>
<?
echo "

<form name='upload' action='upload.php' method='post' enctype='multipart/form-data' onsubmit='return teste()'>
	<fieldset>
		<fieldset>
			<legend>Arquivo</legend>
			<input type='file' name='arquivo' class='boxTexto' size='60'>
		</fieldset>
		<fieldset>
			<legend>Reimporta&ccedil;&otilde;es</legend>
			<input type='checkbox' name='sub' id='sub' checked='checked'> <b>Sobreescreva os dados caso eu ja tenha importado esta compet&ecirc;ncia </b>
			<br>
		</fieldset>
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg>

	</fieldset>
</form>
";
?>

