<script type="text/javascript">
function confirma_senha(valor)
{
	var usr_confirm = document.getElementById('usr_confirm');
	var usr_senha = document.getElementById('usr_senha');
	if (valor == 'add')
	{
		if ((usr_confirm.value == "" || usr_senha.value == "") || usr_confirm.value != usr_senha.value)
		{
			alert('Preencha os campos senha a confirma��o corretamente.');
			return false;
		}
		else
			return true;
	}
	else if (valor == 'edit')
	{
		if ((usr_confirm.value != "" || usr_senha.value != "") && usr_confirm.value != usr_senha.value)
		{
			alert('Preencha os campos senha a confirma��o corretamente.');
			return false;
		}
		else
			return true;
	}
	else
		return false;
}
</script>

<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[modulo]."relatorio/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em UNIDADE");
//------------------------------------------------------------------>
//var_dump($acao);
 if(empty($acao)) {

//
//-> Botoes
  echo "<fieldset>
    <legend>Op��es</legend>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			<tr>
	                    <td> <a href=".$_SESSION[linkroot].$_SESSION[modulo]."zf/usuarios/usuarios><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a> </td>
			</tr>
		</table>

   </fieldset>
  <br>";

//
reglog($id_login,"Formulario de Alteracao de Senha ");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  if(($type=="" OR $acao=="simples")) {
  echo "<form method='post' id='form_usr' 
        onsubmit=\"return confirma_senha('edit');\">
  	  <input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	  <input type=hidden name=type value=simples>
	  <table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	   <tr>
	    <td>
	     <fieldset>
	      <legend>Alteracao de Senha do Usuario </legend>
	       <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	        <tr>
		       <td width=150>Nova Senha:</td>
			<td><input type=password name=usr_senha id=usr_senha class=box size='22'></td>
	        </tr>
	        <tr>
		       <td width=150>Redigite a Nova Senha:</td>
		       <td><input type=password name=usr_confirm id=usr_confirm class=box size='22'></td>
	        </tr>
	        <tr>
	         <td>&nbsp;</td>
	         <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	        </tr>
	       </table>
	     </fieldset>
	    </td>
	   </tr>
          </table><br></form>";
  } 
 }


if($acao=="edit") {
	//var_dump("here");
	 reglog($id_login,"Alterando a Senha do Usuario $id_login");

  $sql = pg_query("update usuarios set " .
            "usr_senha = md5('$usr_senha')"  . "  " .  
            "where usr_codigo='$id_login'");
//var_dump($sql);
msgLocal($id_login,$acao,$sql);
}

function msgLocal($id_login,$acao,$sql) {
	//var_dump($acao);
  $GetNameFile=str_replace("","",$_SERVER["SCRIPT_NAME"]);
  //var_dump($GetNameFile);
	   switch ($acao) {
				case "add":
			 $resp_ok = "<font size=2 color=green><b>INCLUSO com Sucesso</b></font>";
			 $resp_erno = "<font size=2 color=red><b>ERRO ao INCLUIR</b></font>";
			break;
				case "edit":
		  $resp_ok = "<font size=2 color=green><b>EDITADO com Sucesso</b></font>";
		  $resp_erno = "<font size=2 color=red><b>ERRO ao EDITAR</b></font>";
			break;
				case "del":
			 $resp_ok = "<font size=2 color=green><b>APAGADO com Sucesso</b></font>";
		  $resp_erno = "<font size=2 color=red><b>ERRO ao APAGAR</b></font>";
			break;
  }
  
	   if($sql) {
		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
			  <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
			   <tr bgcolor=f9f9f9>
				 <td align=center>$resp_ok</td>
			   </tr>
			  </table><br>";
	  echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
			</SCRIPT>";
	   } else {
		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
			  <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
			   <tr bgcolor=f9f9f9>
				 <td align=center>$resp_erno</td>
			   </tr>
			  </table><br>";
	  echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
			</SCRIPT>";
	   }
  }

//
//-> DEL <---------------------------------------------------------->

?>

