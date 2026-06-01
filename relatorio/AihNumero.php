<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
cabecario();


echo " 

<link href='../estilo.css' rel='stylesheet' type='text/css'></style>
<script type='text/javascript' src='../funcoes.js'></script>
<script type='text/javascript' src='../ajax_motor.js'></script>

<script type='text/javascript'>
	
function valida()
{
	//alert('kauabanga!!');
   if((document.getElementById('numero_livre').checked == '') && (document.getElementById('numero_usado').checked == ''))
    {
		alert('Selecione pelo menos uma das duas opcoes');
		document.getElementById('numero_livre').focus();
			return false;
	}
	return true;
}


function CheckCall() {

     if(document.getElementById('numero_livre').checked != '' && document.getElementById('numero_usado').checked != ''){

	livre = document.getElementById('numero_livre').value;
	usado = document.getElementById('numero_usado').value;
	controle = 1;

}else if(document.getElementById('numero_livre').checked != '' && document.getElementById('numero_usado').checked == '')
{
	livre = document.getElementById('numero_livre').value;
	usado = 'aaa';
	controle = 2;

}else if(document.getElementById('numero_livre').checked == '' && document.getElementById('numero_usado').checked != '')
{
	livre = 'bbb'; 
	usado = document.getElementById('numero_usado').value;
	controle = 3;
}
   

   window.open('Rel_AihNumero.php?numeros_livres='+livre+'&numeros_usados='+usado+'&controle='+controle, null,'height=500,width=900,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');

}

</script>

<form name='form_aih_numero' id='form_aih_numero' action='#' onSubmit='return valida() && CheckCall()'>
	
	"; print "Relat&oacute;rio para vincula&ccedil;&atilde;o do n&uacute;mero da AIH."; echo"
	
<fieldset>
<legend>N&uacute;meros AIH</legend>
<table>
	<tr>
		<td width='100'><label for='numero_livre'>N&uacute;meros Livres:</label></td>
                <td align='left'>
	<input type='checkbox' id='numero_livre' name='numero_livre' class='box' size='17' maxlength='17' value='livre' />
		</td>
	</tr>
	<tr>
		<td><label for='numero_usado'>N&uacute;meros Usados:</label></td>
                <td align='left'>
	<input type='checkbox' id='numero_usado' name='numero_usado' class='box' size='17' maxlength='17' value='usado' />
		</td>
	</tr>
	<tr>
		<td><input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg' /></td>
                <td align=\"right\"><a href=\"../rel_index.php?opcao=5#tabs-5\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>
	</tr>
</table>
</fieldset>
</form>

</body>
</html>";
?>
