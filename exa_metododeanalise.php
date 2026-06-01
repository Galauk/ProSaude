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
	    <legend>Listando Metodo de Analise Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>Metodo</td>
		   <td width='*'>Observacao</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $query = pg_query("select a.man_codigo,b.tpm_metodo,b.tpm_codigo from metododeanalize as a left join tipodemetodo as b on a.tpm_codigo = b.tpm_codigo where b.tpm_metodo like '%$palavra chave%'");
} else {	   
    $query = pg_query("select *from metododeanalize");
}
	   while($row=pg_fetch_array($query)) {
	   $tp = pg_fetch_array(pg_query("select *from tipodemetodos where tpm_codigo = '$row[tpm_codigo]'"));
	   echo "<tr>
			   <td width='100' align='center'>$row[man_codigo]</td>
			   <td width='50%'>$tp[tpm_metodo]</td>
			   <td width='100%'>$row[man_observacao]</td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=form_edit&man_codigo=$row[man_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=del&man_codigo=$row[man_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
     $row = pg_fetch_array(pg_query("select *from metododeanalize where man_codigo = '$man_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=man_codigo value=$man_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
  }

  echo "<fieldset>
	    <legend>$titulo de Tipo de Metodo </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Tipo de Metodo:</td>
		<td><select name=tpm_codigo class=box>";
   echo "<option value=''>..:: Selecione o Tipo de Metodo ::..</option>";
  $sql = pg_query("select *from tipodemetodos");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[tpm_codigo]==$row[tpm_codigo])?"<option value='$rw[tpm_codigo]' selected>$rw[tpm_metodo]</option>":"<option value='$rw[tpm_codigo]'>$rw[tpm_metodo]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td>Observacao: </td>
	      </tr>
	      <tr>
		<td>&nbsp;</td>
		<td><textarea name='man_observacao' class='box' cols='66' rows='3'>$row[man_observacao]</textarea></td>
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
	$sql = "INSERT INTO metododeanalize ( " .
			"tpm_codigo, " .
			"man_observacao ".
               ") VALUES ( " .
			"'$tpm_codigo'".
			",'$man_observacao' ) ";
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE metododeanalize SET " .
         	"tpm_codigo='$tpm_codigo' ". 
         	", man_observacao='$man_observacao' ". 
         	"where man_codigo='$man_codigo'" ;
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM metododeanalize " .
         	"where man_codigo='$man_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
