<?

/**
	@Modulo: EXAME
	@Tabelas: tipodemetodo
	@Acao: Adiciona os típos de Metodos de Exame
*/ 

/*
 @
 @ INCLUDES
 @
*/	

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

/*
 @
 @ BOTOES DE ADICIONAR E BUSCA
 @
*/	

 if(empty($acao)) {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	    <a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
   	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>
		  <input type=hidden name=action value=busca>
		<td width=30>Buscar:</td>
		<td width=120><input type='text' name=palavra_chave class=box /></td>
		<td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td>
	       </form>
	      </tr>
	     </table>
       </fieldset>
      <br>";

/*
 @
 @ LISTANDO DADOS DA SQL
 @
*/	
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Tipo de Metodos Cadastradas</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</th>
		   <td width='*'>Metodo</th>
		   <td width='*'>Observacao</th>
		   <td colspan=2 width='65' align='center'>&nbsp;</th>
		  </tr>";
if($action=="busca") {
	   $query = pg_query("SELECT *from tipodemetodos where tpm_metodo like '%$palavra_chave%'"); 
} else {	   
	   $query = pg_query("SELECT *from tipodemetodos limit 12") or die(pg_last_error()); 
}
	   while($row=pg_fetch_array($query)) {

	   echo "<tr>
			   <td width='50' align='center'>$row[tpm_codigo]</td>
			   <td width='*'>$row[tpm_metodo]</td>
			   <td width='*'>$row[tpm_observacao]</td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=form_edit&tpm_codigo=$row[tpm_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=del&tpm_codigo=$row[tpm_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}


/*
 @
 @ FORMULARIO DE ADICAO E EDICAO
 @
*/	

 if(($acao=="form_add" OR $acao=="form_edit")) {


  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=".$_SERVER["SCRIPT_NAME"]."?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    </fieldset>
	  <br>";


  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>";

  if($acao=="form_add") {
      echo "<input type=hidden name=acao value=add>";
      $titulo = "Cadastro";
      $Btn = "adicionar";
  } else {
     $row = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = '$tpm_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=tpm_codigo value=$tpm_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
  }

  echo "<fieldset>
	    <legend>$titulo de Tipo de Metodos </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td>Metodo:</td>
		<td><input type='text' name='tpm_metodo' class='box' size='69' value='$row[tpm_metodo]'></td>
	      </tr>
	      <tr>
		<td width=70>Observa&ccedil;&atilde;o: </td>
		<td><textarea name='tpm_observacao' class='box' cols='66' rows='3'>$row[tpm_observacao]</textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$Btn."_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	   <br />
	  </form>";

}


/*
 @
 @ SQLs PARA INSERCAO, EDICAO E EXCLUSAO
 @
*/	

 if($acao=="add") {
	$sql = "INSERT INTO tipodemetodos ( " .
			"tpm_metodo, " .
			"tpm_observacao ".
               ") VALUES ( " .
			"'$tpm_metodo'".
			",'$tpm_observacao' ) ";

	$query = pg_query($sql);
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE tipodemetodos SET " .
         	"tpm_metodo='$tpm_metodo' ". 
         	", tpm_observacao='$tpm_observacao' ". 
         	"where tpm_codigo='$tpm_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM tipodemetodos " .
         	"where tpm_codigo='$tpm_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
