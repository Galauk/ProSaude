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
	    <legend>Listando Tipo de Laudos Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <td width='50'>Código</td>
		   <td width='*'>Categoria de Laudo</td>
		   <td width='*'>Tipo de Laudo</td>
		   <td colspan=2 width='65' align='center'>&nbsp;</td>
		  </tr>";
if($action=="busca") {
    $palavra_chave = strtoupper($palavra_chave);
    $query = pg_query("select *from tipodelaudos where tpldo_tipodelaudo like '%$palavra_chave%'");
} else {	   
    $query = pg_query("select *from tipodelaudos");
}
	   while($row=pg_fetch_array($query)) {
	   $tp = pg_fetch_array(pg_query("select *from categoriadelaudos where cldo_codigo = $row[cldo_codigo]"));
	   echo "<tr>
			   <td width='100' align='center'>$row[tpldo_codigo]</td>
			   <td width='50%'>$tp[cldo_categoria]</td>
			   <td width='50%'>$row[tpldo_tipodelaudo]</td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=form_edit&tpldo_codigo=$row[tpldo_codigo]&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
			   <td width='65' align='center'>
			   <a href='$PHP_SELF?acao=del&tpldo_codigo=$row[tpldo_codigo]&id_login=$id_login' onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
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
     $row = pg_fetch_array(pg_query("select *from tipodelaudos where tpldo_codigo = '$tpldo_codigo'"));
      echo "<input type=hidden name=acao value=edit>";
      echo "<input type=hidden name=tpldo_codigo value=$tpldo_codigo>";
      $titulo = "Editar";
      $Btn = "editar";
      if(trim($row[txa_subdivisoes])=="S") {
	 $vse_1 = "selected";
 	 $vse_2 = "";
      } else {
	 $vse_1 = "";
 	 $vse_2 = "selected";
      }

  }

  echo "<fieldset>
	    <legend>$titulo de Tipo de Laudos </legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
		<td align=right>Categoria de Laudos:</td>
		<td><select name=cldo_codigo class=box>";
   echo "<option value=''>..:: Selecione o Categoria do Laudo ::..</option>";
  $sql = pg_query("select *from categoriadelaudos");
 while($rw=pg_fetch_array($sql)) {
   echo ($rw[cldo_codigo]==$row[cldo_codigo])?"<option value='$rw[cldo_codigo]' selected>$rw[cldo_categoria]</option>":"<option value='$rw[cldo_codigo]'>$rw[cldo_categoria]</option>";
 }
  echo "</select></td>
	      </tr>
	      <tr>
		<td align=right>Tipo de Laudo:</td>
		<td><input type=text name=tpldo_tipodelaudo size=50 class=box value='$row[tpldo_tipodelaudo]'></td>
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
	$sql = "INSERT INTO tipodelaudos ( " .
			"cldo_codigo, " .
			"tpldo_tipodelaudo ".
               ") VALUES ( " .
			"'$cldo_codigo'".
			",'$tpldo_tipodelaudo' ) ";
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
	
 }

if($acao=="edit") {
	$sql = 	"UPDATE tipodelaudos SET " .
         	"cldo_codigo='$cldo_codigo' ". 
         	", tpldo_tipodelaudo='$tpldo_tipodelaudo' ". 
         	"where tpldo_codigo='$tpldo_codigo'" ;
	$query = pg_query($sql);
	msg($id_login,$acao,$query);
}

if($acao=="del") {
	$sql = 	"DELETE FROM tipodelaudos " .
         	"where tpldo_codigo='$tpldo_codigo'" ;
			
	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

?>
